<?php
namespace App\Services;

use App\Enums\OfferType;
use App\Enums\OfferStatus;
use App\Enums\TradeStatus;
use App\Models\BuyerPayment;
use App\Models\SellerPayment;
use App\Models\Offer;

class OfferService
{
    public function getUserPaymentMethods(int $userId)
    {
        return [
            OfferType::SELL->value => $this->getSellerPaymentMethods($userId),
            OfferType::BUY->value => $this->getBuyerPaymentMethods($userId),
        ];
    }

    public function getUserPaymentMethodByOfferType(OfferType $offerType, int $userId)
    {
        $paymentMethod = match($offerType){
            OfferType::SELL => $this->getSellerPaymentMethods($userId),
            OfferType::BUY => $this->getBuyerPaymentMethods($userId),
        };
        return [$offerType->value => $paymentMethod];
    }

    private function getSellerPaymentMethods(int $userId)
    {
        return SellerPayment::query()->with(['paymentMethod', 'sellerPaymentItems' => [
            'paymentItem',"paymentItem.paymentOptions"
        ]])->where('user_id', $userId)->get()->groupBy(fn($item) => $item->paymentMethod->currency_id)->toArray();
    }

    private function getBuyerPaymentMethods(int $userId)
    {
        return BuyerPayment::query()->with(['paymentMethod'])->where('user_id', $userId)->get()->groupBy(fn($item) => $item->paymentMethod->currency_id)->toArray();
    }

    public function updateStatus(Offer $offer, OfferStatus $requestOfferStatus)
    {
        $offer->offer_status = $requestOfferStatus;
        $offer->save();
        return $offer;
    }

    public function canUpdateOfferStatus(Offer $offer, OfferStatus $requestOfferStatus)
    {
        return match ($offer->offer_type) {
            OfferType::BUY => match ($offer->offer_status) {
                OfferStatus::PUBLISHING__ACTIVE => match ($requestOfferStatus) {
                    OfferStatus::CANCEL__NO_RETURN,
                    OfferStatus::CANCEL__WAIT_TRADE,
                    OfferStatus::PUBLISHING__PAUSE => true,
                    default => false,
                },
                OfferStatus::PUBLISHING__PAUSE => match ($requestOfferStatus) {
                    OfferStatus::CANCEL__NO_RETURN,
                    OfferStatus::CANCEL__WAIT_TRADE,
                    OfferStatus::PUBLISHING__ACTIVE => true,
                    default => false,
                },
                default => false,
            },
            OfferType::SELL => match ($offer->offer_status) {
                OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT => match ($requestOfferStatus) {
                    OfferStatus::WAIT_PUBLISH__CHECK_PRODUCT,
                    OfferStatus::CANCEL__NO_RETURN => true,
                    default => false,
                },
                OfferStatus::PUBLISHING__ACTIVE => match ($requestOfferStatus) {
                    OfferStatus::CANCEL__NO_RETURN,
                    OfferStatus::CANCEL__WAIT_TRADE,
                    OfferStatus::CANCEL__WAIT_RETURN,
                    OfferStatus::PUBLISHING__PAUSE => true,
                    default => false,
                },
                OfferStatus::PUBLISHING__PAUSE => match ($requestOfferStatus) {
                    OfferStatus::CANCEL__NO_RETURN,
                    OfferStatus::CANCEL__WAIT_TRADE,
                    OfferStatus::CANCEL__WAIT_RETURN,
                    OfferStatus::PUBLISHING__ACTIVE => true,
                    default => false,
                },
                default => false,
            },   
        };
    }

    public function determineNextStatusForCancel(Offer $offer){
        return match($offer->offer_type){
            OfferType::BUY => match ($offer->offer_status) {
                OfferStatus::PUBLISHING__ACTIVE,
                OfferStatus::PUBLISHING__PAUSE => $this->evaluateCancelStatus($offer),
                default => false,
            },
            OfferType::SELL => match ($offer->offer_status) { 
                OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT => OfferStatus::CANCEL__NO_RETURN,
                OfferStatus::PUBLISHING__ACTIVE, 
                OfferStatus::PUBLISHING__PAUSE => $this->evaluateCancelStatus($offer),
                default => false,
            },  
        };
    }

    private function evaluateCancelStatus(Offer $offer)
    {   
        $hasOngoingTrades = $offer->trades()->whereIn('trade_status', TradeStatus::ONGOING_TRADE_STATUSES)->exists();
        if($hasOngoingTrades){
            return OfferStatus::CANCEL__WAIT_TRADE;
        }

        return $offer->offer_type === OfferType::SELL && $offer->amount > 0 ? OfferStatus::CANCEL__WAIT_RETURN : OfferStatus::CANCEL__NO_RETURN;
    }
}