<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Product extends Model
{
    protected $primaryKey='product_id';
    protected $fillable=['category_id','media_id','name','slug','sku','short_description','description','price','stock_quantity','is_active'];
    protected function casts(): array { return ['price'=>'decimal:2','stock_quantity'=>'integer','is_active'=>'boolean']; }
    public function category(): BelongsTo { return $this->belongsTo(Category::class,'category_id','category_id'); }
    public function media(): BelongsTo { return $this->belongsTo(Media::class,'media_id','media_id'); }
}
