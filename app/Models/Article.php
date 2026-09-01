<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Article extends Model
{
    protected $primaryKey='article_id';
    protected $fillable=['article_category_id','thumbnail_id','title','slug','excerpt','content','status','published_at'];
    protected function casts(): array { return ['published_at'=>'datetime']; }
    public function category(): BelongsTo { return $this->belongsTo(ArticleCategory::class,'article_category_id','article_category_id'); }
    public function thumbnail(): BelongsTo { return $this->belongsTo(Media::class,'thumbnail_id','media_id'); }
}
