<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'unit' => [
                'required',
            ],
            'scale' => [
                'required',
            ],
            'currency_id' => [
                'required',
            ],
            'base_currency_rate' => [
                'required',
            ],
            'trade_fee' => [
                'required',
            ],
            'refund_fee' => [
                'required',
            ],
            'product_account_items' => [
                'array',
                'min:1',
            ],
            'product_account_items.*.name' => [
                'required',
            ],
            'product_account_items.*.type' => [
                'required',
            ],
            'product_account_items.*.product_account_item_options' => [
                'array',
            ],
            'product_account_items.*.product_account_item_options.*.name' => [
                'required',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => '商品名',
            'logo' => 'ロゴ',
            'unit' => '単位',
            'scale' => '小数桁数',
            'currency_id' => 'ベース通貨',
            'base_currency_rate' => 'ベース通貨レート',
            'trade_fee' => 'トレード手数料',
            'refund_fee' => '返金手数料',
            'product_account_items' => '商品送付先の項目定義',
            'product_account_items.*.name' => '項目名',
            'product_account_items.*.type' => 'タイプ',
            'product_account_items.*.product_account_item_options' => '商品送付先の項目のオプション定義',
            'product_account_items.*.product_account_item_options.*.name' => '項目名',
        ];
    }
}
