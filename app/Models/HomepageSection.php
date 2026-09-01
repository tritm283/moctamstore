<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HomepageSection extends Model
{
    protected $table='homepage_sections'; protected $primaryKey='section_id';
    protected $fillable=['title','type','position','is_visible','settings'];
    protected function casts(): array { return ['position'=>'integer','is_visible'=>'boolean','settings'=>'array']; }
}
