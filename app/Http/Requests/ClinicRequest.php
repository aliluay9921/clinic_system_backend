<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ClinicRequest extends FormRequest
{
    use SendResponse;

    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        if ($this->method() == "POST") {
            return [
                'clinic_name' => 'required|unique:clinics',
                'address' => 'required',
                'phone_number' => 'required|unique:clinics'
            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:clinics",
                'clinic_name' => 'required|unique:clinics,clinic_name,' . $this->id,
                'address' => 'required',
                'phone_number' => 'required|unique:clinics,phone_number,' . $this->id
            ];
        } else {
            return [
                "id" => "required|exists:clinics",
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
            'id.exists' => 'هذه العيادة غير متوفرة',
            'clinic_name.required' => 'يجب أدخال أسم العيادة',
            'clinic_name.unique' => 'هذا الأسم موجود مسبقاً',
            'address.required' => 'يجب أدخال العنوان',
            'phone_number.required' => 'يجب أدخال رقم الهاتف',
            'phone_number.unique' => 'هذا الرقم موجود مسبقاً'
        ];
    }
}
