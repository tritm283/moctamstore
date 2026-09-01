<?php
namespace Tests\Feature;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CheckoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_checkout_freezes_price_creates_payment_decrements_stock_and_clears_cart(): void
    {
        $user=User::create(['email'=>'buyer@example.com','full_name'=>'Buyer','password'=>'secret123','is_active'=>true]);
        $address=UserAddress::create(['user_id'=>$user->user_id,'receiver_name'=>'Buyer','receiver_phone'=>'0900000000','province_city'=>'Ha Noi','district'=>'Ba Dinh','ward_commune'=>'Doi Can','detailed_address'=>'1 Test','is_default'=>true]);
        $category=Category::create(['name'=>'Test','slug'=>'test','position'=>0]);
        $product=Product::create(['category_id'=>$category->category_id,'name'=>'P1','slug'=>'p1','sku'=>'P1','price'=>'125000.00','stock_quantity'=>10,'is_active'=>true]);
        $cart=Cart::create(['user_id'=>$user->user_id]);
        CartItem::create(['cart_id'=>$cart->cart_id,'product_id'=>$product->product_id,'quantity'=>2]);
        $order=app(CheckoutService::class)->checkout($user,$address->address_id,'COD',null);
        $this->assertSame('250000.00',(string)$order->total_amount);
        $this->assertSame('125000.00',(string)$order->items->first()->price);
        $this->assertSame(8,$product->refresh()->stock_quantity);
        $this->assertDatabaseCount('cart_items',0);
        $this->assertDatabaseHas('payments',['order_id'=>$order->order_id,'status'=>'pending']);
    }
}
