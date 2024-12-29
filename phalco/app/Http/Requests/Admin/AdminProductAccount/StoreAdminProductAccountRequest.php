<?php

namespace App\Http\Requests\Admin\AdminProductAccount;

use App\Enums\ProductAccountItemType;
use App\Models\AdminProductAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminProductAccountRequest extends FormRequest
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
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'is_temporary' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if (blank($this->product_id)) {
                        return;
                    }
                    $requireTemporary = AdminProductAccount::where('product_id', $this->product_id)
                        ->where('is_temporary', true)
                        ->count() === 0;
                    if ($requireTemporary && !$value) {
                        $fail('一時預かり用アカウントが１つ必要です');
                    }
                },
            ],
            'is_send' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if (blank($this->product_id)) {
                        return;
                    }
                    $requireTemporary = AdminProductAccount::where('product_id', $this->product_id)
                            ->where('is_send', true)
                            ->count() === 0;
                    if ($requireTemporary && !$value) {
                        $fail('送付用アカウントが１つ必要です');
                    }
                },
            ],
            'admin_product_account_items' => [
                'array',
            ],
            'admin_product_account_items.*.product_account_item_id' => [
                'required',
                'exists:product_account_items,id',
            ],
            'admin_product_account_items.*.type' => [
                'required',
                Rule::enum(ProductAccountItemType::class),
            ],
            'admin_product_account_items.*.value' => [
                'required',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => '運営アカウント名',
            'product_id' => '商品',
            'admin_product_account_items.*.value' => '設定値',
        ];
    }
}
