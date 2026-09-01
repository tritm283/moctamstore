<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
class CartController extends Controller
{
    public function show(Request $r,CartService $s){ return $this->ok($s->get($r->user())); }
    public function add(Request $r,CartService $s){ $d=$r->validate(['product_id'=>['required','integer','exists:products,product_id'],'quantity'=>['required','integer','min:1']]); return $this->ok($s->add($r->user(),$d['product_id'],$d['quantity']),'Đã thêm vào giỏ.'); }
    public function update(Request $r,CartItem $item,CartService $s){ $d=$r->validate(['quantity'=>['required','integer','min:0']]); return $this->ok($s->update($r->user(),$item,$d['quantity']),'Đã cập nhật giỏ.'); }
    public function destroy(Request $r,CartItem $item,CartService $s){ return $this->ok($s->update($r->user(),$item,0),'Đã xóa khỏi giỏ.'); }
}
