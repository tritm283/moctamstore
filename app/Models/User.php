<?php
namespace App\Models;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $primaryKey = 'user_id';
    protected $fillable = ['email','phone','full_name','password','avatar_id','role','is_active','is_marketing_allowed','email_verified_at'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['password'=>'hashed','role'=>UserRole::class,'is_active'=>'boolean','is_marketing_allowed'=>'boolean','email_verified_at'=>'datetime']; }
    public function avatar(): BelongsTo { return $this->belongsTo(Media::class, 'avatar_id', 'media_id'); }
    public function socialAccounts(): HasMany { return $this->hasMany(UserSocialAccount::class, 'user_id', 'user_id'); }
    public function addresses(): HasMany { return $this->hasMany(UserAddress::class, 'user_id', 'user_id'); }
    public function cart(): HasOne { return $this->hasOne(Cart::class, 'user_id', 'user_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class, 'user_id', 'user_id'); }
}
