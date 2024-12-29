<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Trade;
use App\Models\User;

class NumberingService
{
    /**
     * ユーザー番号を生成
     *
     * @return int
     */
    public function generateUserNumber()
    {
        do {
            $userNo = random_int(1000000000, 0x7FFFFFFF);
        } while (User::where('user_no', $userNo)->exists());

        return $userNo;
    }

    /**
     * オファー番号を生成
     * 
     * @return int
     */
    public function generateOfferNumber()
    {
        do {
            $offerNo = random_int(1000000, 9999999);
            $offerNo = $offerNo * 10 + $this->calcCheckDigit($offerNo);
        } while (Offer::where('offer_no', $offerNo)->exists());

        return $offerNo;
    }

    /**
     * トレード番号を生成
     * 
     * @return int
     */
    public function generateTradeNumber()
    {
        do {
            $tradeNo = random_int(10000000, 99999999);
            $tradeNo = $tradeNo * 10 + $this->calcCheckDigit($tradeNo);
        } while (Trade::where('trade_no', $tradeNo)->exists());

        return $tradeNo;
    }

    /**
     * チェックデジット（モジュラス10 ウェイト2・1分割）の計算
     * 
     * @param int $value 計算対象の数値
     * @return int
     */
    private function calcCheckDigit(int $value)
    {
        $check = 0;
        $strVal = strval($value);
        $len = strlen($strVal);
        for ($i = 0; $i < $len; $i++) {
            $weight = $i % 2 ? 1 : 2;
            $c = intval($strVal[$len - $i - 1]) * $weight;
            $c -= $c >= 10 ? 9 : 0;
            $check += $c;
        }
        return (10 - $check % 10) % 10;
    }
}
