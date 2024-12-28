<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules() {
        return [
            'name' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'regex:/^\d+-\d+$/', 'size:8'],
            'address' => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(){
        return [
            'name.required' => 'お名前を入力してください',
            'postal_code.regex' => '郵便番号は-を含めた8文字で入力してください',
            'postal_code.size' => '郵便番号は-を含めた8文字で入力してください',
            'profile_image.image' => '画像ファイルをアップロードしてください',
            'profile_image.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
            'profile_image.max' => 'プロフィール画像のサイズは2MB以下にしてください',
        ];
    }
}
