<?php

namespace App\Http\Requests\User;

use App\Enums\PaymentItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserSellerPaymentRequest extends FormRequest
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
            'paymentMethod_id' => [
                'required',
                'exists:payment_methods,id',
            ],
            'seller_payment_items' => [
                'array',
            ],
            'seller_payment_items.*.payment_item_id' => [
                'required',
                'exists:payment_items,id',
            ],
            'seller_payment_items.*.type' => [
                'required',
                Rule::enum(PaymentItemType::class),
            ],
            'seller_payment_items.*.value' => [
                'required',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'paymentMethod_id' => '支払方法',
            'seller_payment_items.*.value' => '設定値',
        ];
    }
}
