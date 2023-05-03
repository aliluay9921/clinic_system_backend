<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
{
    use SendResponse;
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
        if ($this->method() == "POST") {
            return [
                'user_name' => 'required|unique:users,user_name',
                'password' => 'required',
                'phone_number' => 'required|unique:users,phone_number',
                'user_type' => 'required',
                "clinic_id" => 'required|exists:clinics,id'
            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:users",
                'user_name' => 'required|unique:users,user_name,' . $this->id,
                'password' => 'required',
                'phone_number' => 'required|unique:users,phone_number,' . $this->id,
                'user_type' => 'required',
                "clinic_id" => 'required|exists:clinics,id'
            ];
        } else {
            return [
                "id" => "required|exists:users",
            ];
        }
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->send_response(400, 'خطأ في المدخلات', $validator->errors()));
    }
    public function messages()
    {
        return [
            'id.required' => 'يجب أدخال الأسم',
            'id.exists' => 'هذا المستخدم غير متوفر',
            'user_name.required' => 'يجب أدخال أسم المستخدم',
            'user_name.unique' => 'هذا الأسم موجود مسبقاً',
            'password.required' => 'يجب أدخال كلمة المرور',
            'phone_number.required' => 'يجب أدخال رقم الهاتف',
            'phone_number.unique' => 'هذا الرقم موجود مسبقاً',
            'user_type.required' => 'يجب أدخال نوع المستخدم',
            'clinic_id.required' => 'يجب أدخال العيادة',
            'clinic_id.exists' => 'هذه العيادة غير متوفرة'
        ];
    }
}
