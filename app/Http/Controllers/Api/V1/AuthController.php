<?php
namespace App\Http\Controllers\Api\V1;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Models\Cart;
use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function register(RegisterRequest $request){ $user=User::create([...$request->validated(),'role'=>UserRole::CUSTOMER,'is_active'=>true]); Cart::create(['user_id'=>$user->user_id]); $token=$user->createToken($request->input('device_name','web'),['customer'])->plainTextToken; return $this->ok(['user'=>$user->load('avatar'),'token'=>$token],'Đăng ký thành công.',201); }
    public function login(LoginRequest $request){ $user=User::where('email',$request->email)->first(); if(!$user||!$user->password||!Hash::check($request->password,$user->password)) throw ValidationException::withMessages(['email'=>'Email hoặc mật khẩu không đúng.']); if(!$user->is_active) abort(403,'Tài khoản đã bị khóa.'); $token=$user->createToken($request->input('device_name','web'),['customer'])->plainTextToken; return $this->ok(['user'=>$user->load('avatar'),'token'=>$token],'Đăng nhập thành công.'); }
    public function social(SocialLoginRequest $request,SocialAuthService $service){ $user=$service->authenticate($request->provider,$request->token); if(!$user->is_active) abort(403,'Tài khoản đã bị khóa.'); Cart::firstOrCreate(['user_id'=>$user->user_id]); $token=$user->createToken($request->input('device_name','social-web'),['customer'])->plainTextToken; return $this->ok(['user'=>$user->load('avatar'),'token'=>$token],'Đăng nhập mạng xã hội thành công.'); }
    public function me(Request $request){ return $this->ok($request->user()->load('avatar')); }
    public function logout(Request $request){ $request->user()->currentAccessToken()?->delete(); return $this->ok(null,'Đã đăng xuất.'); }
}
