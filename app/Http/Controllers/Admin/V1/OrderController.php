<?php
namespace App\Http\Controllers\Admin\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $q = Order::with('payment');
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($r->filled('user_id')) $q->where('user_id', $r->integer('user_id'));
        return $this->ok($q->orderByDesc('order_id')->paginate(30));
    }

    public function show(Order $order)
    {
        return $this->ok($order->load(['items', 'payment']));
    }

    public function status(Request $r, Order $order, OrderService $service)
    {
        $d = $r->validate(['status' => ['required', Rule::enum(OrderStatus::class)]]);
        return $this->ok(
            $service->changeStatus($order, OrderStatus::from($d['status'])),
            'Đã cập nhật trạng thái đơn.'
        );
    }
}
