<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserAddress extends Model
{
    protected $table='user_addresses'; protected $primaryKey='address_id';
    protected $fillable=['user_id','receiver_name','receiver_phone','province_city','district','ward_commune','detailed_address','is_default'];
    protected function casts(): array { return ['is_default'=>'boolean']; }
}
