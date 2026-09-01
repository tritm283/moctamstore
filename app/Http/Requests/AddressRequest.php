<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AddressRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['receiver_name'=>['required','string','max:120'],'receiver_phone'=>['required','string','max:30'],'province_city'=>['required','string','max:120'],'district'=>['required','string','max:120'],'ward_commune'=>['required','string','max:120'],'detailed_address'=>['required','string','max:500'],'is_default'=>['sometimes','boolean']]; }
}
