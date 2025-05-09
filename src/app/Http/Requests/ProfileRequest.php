<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => ['required'],
            'zip_code' => ['required', 'regex:/^[0-9]{3}-[0-9]{4}$/', 'max:8'],
            'address' => ['required'],
            'profile_img' => ['mimetypes:imaga/jpeg,image/jpg,image/png', 'mimes:jpeg,jpg,png'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'zip_code.required' => '郵便番号を入力してください',
            'zip_code.regex' => '郵便番号は、半角数字とハイフンを含めてXXX-XXXX の形で入力してください',
            'zip_code.max' => '郵便番号は、半角数字とハイフンを含めて8桁で入力してください',
            'address.required' => '配送先の住所を入力してください',
            'profile_img.mimetypes' => 'プロフィール画像には、jpgファイルかpngファイルの画像をアップロードしてください。',
            'profile_img.mimes' => 'プロフィール画像には、jpgファイルかpngファイルの画像をアップロードしてください。'
        ];
    }
}
