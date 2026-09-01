<?php
namespace App\Http\Controllers\Admin\V1;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function login(LoginRequest $r){ $u=User::where('email',$r->email)->first(); if(!$u||!$u->password||!Hash::check($r->password,$u->password)||$u->role!==UserRole::ADMIN) throw ValidationException::withMessages(['email'=>'Thông tin quản trị không hợp lệ.']); if(!$u->is_active) abort(403,'Tài khoản đã bị khóa.'); $t=$u->createToken($r->input('device_name','admin-web'),['admin'])->plainTextToken; return $this->ok(['user'=>$u,'token'=>$t],'Đăng nhập quản trị thành công.'); }
    public function me(Request $r){ return $this->ok($r->user()->load('avatar')); }
    public function logout(Request $r){ $r->user()->currentAccessToken()?->delete(); return $this->ok(null,'Đã đăng xuất.'); }
}
