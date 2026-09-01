<?php
namespace App\Models;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Order extends Model
{
    protected $primaryKey='order_id';
    protected $fillable=['user_id','status','total_amount','shipping_address','note'];
    protected function casts(): array { return ['status'=>OrderStatus::class,'total_amount'=>'decimal:2','shipping_address'=>'array']; }
    public function items(): HasMany { return $this->hasMany(OrderItem::class,'order_id','order_id'); }
    public function payment(): HasOne { return $this->hasOne(Payment::class,'order_id','order_id'); }
}
