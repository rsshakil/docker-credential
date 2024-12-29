<?php

namespace App\Http\Requests\Admin\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
            ],
            'logo' => [
                'required',
                'file',
            ],
            'currency_id' => [
                'required',
            ],
            'payment_items' => [
                'array',
                'min:1',
            ],
            'payment_items.*.name' => [
                'required',
            ],
            'payment_items.*.type' => [
                'required',
            ],
            'payment_items.*.payment_options' => [
                'array',
            ],
            'payment_items.*.payment_options.*.name' => [
                'required',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => '支払方法名',
            'logo' => 'ロゴ',
            'currency_id' => '通貨',
            'payment_items' => '支払方法の項目定義',
            'payment_items.*.name' => '項目名',
            'payment_items.*.type' => 'タイプ',
            'payment_items.*.payment_options' => '支払方法の項目のオプション定義',
            'payment_items.*.payment_options.*.name' => '項目名',
        ];
    }
}
