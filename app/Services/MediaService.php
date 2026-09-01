<?php
namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MediaService
{
    public function __construct(private GoogleDriveService $drive) {}

    public function create(UploadedFile $file): Media
    {
        $payload = $this->drive->uploadPublic($file);
        try {
            return Media::create($payload);
        } catch (\Throwable $e) {
            $this->drive->delete($payload['drive_file_id']);
            throw $e;
        }
    }

    public function delete(Media $media): void
    {
        $refs = [
            'users.avatar_id' => DB::table('users')->where('avatar_id', $media->media_id)->exists(),
            'products.media_id' => DB::table('products')->where('media_id', $media->media_id)->exists(),
            'articles.thumbnail_id' => DB::table('articles')->where('thumbnail_id', $media->media_id)->exists(),
        ];
        $usedBy = array_keys(array_filter($refs));
        if ($usedBy) {
            throw ValidationException::withMessages(['media' => 'Media đang được sử dụng bởi: '.implode(', ', $usedBy)]);
        }

        $fileId = $media->drive_file_id;
        $media->delete();
        $this->drive->delete($fileId);
    }
}
