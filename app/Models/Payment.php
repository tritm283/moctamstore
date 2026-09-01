<?php
namespace App\Models;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model
{
    protected $fillable=['order_id','method','status','amount','transaction_code','gateway_payload','paid_at'];
    protected $primaryKey='payment_id';
    protected function casts(): array { return ['status'=>PaymentStatus::class,'amount'=>'decimal:2','gateway_payload'=>'array','paid_at'=>'datetime']; }
}
