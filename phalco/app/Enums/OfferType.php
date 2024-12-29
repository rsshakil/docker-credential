<?php

namespace App\Enums;

enum OfferType: int
{
    case BUY = 1;
    case SELL = 2;

    public function text()
    {
        return match ($this) {
            self::BUY => '購入',
            self::SELL => '販売',
        };
    }
}
