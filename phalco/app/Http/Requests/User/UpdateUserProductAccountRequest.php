<?php

namespace App\Http\Requests\User;

use App\Enums\ProductAccountItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProductAccountRequest extends FormRequest
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
            'id' => [
                'required',
                'exists:user_product_accounts,id',
            ],
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'user_product_account_items' => [
                'array',
            ],
            'user_product_account_items.*.product_account_item_id' => [
                'required',
                'exists:product_account_items,id',
            ],
            'user_product_account_items.*.type' => [
                'required',
                Rule::enum(ProductAccountItemType::class),
            ],
            'user_product_account_items.*.value' => [
                'required',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ユーザーアカウント',
            'product_id' => '商品',
            'user_product_account_items.*.value' => '設定値',
        ];
    }
}
