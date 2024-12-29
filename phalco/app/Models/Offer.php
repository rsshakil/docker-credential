<?php

namespace App\Models;

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Enums\ProblemReportStatus;
use App\Enums\RateType;
use App\Services\AmountCalculationService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'offer_type' => OfferType::class,
        'rate_type' => RateType::class,
        'offer_status' => OfferStatus::class,
        'problem_report_status' => ProblemReportStatus::class,
        'requested_at' => 'datetime',
    ];

    /**
     * Relation
     */

    /**
     * 掲載ユーザー
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 取引通貨
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * 一時預かり用運営アカウント
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminProductAccount()
    {
        return $this->belongsTo(AdminProductAccount::class);
    }

    /**
     * 取引
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * オファー操作履歴
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offerHistories()
    {
        return $this->hasMany(OfferHistory::class);
    }

    /**
     * 最新のオファー操作履歴
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestOfferHistory()
    {
        return $this->hasOne(OfferHistory::class)->latest();
    }

    /**
     * １つ前のオファー操作履歴
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function prevOfferHistory()
    {
        return $this->hasOne(OfferHistory::class)->latest()->offset(1);
    }

    /**
     * トレード条件
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offerRequirement()
    {
        return $this->hasMany(OfferRequirement::class);
    }

    /**
     * チャットルーム
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function chatRooms()
    {
        return $this->morphOne(ChatRoom::class, 'chat_holder');
    }

     /* 掲載額
     *
     * @return Attribute
     */
    public function displayAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $calc = app(AmountCalculationService::class);
                $tradeFee = $this->product->trade_fee;
                $scale = $this->product->scale;
                return match ($this->offer_type) {
                    OfferType::BUY => $calc->getDisplayAmountBuy($this->amount??0, $tradeFee, $scale),
                    OfferType::SELL => $calc->getDisplayAmountSell($this->amount??0, $tradeFee, $scale),
                };
            }
        );
    }

    /* DisplayVariableRate
     *
     * @return Attribute
     */
    public function displayRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                $calc = app(AmountCalculationService::class);
                $baseCurrencyRate = $this->product->base_currency_rate;
                $scale = $this->product->scale;
                $currencyRate = $this->product
                    ->currency
                    ->currencyRates()
                    ->find(Currency::find($this->currency_id))
                    ->pivot
                    ->rate;
                return match ($this->rate_type) {
                    RateType::FIXED => $this->rate,
                    RateType::VARIABLE => $calc->getVariableTypeDisplayRate($this->rate??0, $currencyRate, $baseCurrencyRate, $scale),
                };
            }
        );
    }

    /**
     * 返送量
     *
     * @return Attribute
     */
    public function returnAmount(): Attribute
    {
        return Attribute::make(get: fn () =>
            app(AmountCalculationService::class)->getOfferReturnAmount($this)
        );
    }
}
