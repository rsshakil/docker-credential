<?php

namespace App\Http\Requests\Admin\Currency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'                         => 'nullable | integer',
            'name'                       => 'required',
            'code'                       => 'required | max:3',
            'scale'                      => 'required | integer',
            'min_amount'                 => 'nullable | numeric',
            'is_fixed_rate'              => 'nullable | boolean',
            'currency_rate.*'            => 'nullable | array',
            'currency_rate.*.pivot.rate' => 'nullable | numeric',
        ];
    }

    public function attributes()
    {
        return [
            'id'                         => '通貨ID',
            'name'                       => '通貨名',
            'code'                       => '通貨コード',
            'scale'                      => '小数点数',
            'min_amount'                 => '最小取引額',
            'currency_rate.*.pivot.rate' => '通貨レート',
        ];
    }
}
