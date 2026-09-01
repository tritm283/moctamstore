<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index(Request $r){$q=User::with('avatar');if($r->filled('q'))$q->where(fn($x)=>$x->where('email','ilike','%'.$r->q.'%')->orWhere('full_name','ilike','%'.$r->q.'%')->orWhere('phone','ilike','%'.$r->q.'%'));return $this->ok($q->orderByDesc('user_id')->paginate(30));}
    public function show(User $user){return $this->ok($user->load(['avatar','addresses','socialAccounts']));}
    public function update(Request $r,User $user){$d=$r->validate(['is_active'=>['sometimes','boolean'],'is_marketing_allowed'=>['sometimes','boolean']]);$user->update($d);return $this->ok($user->refresh(),'Đã cập nhật user.');}
}
