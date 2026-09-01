<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Media extends Model
{
    protected $table='media'; protected $primaryKey='media_id';
    protected $fillable=['drive_file_id','drive_view_url','file_name','mime_type','size_bytes'];
}
