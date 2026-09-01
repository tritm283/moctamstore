<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model
{
    protected $table='order_items'; protected $primaryKey='order_item_id';
    protected $fillable=['order_id','product_id','product_name','quantity','price'];
    protected function casts(): array { return ['price'=>'decimal:2','quantity'=>'integer']; }
}
