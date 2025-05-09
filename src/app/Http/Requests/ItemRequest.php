<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'explain' => ['required', 'max:255'],
            'img_path' => ['required', 'mimetypes:image/jpeg,image/jpg,image/png', 'mimes:jpeg,jpg,png'],
            'category_ids' => ['required', 'array'],
            'condition' => ['required'],
            'price' => ['required', 'integer', 'min:0']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'explain.required' => '商品説明を入力してください',
            'explain.max' => '商品説明は255文字以下で入力してください',
            'img_path.required' => '商品画像を選択してください',
            'img_path.mimetypes' => '商品画像には、jpgファイルかpngファイルの画像をアップロードしてください。',
            'img_path.mimes' => '商品画像には、jpgファイルかpngファイルの画像をアップロードしてください。',
            'category_ids.required' => '商品のカテゴリーを１つ以上選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格は数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください'
        ];
    }
}
