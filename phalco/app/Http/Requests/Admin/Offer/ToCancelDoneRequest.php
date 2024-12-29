<?php

namespace App\Http\Requests\Admin\Offer;

use Illuminate\Foundation\Http\FormRequest;

class ToCancelDoneRequest extends FormRequest
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
            'admin_product_account_id' => [
                'required',
                'exists:admin_product_accounts,id',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'admin_product_account_id' => '運営アカウント',
        ];
    }
}
