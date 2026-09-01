<?php
namespace App\Services;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CheckoutService
{
    public function checkout(User $user,int $addressId,string $paymentMethod,?string $note): Order
    {
        return DB::transaction(function() use($user,$addressId,$paymentMethod,$note){
            $address=UserAddress::where('address_id',$addressId)->where('user_id',$user->user_id)->firstOrFail();
            $cart=Cart::where('user_id',$user->user_id)->lockForUpdate()->first();
            if (!$cart) throw ValidationException::withMessages(['cart'=>'Giỏ hàng đang trống.']);
            $items=CartItem::where('cart_id',$cart->cart_id)->lockForUpdate()->get();
            if ($items->isEmpty()) throw ValidationException::withMessages(['cart'=>'Giỏ hàng đang trống.']);
            $order=Order::create(['user_id'=>$user->user_id,'status'=>OrderStatus::PENDING,'total_amount'=>0,'shipping_address'=>['receiver_name'=>$address->receiver_name,'receiver_phone'=>$address->receiver_phone,'province_city'=>$address->province_city,'district'=>$address->district,'ward_commune'=>$address->ward_commune,'detailed_address'=>$address->detailed_address],'note'=>$note]);
            $total='0.00';
            foreach($items as $item){
                $product=Product::where('product_id',$item->product_id)->lockForUpdate()->firstOrFail();
                if (!$product->is_active) throw ValidationException::withMessages(['cart'=>"Sản phẩm {$product->name} hiện không bán."]);
                if ($product->stock_quantity<$item->quantity) throw ValidationException::withMessages(['cart'=>"Sản phẩm {$product->name} chỉ còn {$product->stock_quantity}."]);
                $price=(string)$product->price;
                OrderItem::create(['order_id'=>$order->order_id,'product_id'=>$product->product_id,'product_name'=>$product->name,'quantity'=>$item->quantity,'price'=>$price]);
                $line=bcmul($price,(string)$item->quantity,2); $total=bcadd($total,$line,2);
                $product->decrement('stock_quantity',$item->quantity);
            }
            $order->update(['total_amount'=>$total]);
            Payment::create(['order_id'=>$order->order_id,'method'=>$paymentMethod,'status'=>PaymentStatus::PENDING,'amount'=>$total]);
            CartItem::where('cart_id',$cart->cart_id)->delete();
            return $order->load(['items','payment']);
        },3);
    }
}
