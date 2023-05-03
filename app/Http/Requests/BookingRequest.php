<?php

namespace App\Http\Requests;

use App\Traits\SendResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BookingRequest extends FormRequest
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
                'patint_name' => 'required',
                'address' => 'required',
                'gender' => 'required',
                'phone_number' => 'required',
                'age' => 'required',
                'booking_date' => 'required|after_or_equal:today',
                'booking_time' => 'required',
                'doctor_id' => 'required',
                'booking_type' => 'required',
                'booking_note' => 'required',
                'price' => 'required',
                'primary_diagonses' => 'required',
                'payment_method' => 'required',
                'value_paid' => 'required_if:payment_method,1'

            ];
        } else if ($this->method() == "PUT") {
            return [
                "id" => "required|exists:bookings",
                'patint_name' => 'required',
                'address' => 'required',
                'gender' => 'required',
                'phone_number' => 'required',
                'age' => 'required',
                'booking_date' => 'required',
                'booking_time' => 'required',
                'doctor_id' => 'required',
                'booking_type' => 'required',
                'booking_note' => 'required',
                'primary_diagonses' => 'required',
                'price' => 'required',
                'payment_method' => 'required',
                'value_paid' => 'required_if:payment_method,1'
            ];
        } else {
            return [
                "id" => "required|exists:bookings",
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
            'id.required' => 'يجب أدخال الحجز الخاص بالمريض ',
            'id.exists' => 'الحجز غير متوفر',
            'patint_name.required' => 'يجب أدخال اسم المريض',
            'address.required' => 'يجب أدخال عنوان المريض',
            'gender.required' => 'يجب أدخال جنس المريض',
            'phone_number.required' => 'يجب أدخال رقم الهاتف',
            'age.required' => 'يجب أدخال عمر المريض',
            'booking_date.required' => 'يجب أدخال تاريخ الحجز',
            'booking_time.required' => 'يجب أدخال وقت الحجز',
            'doctor_id.required' => 'يجب أدخال اسم الطبيب',
            'booking_type.required' => 'يجب أدخال نوع الحجز',
            'booking_note.required' => 'يجب أدخال ملاحظات الحجز',
            'price.required' => 'يجب أدخال سعر الحجز',
            'primary_diagonses.required' => 'يجب أدخال التشخيص الأساسي',
            'booking_date.after_or_equal' => 'يجب أن يكون تاريخ الحجز أكبر من تاريخ اليوم',
        ];
    }
}
