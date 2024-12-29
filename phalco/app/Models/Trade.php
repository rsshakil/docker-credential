<?php

namespace App\Models;

use App\Enums\OfferType;
use App\Enums\ProblemReportStatus;
use App\Enums\ProblemUser;
use App\Enums\TradeStatus;
use App\Services\AmountCalculationService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'trade_status' => TradeStatus::class,
        'buyer_problem_report_status' => ProblemReportStatus::class,
        'seller_problem_report_status' => ProblemReportStatus::class,
        'problem_user' => ProblemUser::class,
    ];

    /**
     * Sellerが送る送付量
     *
     * @return Attribute
     */
    public function sendTradeAmount(): Attribute
    {
        return Attribute::make(get: fn () =>
            app(AmountCalculationService::class)->getSendTradeAmount($this)
        );
    }

    /**
     * Buyerが受け取る取引量
     *
     * @return Attribute
     */
    public function receiptTradeAmount(): Attribute
    {
        return Attribute::make(get: fn () =>
            app(AmountCalculationService::class)->getReceiptTradeAmount($this)
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
            app(AmountCalculationService::class)->getTradeReturnAmount($this)
        );
    }

    /**
     * Relation
     */

    /**
     * オファー
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Buyer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
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
     * 一時預かり用運営アカウント
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminProductAccount()
    {
        return $this->belongsTo(AdminProductAccount::class);
    }

    /**
     * 支払先情報ID
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sellerPayment()
    {
        return $this->belongsTo(SellerPayment::class);
    }

    /**
     * Buyer商品送付先
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyerAccount()
    {
        return $this->belongsTo(UserProductAccount::class, 'buyer_account_id');
    }

    /**
     * Seller商品送付先
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sellerAccount()
    {
        return $this->belongsTo(UserProductAccount::class, 'seller_account_id');
    }

    /**
     * トレード操作履歴
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tradeHistories()
    {
        return $this->hasMany(TradeHistory::class);
    }

    /**
     * 最新のトレード操作履歴
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestTradeHistory()
    {
        return $this->hasOne(TradeHistory::class)->latest();
    }

    /**
     * 最新のトレード停止操作履歴
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestStopTradeHistory()
    {
        return $this->hasOne(TradeHistory::class)
            ->where('result_status', TradeStatus::STOP)
            ->latest();
    }

    /**
     * ChatRoom チャットルーム
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function chatRooms()
    {
        return $this->morphMany(ChatRoom::class, 'chat_holder');
    }
}
