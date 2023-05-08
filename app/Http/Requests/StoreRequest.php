<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreRequest extends FormRequest
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
                'product_name' => 'required|unique:stores,product_name',
                'quantity' => 'required',
                'price' => 'required',
                'expaired' => 'required|date|after_or_equal:today',
                'representative_id' => 'exists:representatives,id'
            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:stores",
                'product_name' => 'required|unique:stores,product_name,' . $this->id,
                'quantity' => 'required',
                'price' => 'required',
                'expaired' => 'required',
                'representative_id' => 'exists:representatives,id'

            ];
        } else {
            return [
                "id" => "required|exists:stores",
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
            'product_name.required' => 'اسم المنتج مطلوب',
            'product_name.unique' => 'اسم المنتج موجود مسبقا',
            'quantity.required' => 'الكمية مطلوبة',
            'price.required' => 'السعر مطلوب',
            'expaired.required' => 'تاريخ الانتهاء مطلوب',
            'expaired.date' => 'تاريخ الانتهاء يجب ان يكون تاريخ',
            'expaired.after_or_equal' => 'تاريخ الانتهاء يجب ان يكون اكبر من او يساوي تاريخ اليوم',
            'representative_id.exists' => 'المندوب غير موجود'
        ];
    }
}
