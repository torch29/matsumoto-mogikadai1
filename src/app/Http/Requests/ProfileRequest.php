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
            'profile_img' => ['mimetypes:imaga/jpeg,image/jpg,image/png', 'mimes:jpeg,jpg,png'],
        ];
    }

    public function messages()
    {
        return [
            'profile_img.mimetypes' => 'プロフィール画像には、jpgファイルかpngファイルの画像をアップロードしてください。',
            'profile_img.mimes' => 'プロフィール画像には、jpgファイルかpngファイルの画像をアップロードしてください。'
        ];
    }
}
