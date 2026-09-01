<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ArticleCategory extends Model
{
    protected $table='article_categories'; protected $primaryKey='article_category_id';
    protected $fillable=['name','slug','description','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
}
