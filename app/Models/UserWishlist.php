<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserWishlist extends Model
{
    protected $table='user_wishlists'; protected $primaryKey='wishlist_id';
    protected $fillable=['user_id','product_id'];
}
