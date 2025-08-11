<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'message' => ['required', 'max:400'],
            'img_path' => ['mimetypes:image/jpeg,image/jpg,image/png', 'mimes:jpeg,jpg,png']
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max' => '本文は400文字以下で入力してください',
            'img_path.mimetypes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'img_path.mimes' => '「.png」または「.jpeg」形式でアップロードしてください'
        ];
    }
}
