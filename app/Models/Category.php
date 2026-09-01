<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $primaryKey='category_id';
    protected $fillable=['name','slug','description','parent_id','is_active','position'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
}
