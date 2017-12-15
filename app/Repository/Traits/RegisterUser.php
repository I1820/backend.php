<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/15/17
 * Time: 12:28 PM
 */
namespace App\Repository\Traits;

use App\Exceptions\AuthException;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait RegisterUser
{

    /**
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function insertUser(Request $request)
    {
        if ($request->has('legal') && $request->get('legal') == 1) {
            return $this->insertLegalUser($request);
        } else {
            return $this->insertRealUser($request);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws AuthException
     */
    public function validateRegisterUser(Request $request)
    {
        if ($request->has('legal') && $request->get('legal') == 1) {
            $this->validateRegisterLegal($request);
        } else {
            $this->validateRegisterReal($request);
        }

    }

    private function validateRegisterReal(Request $request)
    {
        $messages = [
            'email.required' => 'لطفا ایمیل را وارد کنید',
            'email.email' => 'لطفا ایمیل را درست وارد کنید',
            'email.unique' => 'این ایمیل قبلا ثبت شده است',
            'password.required' => 'لطفا رمزعبور را وارد کنید',
            'password.min' => 'رمز عبور حداقل باید ۶ کارکتر باشد',
            'other_info.json' => 'لطفا سایر اطلاعات را درست وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'other_info' => 'json',
        ], $messages);

        if ($validator->fails())
            throw new AuthException($validator->errors()->first(), AuthException::C_GE);
    }

    private function validateRegisterLegal(Request $request)
    {
        $messages = [
            'org_interface_name.required' => 'لطفا نام رابط سازمان را وارد کنید',
            'org_interface_last_name.required' => 'لطفا نام خانوادگی رابط سازمان را وارد کنید',
            'org_interface_phone.required' => 'لطفا شماره تلفن ثابت رابط سازمان را وارد کنید',
            'org_interface_mobile.required' => 'لطفا شماره موبایل شخصی را وارد کنید',
            'type.required' => 'لطفا نوع حقوقی را وارد کنید',
            'org_name.required' => 'لطفا نام سازمان را وارد کنید',
            'reg_number.required' => 'لطفا شماره ثبت را وارد کنید',
            'ec_code.required' => 'لطفا کد اقتصادی را وارد کنید',

            'email.email' => 'لطفا ایمیل را درست وارد کنید',
            'email.unique' => 'این ایمیل قبلا ثبت شده است',
            'password.required' => 'لطفا رمزعبور را وارد کنید',
            'password.min' => 'رمز عبور حداقل باید ۶ کارکتر باشد',
        ];

        $validator = Validator::make($request->all(), [
            'org_interface_name' => 'required',
            'org_interface_last_name' => 'required',
            'org_interface_phone' => 'required',
            'org_interface_mobile' => 'required',
            'type' => 'required',
            'org_name' => 'required',
            'reg_number' => 'required',
            'ec_code' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], $messages);

        if ($validator->fails())
            throw new AuthException($validator->errors()->first(), AuthException::C_GE);
    }


    private function insertRealUser(Request $request)
    {
        return User::create([
            'active' => true,
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'other_info' => json_decode($request->get('other_info'), true)
        ]);
    }

    private function insertLegalUser(Request $request)
    {
        return User::create([
            'legal' => true,
            'active' => false,
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'other_info' => $request->only([
                'org_interface_name',
                'org_interface_last_name',
                'org_interface_phone',
                'org_interface_mobile',
                'type',
                'org_name',
                'reg_number',
                'ec_code'
            ])
        ]);
    }
}