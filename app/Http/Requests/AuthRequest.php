<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_name' => 'required|unique:users',
            'password' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'user_name.required' => 'يجب ادخال أسم المستخدم ',
            'user_name.unique' => 'أسم المستخدم الذي قمت بأدخاله مستخدم سابقاً',
            'password.required' => 'يجب ادخال كلمة المرور '
        ];
    }
}
