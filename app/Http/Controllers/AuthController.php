<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use SendResponse, UploadImage, Pagination;

    public function login(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_name' => 'required',
            'password' => 'required'
        ], [
            'user_name.required' => 'يرجى ادخال رقم أسم المستخدم ',
            'password.required' => 'يرجى ادخال كلمة المرور ',
        ]);
        if ($validator->fails()) {
            return $this->send_response(400, "حصل خطأ في المدخلات", $validator->errors(), []);
        }

        if (auth()->attempt(array('user_name' => $request['user_name'], 'password' => $request['password']))) {
            $user = auth()->user();
            $token = $user->createToken('clinic_system_ali_luay')->accessToken;
            return $this->send_response(200, 'تم تسجيل الدخول بنجاح', [], $user, $token);
        } else {
            return $this->send_response(400, 'هناك مشكلة تحقق من تطابق المدخلات', null, null, null);
        }
    }

    public function register(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'phone_number' => 'required|unique:users,phone_number',
            'user_name' => 'required|unique:users,user_name',
            'name' => 'required',
            'password' => 'required',
            'clinic_id' => 'required',
        ], [
            'phone_number.required' => 'يرجى ادخال رقم الهاتف',
            'user_name.required' => 'يرجى ادخال اسم المستخدم ',
            'phone_number.unique' => 'رقم الهاتف الذي قمت بأدخاله تم استخدامه سابقاً',
            'user_name.unique' => 'اسم المستخدم الذي قمت بأدخاله تم استخدامه سابقاً',
            'password.required' => 'يرجى ادخال كلمة المرور ',
            'clinic_id.required' => 'يرجى ادخال العيادة',
            'name.required' => 'يرجى ادخال الاسم',
        ]);
        if ($validator->fails()) {
            return $this->send_response(400, "حصل خطأ في المدخلات", $validator->errors(), []);
        }
        $data = [];
        $data = [
            'user_name' => $request['user_name'],
            'phone_number' => $request['phone_number'],
            'clinic_id' => $request['clinic_id'],
            'password' => bcrypt($request['password']),
            'name' => $request['name'],
            'user_type' => 1,
        ];

        $user = User::create($data);
        $token = $user->createToken($user->user_name)->accessToken;

        return $this->send_response(200, 'تم تسجيل الدخول بنجاح', [], User::find($user->id), $token);
    }
}
