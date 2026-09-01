<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
class CatalogController extends Controller
{
    public function categories(){ return $this->ok(Category::where('is_active',true)->orderBy('position')->get()); }
    public function products(Request $r){ $q=Product::with(['media','category'])->where('is_active',true); if($r->filled('category_id'))$q->where('category_id',$r->integer('category_id')); if($r->filled('q'))$q->where(fn($x)=>$x->where('name','ilike','%'.$r->q.'%')->orWhere('sku','ilike','%'.$r->q.'%')); return $this->ok($q->orderByDesc('product_id')->paginate(min($r->integer('per_page',20),100))); }
    public function product(string $idOrSlug){ $p=Product::with(['media','category'])->where('is_active',true)->where(fn($q)=>$q->where('product_id',ctype_digit($idOrSlug)?(int)$idOrSlug:-1)->orWhere('slug',$idOrSlug))->firstOrFail(); return $this->ok($p); }
    public function articleCategories(){ return $this->ok(ArticleCategory::where('is_active',true)->orderBy('name')->get()); }
    public function articles(Request $r){ $q=Article::with(['thumbnail','category'])->where('status','published')->where(fn($q)=>$q->whereNull('published_at')->orWhere('published_at','<=',now())); if($r->filled('category_id'))$q->where('article_category_id',$r->integer('category_id')); return $this->ok($q->orderByDesc('published_at')->paginate(min($r->integer('per_page',20),100))); }
    public function article(string $slug){ return $this->ok(Article::with(['thumbnail','category'])->where('slug',$slug)->where('status','published')->firstOrFail()); }
}
