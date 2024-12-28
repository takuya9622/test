<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules() {
        if (session()->has('shipping_address')) {
            $shippingAddress = session('shipping_address');

            return [
                'payment_method' => ['required'],
                'postal_code' => ['required', 'regex:/^\d+-\d+$/', 'size:8'],
                'address' => ['required', 'string', 'max:255'],
                'building' => ['nullable', 'string', 'max:255'],
            ];
        }

        return [
            'payment_method' => ['required'],
            'postal_code' => ['required', 'regex:/^\d+-\d+$/', 'size:8'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages() {
        return [
            'payment_method.required' => 'お支払方法を選択してください',
            'postal_code.regex' => '郵便番号は-を含めた8文字で入力してください',
            'postal_code.size' => '郵便番号は-を含めた8文字で入力してください',
            'postal_code.required' => '郵便番号を設定してください',
            'address.required' => '住所を設定してください',
            'building.required' => '建物名を設定してください',
        ];
    }
}
