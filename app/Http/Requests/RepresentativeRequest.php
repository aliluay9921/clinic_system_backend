<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RepresentativeRequest extends FormRequest
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
                'name' => 'required|unique:representatives,name',
                'phone_number' => 'required|unique:representatives,phone_number',
                'company_name' => 'required',
            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:representatives",
                'name' => 'required|unique:representatives,name,' . $this->id,
                'phone_number' => 'required|unique:representatives,phone_number,' . $this->id,
                'company_name' => 'required',
            ];
        } else {
            return [
                "id" => "required|exists:representatives",
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
            'name.required' => 'يجب أدخال الأسم',
            'name.unique' => 'الأسم موجود مسبقا',
            'phone_number.required' => 'يجب أدخال رقم الهاتف',
            'phone_number.unique' => 'رقم الهاتف موجود مسبقا',
            'company.required' => 'يجب أدخال اسم الشركة',

        ];
    }
}
