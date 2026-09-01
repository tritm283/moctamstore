<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;
class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['full_name'=>['required','string','max:120'],'email'=>['required','email','max:190','unique:users,email'],'phone'=>['nullable','string','max:30','unique:users,phone'],'password'=>['required','string','min:8','max:72','confirmed'],'is_marketing_allowed'=>['sometimes','boolean']]; }
}
