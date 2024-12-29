<?php

namespace App\Jobs;
use App\Models\Offer;
use App\Enums\OfferStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class JobsOfferSendingProductTimeout implements ShouldQueue
{
    use Queueable;

    protected $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function handle()
    {
        if($this->offer->offer_status === OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT){
            $this->offer->offer_status =  OfferStatus::CANCEL__NO_RETURN;
            $this->offer->save(); 
        }else{
            return;
        }
    }
}
