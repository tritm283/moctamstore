<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaUploadRequest;
use App\Services\MediaService;
use Illuminate\Http\Request;
class ProfileController extends Controller
{
    public function update(Request $r){ $d=$r->validate(['full_name'=>['sometimes','string','max:120'],'phone'=>['sometimes','nullable','string','max:30','unique:users,phone,'.$r->user()->user_id.',user_id'],'is_marketing_allowed'=>['sometimes','boolean']]); $r->user()->update($d); return $this->ok($r->user()->refresh()->load('avatar'),'Đã cập nhật hồ sơ.'); }
    public function avatar(MediaUploadRequest $r,MediaService $s){ $media=$s->create($r->file('file')); $old=$r->user()->avatar; $r->user()->update(['avatar_id'=>$media->media_id]); if($old) { try{$s->delete($old);}catch(\Throwable){} } return $this->ok($media,'Đã cập nhật avatar.',201); }
}
