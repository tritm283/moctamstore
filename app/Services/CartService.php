<?php
namespace App\Services;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;
class CartService
{
    public function get(User $user): Cart
    {
        $cart=Cart::firstOrCreate(['user_id'=>$user->user_id]);
        return $cart->load(['items.product.media','items.product.category']);
    }
    public function add(User $user,int $productId,int $quantity): Cart
    {
        $product=Product::where('product_id',$productId)->where('is_active',true)->firstOrFail();
        if ($quantity<1) throw ValidationException::withMessages(['quantity'=>'Số lượng phải >= 1.']);
        $cart=Cart::firstOrCreate(['user_id'=>$user->user_id]);
        $item=CartItem::firstOrNew(['cart_id'=>$cart->cart_id,'product_id'=>$productId]);
        $newQty=($item->exists?$item->quantity:0)+$quantity;
        if ($newQty>$product->stock_quantity) throw ValidationException::withMessages(['quantity'=>'Số lượng vượt quá tồn kho hiện tại.']);
        $item->quantity=$newQty; $item->save(); return $this->get($user);
    }
    public function update(User $user,CartItem $item,int $quantity): Cart
    {
        $cart=Cart::where('user_id',$user->user_id)->firstOrFail(); abort_unless($item->cart_id===$cart->cart_id,404);
        if ($quantity<=0) $item->delete(); else { $product=Product::findOrFail($item->product_id); if ($quantity>$product->stock_quantity) throw ValidationException::withMessages(['quantity'=>'Số lượng vượt quá tồn kho hiện tại.']); $item->update(['quantity'=>$quantity]); }
        return $this->get($user);
    }
}
