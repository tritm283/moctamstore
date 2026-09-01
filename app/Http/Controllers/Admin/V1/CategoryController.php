<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
    private function rules(?Category $m=null): array { $id=$m?->category_id; return ['name'=>['required','string','max:190'],'slug'=>['required','string','max:190','unique:categories,slug'.($id?",{$id},category_id":'')],'description'=>['nullable','string'],'parent_id'=>['nullable','integer','exists:categories,category_id'],'is_active'=>['sometimes','boolean'],'position'=>['sometimes','integer','min:0']]; }
    public function index(){ return $this->ok(Category::orderBy('position')->paginate(50)); }
    public function store(Request $r){ return $this->ok(Category::create($r->validate($this->rules())),'Đã tạo danh mục.',201); }
    public function show(Category $category){ return $this->ok($category); }
    public function update(Request $r,Category $category){ $category->update($r->validate($this->rules($category))); return $this->ok($category->refresh(),'Đã cập nhật.'); }
    public function destroy(Category $category){ $category->delete(); return $this->ok(null,'Đã xóa.'); }
}
