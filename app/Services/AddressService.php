<?php
namespace App\Services;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
class AddressService
{
    public function create(User $user,array $data): UserAddress
    {
        return DB::transaction(function() use($user,$data){
            $hasAny=UserAddress::where('user_id',$user->user_id)->exists();
            $makeDefault=(bool)($data['is_default']??false) || !$hasAny;
            if ($makeDefault) UserAddress::where('user_id',$user->user_id)->update(['is_default'=>false]);
            return UserAddress::create([...$data,'user_id'=>$user->user_id,'is_default'=>$makeDefault]);
        });
    }
    public function update(User $user,UserAddress $address,array $data): UserAddress
    {
        abort_unless($address->user_id===$user->user_id,404);
        return DB::transaction(function() use($user,$address,$data){
            if (($data['is_default']??false)===true) UserAddress::where('user_id',$user->user_id)->where('address_id','!=',$address->address_id)->update(['is_default'=>false]);
            $address->update($data); return $address->refresh();
        });
    }
    public function setDefault(User $user,UserAddress $address): UserAddress
    {
        abort_unless($address->user_id===$user->user_id,404);
        return DB::transaction(function() use($user,$address){
            UserAddress::where('user_id',$user->user_id)->update(['is_default'=>false]);
            $address->update(['is_default'=>true]); return $address->refresh();
        });
    }
}
