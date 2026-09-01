<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\UserWishlist;
use Illuminate\Http\Request;
class WishlistController extends Controller
{
    public function index(Request $r){ return $this->ok(UserWishlist::query()->join('products','products.product_id','=','user_wishlists.product_id')->leftJoin('media','media.media_id','=','products.media_id')->where('user_wishlists.user_id',$r->user()->user_id)->select('user_wishlists.*','products.name','products.slug','products.price','media.drive_view_url')->orderByDesc('wishlist_id')->get()); }
    public function toggle(Request $r){ $d=$r->validate(['product_id'=>['required','integer','exists:products,product_id']]); $q=['user_id'=>$r->user()->user_id,'product_id'=>$d['product_id']]; $existing=UserWishlist::where($q)->first(); if($existing){$existing->delete(); return $this->ok(['wishlisted'=>false]);} UserWishlist::create($q); return $this->ok(['wishlisted'=>true], 'Đã thêm yêu thích.',201); }
    public function destroy(Request $r,int $productId){ UserWishlist::where('user_id',$r->user()->user_id)->where('product_id',$productId)->delete(); return $this->ok(null,'Đã bỏ yêu thích.'); }
}
