<?php

namespace App\Enums;

enum ProblemUser: int
{
    case NONE = 1;
    case BUYER = 2;
    case SELLER = 3;
    public function text()
    {
        return match ($this) {
            self::NONE => 'なし',
            self::BUYER => 'Buyerの責任',
            self::SELLER => 'Sellerの責任',
        };
    }
}
