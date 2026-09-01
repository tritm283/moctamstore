<?php
namespace App\Services;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Illuminate\Http\UploadedFile;
use RuntimeException;
class GoogleDriveService
{
    private Drive $drive;
    public function __construct()
    {
        $path=(string) config('services.google_drive.service_account_json');
        if ($path==='' || !is_file($path)) throw new RuntimeException('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON chưa hợp lệ.');
        $client=new GoogleClient();
        $client->setAuthConfig($path);
        $client->setScopes([Drive::DRIVE_FILE]);
        $this->drive=new Drive($client);
    }
    public function uploadPublic(UploadedFile $file): array
    {
        $meta=new DriveFile(['name'=>$file->getClientOriginalName()]);
        $folder=(string) config('services.google_drive.folder_id');
        if ($folder!=='') $meta->setParents([$folder]);
        $created=$this->drive->files->create($meta,[
            'data'=>file_get_contents($file->getRealPath()), 'mimeType'=>$file->getMimeType(), 'uploadType'=>'multipart',
            'fields'=>'id,name,mimeType,size,webViewLink,webContentLink', 'supportsAllDrives'=>true,
        ]);
        $permission=new Permission(['type'=>'anyone','role'=>'reader']);
        $this->drive->permissions->create($created->getId(),$permission,['supportsAllDrives'=>true]);
        $id=$created->getId();
        return [
            'drive_file_id'=>$id,
            'drive_view_url'=>"https://drive.google.com/uc?export=view&id={$id}",
            'file_name'=>$created->getName() ?: $file->getClientOriginalName(),
            'mime_type'=>$created->getMimeType() ?: $file->getMimeType(),
            'size_bytes'=>(int) ($created->getSize() ?: $file->getSize()),
        ];
    }
    public function delete(string $fileId): void
    {
        try { $this->drive->files->delete($fileId,['supportsAllDrives'=>true]); } catch (\Throwable) { /* idempotent cleanup */ }
    }
}
