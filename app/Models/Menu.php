<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    protected $fillable=['title','parent_id','url','position','is_visible','category_id','product_id','article_category_id','article_id'];
    protected $primaryKey='menu_id';
    protected function casts(): array { return ['position'=>'integer','is_visible'=>'boolean']; }
}
