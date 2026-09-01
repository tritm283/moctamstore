<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserSocialAccount extends Model
{
    protected $table='user_social_accounts'; protected $primaryKey='social_account_id';
    protected $fillable=['user_id','provider','provider_user_id'];
}
