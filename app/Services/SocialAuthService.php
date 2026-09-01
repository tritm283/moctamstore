<?php
namespace App\Services;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserSocialAccount;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
class SocialAuthService
{
    public function authenticate(string $provider,string $token): User
    {
        $profile=match($provider){'google'=>$this->google($token),'facebook'=>$this->facebook($token),default=>throw new RuntimeException('Provider không hỗ trợ.')};
        return DB::transaction(function() use($provider,$profile){
            $social=UserSocialAccount::where('provider',$provider)->where('provider_user_id',$profile['provider_user_id'])->lockForUpdate()->first();
            if ($social) return User::findOrFail($social->user_id);
            $user=null;
            if (!empty($profile['email'])) $user=User::where('email',$profile['email'])->lockForUpdate()->first();
            if (!$user) {
                if (empty($profile['email'])) throw new RuntimeException('Mạng xã hội không trả về email; không thể tự tạo tài khoản an toàn.');
                $user=User::create(['email'=>$profile['email'],'full_name'=>$profile['name'] ?: 'Khách hàng','password'=>null,'role'=>UserRole::CUSTOMER,'is_active'=>true,'is_marketing_allowed'=>false,'email_verified_at'=>now()]);
            }
            UserSocialAccount::create(['user_id'=>$user->user_id,'provider'=>$provider,'provider_user_id'=>$profile['provider_user_id']]);
            return $user;
        });
    }
    private function google(string $token): array
    {
        $client=new GoogleClient(['client_id'=>config('services.google.client_id')]);
        $payload=$client->verifyIdToken($token);
        if (!$payload || empty($payload['sub'])) throw new RuntimeException('Google token không hợp lệ.');
        return ['provider_user_id'=>(string)$payload['sub'],'email'=>$payload['email']??null,'name'=>$payload['name']??null];
    }
    private function facebook(string $token): array
    {
        $version=config('services.facebook.graph_version','v23.0');
        $appToken=config('services.facebook.app_id').'|'.config('services.facebook.app_secret');
        $debug=Http::timeout(10)->get("https://graph.facebook.com/{$version}/debug_token",['input_token'=>$token,'access_token'=>$appToken])->throw()->json('data');
        if (!($debug['is_valid']??false) || (string)($debug['app_id']??'') !== (string) config('services.facebook.app_id')) throw new RuntimeException('Facebook token không hợp lệ.');
        $me=Http::timeout(10)->get("https://graph.facebook.com/{$version}/me",['fields'=>'id,name,email','access_token'=>$token])->throw()->json();
        if (empty($me['id'])) throw new RuntimeException('Không đọc được Facebook user id.');
        return ['provider_user_id'=>(string)$me['id'],'email'=>$me['email']??null,'name'=>$me['name']??null];
    }
}
