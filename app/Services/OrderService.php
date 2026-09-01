<?php
namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    private const ALLOWED = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipping', 'cancelled'],
        'shipping' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function changeStatus(Order $order, OrderStatus $next): Order
    {
        return DB::transaction(function () use ($order, $next) {
            /** @var Order $locked */
            $locked = Order::where('order_id', $order->order_id)->lockForUpdate()->firstOrFail();
            $current = $locked->status->value;

            if ($current === $next->value) {
                return $locked->load(['items', 'payment']);
            }

            if (!in_array($next->value, self::ALLOWED[$current] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => "Không thể chuyển đơn từ {$current} sang {$next->value}.",
                ]);
            }

            if ($next === OrderStatus::CANCELLED) {
                $items = $locked->items()->lockForUpdate()->get();
                foreach ($items as $item) {
                    $product = Product::where('product_id', $item->product_id)->lockForUpdate()->firstOrFail();
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            $locked->update(['status' => $next]);
            return $locked->refresh()->load(['items', 'payment']);
        }, 3);
    }
}
