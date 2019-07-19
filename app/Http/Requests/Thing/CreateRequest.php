<?php

namespace App\Http\Requests\Thing;

use function foo\func;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'devEUI' => 'required|regex:/^[0-9a-fA-F]{16}$/|unique:things,dev_eui',
            'type' => 'required|',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'period' => 'required|numeric',
            'thing_profile_slug' => Rule::requiredIf($this->input('type') == 'lora')
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'لطفا نام شی را وارد کنید',
            'type.required' => 'نوع اینترفیس شی را وارد کنید',
            'lat.numeric' => 'لطفا محل سنسور را درست وارد کنید',
            'lat.required' => 'لطفا محل سنسور را درست کنید',
            'long.numeric' => 'لطفا محل سنسور را درست وارد کنید',
            'long.required' => 'لطفا محل سنسور را وارد کنید',
            'period.required' => 'لطفا بازه ارسال داده سنسور را وارد کنید',
            'period.numeric' => 'لطفا بازه ارسال داده سنسور را درست وارد کنید',
            'devEUI.min' => 'لطفا devEUI سنسور را درست وارد کنید',
            'devEUI.max' => 'لطفا devEUI سنسور را درست وارد کنید',
            'devEUI.unique' => 'این devEUI قبلا ثبت شده است',
            'devEUI.required' => 'لطفا devEUI سنسور را وارد کنید',
            'thing_profile_slug.required' => 'لطفا شناسه پروفایل شی را وارد کنید',
        ];
    }
}
