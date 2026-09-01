<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
class ArticleCategoryController extends Controller
{
    private function rules(?ArticleCategory $m=null):array{$id=$m?->article_category_id;return ['name'=>['required','string','max:190'],'slug'=>['required','string','max:190','unique:article_categories,slug'.($id?",{$id},article_category_id":'')],'description'=>['nullable','string'],'is_active'=>['sometimes','boolean']];}
    public function index(){return $this->ok(ArticleCategory::orderBy('name')->paginate(50));}
    public function store(Request $r){return $this->ok(ArticleCategory::create($r->validate($this->rules())),'Đã tạo chuyên mục.',201);}
    public function show(ArticleCategory $articleCategory){return $this->ok($articleCategory);}
    public function update(Request $r,ArticleCategory $articleCategory){$articleCategory->update($r->validate($this->rules($articleCategory)));return $this->ok($articleCategory->refresh(),'Đã cập nhật.');}
    public function destroy(ArticleCategory $articleCategory){$articleCategory->delete();return $this->ok(null,'Đã xóa.');}
}
