<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaUploadRequest;
use App\Models\Product;
use App\Services\MediaService;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    private function rules(?Product $m=null): array { $id=$m?->product_id; return ['category_id'=>['required','integer','exists:categories,category_id'],'media_id'=>['nullable','integer','exists:media,media_id'],'name'=>['required','string','max:255'],'slug'=>['required','string','max:255','unique:products,slug'.($id?",{$id},product_id":'')],'sku'=>['required','string','max:100','unique:products,sku'.($id?",{$id},product_id":'')],'short_description'=>['nullable','string'],'description'=>['nullable','string'],'price'=>['required','numeric','min:0'],'stock_quantity'=>['required','integer','min:0'],'is_active'=>['sometimes','boolean']]; }
    public function index(Request $r){ $q=Product::with(['media','category']); if($r->filled('q'))$q->where('name','ilike','%'.$r->q.'%'); return $this->ok($q->orderByDesc('product_id')->paginate(30)); }
    public function store(Request $r){ return $this->ok(Product::create($r->validate($this->rules())),'Đã tạo sản phẩm.',201); }
    public function show(Product $product){ return $this->ok($product->load(['media','category'])); }
    public function update(Request $r,Product $product){ $product->update($r->validate($this->rules($product))); return $this->ok($product->refresh()->load(['media','category']),'Đã cập nhật sản phẩm.'); }
    public function destroy(Product $product){ $product->delete(); return $this->ok(null,'Đã xóa sản phẩm.'); }
    public function image(MediaUploadRequest $r,Product $product,MediaService $s){ $media=$s->create($r->file('file')); $old=$product->media; $product->update(['media_id'=>$media->media_id]); if($old){try{$s->delete($old);}catch(\Throwable){}} return $this->ok($product->refresh()->load('media'),'Đã cập nhật ảnh sản phẩm.'); }
}
