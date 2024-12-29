<?php

namespace App\Http\Requests\Admin\Trade;

use App\Enums\TradeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToStopDoneRequest extends FormRequest
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
                Rule::requiredIf(collect([
                    TradeStatus::STOP__RETURN,
                    TradeStatus::STOP__SEND,
                ])->contains($this->trade->trade_status)),
                'nullable',
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
