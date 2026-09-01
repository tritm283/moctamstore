<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaUploadRequest;
use App\Models\Article;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ArticleController extends Controller
{
    private function rules(?Article $m=null):array{$id=$m?->article_id;return ['article_category_id'=>['required','integer','exists:article_categories,article_category_id'],'thumbnail_id'=>['nullable','integer','exists:media,media_id'],'title'=>['required','string','max:255'],'slug'=>['required','string','max:255','unique:articles,slug'.($id?",{$id},article_id":'')],'excerpt'=>['nullable','string'],'content'=>['required','string'],'status'=>['required',Rule::in(['draft','published','archived'])],'published_at'=>['nullable','date']];}
    public function index(){return $this->ok(Article::with(['thumbnail','category'])->orderByDesc('article_id')->paginate(30));}
    public function store(Request $r){return $this->ok(Article::create($r->validate($this->rules())),'Đã tạo bài viết.',201);}
    public function show(Article $article){return $this->ok($article->load(['thumbnail','category']));}
    public function update(Request $r,Article $article){$article->update($r->validate($this->rules($article)));return $this->ok($article->refresh()->load(['thumbnail','category']),'Đã cập nhật bài viết.');}
    public function destroy(Article $article){$article->delete();return $this->ok(null,'Đã xóa bài viết.');}
    public function thumbnail(MediaUploadRequest $r,Article $article,MediaService $s){$media=$s->create($r->file('file'));$old=$article->thumbnail;$article->update(['thumbnail_id'=>$media->media_id]);if($old){try{$s->delete($old);}catch(\Throwable){}}return $this->ok($article->refresh()->load('thumbnail'),'Đã cập nhật thumbnail.');}
}
