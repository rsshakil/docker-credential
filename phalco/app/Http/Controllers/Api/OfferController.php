<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Offer;
use App\Services\AmountCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;


class OfferController extends Controller
{
    public function __construct(
        readonly private AmountCalculationService $amountCalculationService,
    )
    {
    }

    public function getProductRate(Request $request)
    {
        $product = Product::find($request->query('product_id'));
        $rate = $product
            ->currency
            ->currencyRates()
            ->find(Currency::find($request->query('currency_id')))
            ->pivot
            ->rate;
        $baseRate = $this->amountCalculationService->getProductRate($rate, $product->base_currency_rate);
        $minRate = $this->amountCalculationService->getProductMinRate($baseRate);
        $maxRate = $this->amountCalculationService->getProductMaxRate($baseRate);

        return [
            "baseRate" => $baseRate,
            "minRate" => $minRate,
            "maxRate" => $maxRate
        ];
    }

    public function getDisplayAmountBuy(Request $request)
    {
        $amount = $request->float('amount');
        $product = Product::find($request->query('product_id'));

        return $this->amountCalculationService->getDisplayAmountBuy(
            $amount,
            $product->trade_fee,
            $product->scale
        );
    }

    public function getDisplayAmountSell(Request $request)
    {
        $amount = $request->float('amount');
        $product = Product::find($request->query('product_id'));

        return $this->amountCalculationService->getDisplayAmountSell(
            $amount,
            $product->trade_fee,
            $product->scale
        );
    }

    public function getOfferRemainingTime(Offer $offer)
    {   
        if (!$offer) {
            return response()->json(['error' => 'Offer not found'], 404);
        }

        $requestDate = $offer->requested_at;
        $countdownEnd = $requestDate->addMinutes(15);
        $now = Carbon::now();
        $remainingTime = $now->diffInSeconds($countdownEnd, false);
        $isExpired = false;

        if ($remainingTime < 0) {
            $isExpired = true;
        }
    
        return response()->json([
            'remainingTime' => $remainingTime, 
            'endTime' => $countdownEnd->toDateTimeString(), 
            'isExpired' => $isExpired
        ]);
    }
}
