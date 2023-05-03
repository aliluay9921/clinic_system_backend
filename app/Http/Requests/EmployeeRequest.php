<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class EmployeeRequest extends FormRequest
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
                'full_name' => 'required|unique:employees,full_name',
                'phone_number' => 'required|unique:employees,phone_number',
                'employee_title' => 'required',
                'address' => 'required',
                'salary' => 'required'
            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:employees",
                'full_name' => 'required|unique:employees,full_name,' . $this->id,
                'phone_number' => 'required|unique:employees,phone_number,' . $this->id,
                'employee_title' => 'required',
                'address' => 'required',
                'salary' => 'required'
            ];
        } else {
            return [
                "id" => "required|exists:employees",
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
            'id.exists' => 'المستخدم غير موجود',
            'full_name.required' => 'يجب أدخال الأسم',
            'full_name.unique' => 'الأسم موجود مسبقا',
            'phone_number.required' => 'يجب أدخال رقم الهاتف',
            'phone_number.unique' => 'رقم الهاتف موجود مسبقا',
            'employee_title.required' => 'يجب أدخال المسمى الوظيفي',
            'address.required' => 'يجب أدخال العنوان',
            'salary.required' => 'يجب أدخال الراتب',
        ];
    }
}
