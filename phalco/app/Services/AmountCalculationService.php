<?php

namespace App\Services;

use App\Enums\OfferType;
use App\Enums\ProblemUser;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Trade;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AmountCalculationService
{
    /**
     * 商品のレートを取得
     *
     * @param string $rate 商品の基準通貨の変換レート
     * @param string $weight 商品のベース通貨に対する重み係数
     * @return float
     */
    public function getProductRate(string $rate, string $weight)
    {
        return (float)bcmul($rate, $weight, 2);
    }

    /**
     * 商品の基準レートの下限を取得
     *
     * @param string $rate 商品のレート
     * @return float
     */
    public function getProductMaxRate(string $rate)
    {
        return floor(bcmul($rate, '1.3', 3) * 100) / 100;
    }

    /**
     * 商品の基準レートの上限を取得
     *
     * @param string $rate 商品のレート
     * @return float
     */
    public function getProductMinRate(string $rate)
    {
        return ceil(bcmul($rate, '0.7', 3) * 100) / 100;
    }

    /**
     * 購入時
     * 総数量から手数料を考慮した取引できる数量を取得
     *
     * @param float $amount
     * @param float $tradeFee
     * @param int $scale
     * @return float|int
     */
    public function getDisplayAmountBuy(float $amount, float $tradeFee, int $scale)
    {
        $scaleValue = 10 ** $scale;
        return ceil(($amount * (1 + $tradeFee)) * $scaleValue) / $scaleValue;
    }

    /**
     * 販売時
     * 総数量から手数料を考慮した取引できる数量を取得
     *
     * @param float $amount
     * @param float $tradeFee
     * @param int $scale
     * @return float|int
     */
    public function getDisplayAmountSell(float $amount, float $tradeFee, int $scale)
    {
        $scaleValue = 10 ** $scale;
        return floor(($amount / (1 + $tradeFee)) * $scaleValue) / $scaleValue;
    }

    /**
     * Calculation variable rate of an Offer
     *
     * @param float $rate
     * @param float $currencyRate
     * @param float $baseCurrencyRate
     * @param int $scale
     * @return float|int
     */
    public function getVariableTypeDisplayRate(float $rate, float $currencyRate, float $baseCurrencyRate, int $scale)
    {
        $scaleValue = 10 ** $scale;
        $rateConvertion = $rate/100;
        return floor(($rateConvertion * $currencyRate * $baseCurrencyRate) * $scaleValue) / $scaleValue;
    }

    /**
     * オファーの返送量
     *
     * @param Offer $offer
     * @return mixed
     */
    public function getOfferReturnAmount(Offer $offer)
    {
        return $offer->amount - $offer->product->refund_fee;
    }

    /**
     * トレードの返送量
     * Seller責任なら手数料差し引き
     * Buyer責任ならそのまま
     *
     * @param Trade $trade
     * @return mixed
     */
    public function getTradeReturnAmount(Trade $trade)
    {
        if ($trade->problem_user === ProblemUser::SELLER) {
            $amount = $trade->trade_amount - $trade->product->refund_fee;
        } else {
            $amount = $trade->trade_amount;
        }

        return $amount;
    }

    /**
     * Sellerが送る送付量
     *
     * @param Trade $trade
     * @return float
     */
    public function getSendTradeAmount(Trade $trade)
    {
        if ($trade->offer->offer_type === OfferType::SELL) {
            $tradeAmount = $trade->trade_amount;
            $tradeFee = $trade->product->trade_fee;
            $scale = 10 ** $trade->product->scale;
            $amount = ceil(($tradeAmount * (1 + $tradeFee)) * $scale) / $scale;
        } else {
            $amount = $trade->trade_amount;
        }
        return $amount;
    }

    /**
     * Buyerが受け取る取引量
     *
     * @param Trade $trade
     * @return float
     */
    public function getReceiptTradeAmount(Trade $trade)
    {
        if ($trade->offer->offer_type === OfferType::BUY) {
            $tradeAmount = $trade->trade_amount;
            $tradeFee = $trade->product->trade_fee;
            $scale = 10 ** $trade->product->scale;
            $amount = floor(($tradeAmount / (1 + $tradeFee)) * $scale) / $scale;
        } else {
            $amount = $trade->trade_amount;
        }
        return $amount;
    }

    /**
     * トレード完了後のオファー総数量を取得
     *
     * @param Trade $trade
     * @return float
     */
    public function getOfferAmountAfterTrade(Trade $trade)
    {
        if ($trade->offer->offer_type === OfferType::BUY) {
            $tradeAmount = $trade->receipt_trade_amount;
        } else {
            $tradeAmount = $trade->send_trade_amount;
        }

        return $trade->offer->amount - $tradeAmount;
    }
}
