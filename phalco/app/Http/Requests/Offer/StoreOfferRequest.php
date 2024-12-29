<?php

namespace App\Http\Requests\Offer;

use App\Enums\OfferType;
use App\Enums\RateType;
use App\Models\Currency;
use App\Models\Product;
use App\Services\AmountCalculationService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfferRequest extends FormRequest
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
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'offer_type' => [
                'required',
                Rule::enum(OfferType::class),
            ],
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'currency_id' => [
                'required',
                'exists:currencies,id',
            ],
            'rate_type' => [
                'required',
                Rule::enum(RateType::class),
            ],
            'rate' => [
                'bail',
                'required',
                'numeric',
                'regex:/^(0|[1-9]\d*)(\.\d{1,2}|)$/',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($this->enum('rate_type', RateType::class) === RateType::FIXED) {
                        $product = Product::find($this->input('product_id'));
                        $rate = $product
                            ->currency
                            ->currencyRates()
                            ->find(Currency::find($this->input('currency_id')))
                            ->pivot
                            ->rate;
                        $calcService = app(AmountCalculationService::class);
                        $baseRate = $calcService->getProductRate($rate, $product->base_currency_rate);
                        $minRate = $calcService->getProductMinRate($baseRate);
                        $maxRate = $calcService->getProductMaxRate($baseRate);

                        if ($minRate > $value || $maxRate < $value) {
                            $fail("{$minRate} ~ {$maxRate} の範囲で指定してください");
                        }
                    } else {
                        if (70 > $value || 130 < $value) {
                            $fail('70 ~ 130の範囲で指定してください');
                        }
                    }
                },
            ],
            'amount' => [
                'required',
                'numeric',
            ],
            'min_amount' => [
                'required',
                'numeric',
                function (string $attribute, mixed $value, Closure $fail) {
                    $currency = Currency::find($this->input('currency_id'));
                    if ($currency->min_amount > $value) {
                        $fail("{$currency->min_amount}以上にしてください");
                    }
                },
            ],
            'max_amount' => [
                'nullable',
                'numeric',
            ],
            'time_limit' => [
                'required',
                'integer',
                Rule::in([30, 15]),
            ],
            'note' => [
                'nullable',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'product_id' => '商品種類',
            'rate' => 'レート',
            'amount' => '量',
            'min_amount' => '最小額',
            'max_amount' => '最大額',
            'note' => '備考',
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge([
            'user_id' => $this->user()->id,
            'note' => $this->input('note') ?? ''
        ]);
    }
}
