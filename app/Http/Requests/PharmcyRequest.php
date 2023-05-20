<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PharmcyRequest extends FormRequest
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
                'medicen_name' => 'required|unique:pharmacy_stores,medicen_name',
                'quantity' => 'required',
                'price' => 'required',
                'expaired' => 'required|date|after_or_equal:today',
                'representative_id' => 'exists:representatives,id'
            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:pharmacy_stores",
                'medicen_name' => 'required|unique:pharmacy_stores,medicen_name,' . $this->id,
                'quantity' => 'required',
                'price' => 'required',
                'expaired' => 'required|date|after_or_equal:today',
                'representative_id' => 'exists:representatives,id'

            ];
        } else {
            return [
                "id" => "required|exists:pharmacy_stores",
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
            'medicen_name.required' => 'يجب ادخال اسم الدواء',
            'medicen_name.unique' => 'اسم الدواء موجود مسبقا',
            'quantity.required' => 'يجب ادخال الكمية',
            'price.required' => 'يجب ادخال السعر',
            'expaired.required' => 'يجب ادخال تاريخ الانتهاء',
            'expaired.date' => 'يجب ادخال تاريخ الانتهاء بشكل صحيح',
            'expaired.after_or_equal' => 'يجب ادخال تاريخ الانتهاء بشكل صحيح',
            'representative_id.exists' => 'يجب ادخال رقم المندوب بشكل صحيح',
        ];
    }
}
