<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaUploadRequest;
use App\Models\Media;
use App\Services\MediaService;
class MediaController extends Controller
{
    public function index(){ return $this->ok(Media::orderByDesc('media_id')->paginate(30)); }
    public function store(MediaUploadRequest $r,MediaService $s){ return $this->ok($s->create($r->file('file')),'Upload thành công.',201); }
    public function destroy(Media $medium,MediaService $s){ $s->delete($medium); return $this->ok(null,'Đã xóa media.'); }
}
