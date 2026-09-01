<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MediaUploadRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['file'=>['required','file','image','mimes:jpg,jpeg,png,webp,gif','max:'.(int) env('MEDIA_MAX_KB',10240)]]; }
}
