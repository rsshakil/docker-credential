<?php

namespace App\Enums;

enum RateType: int
{
    case FIXED = 1;
    case VARIABLE = 2;

    public function text()
    {
        return match ($this) {
            self::FIXED => '固定レート',
            self::VARIABLE => '変動レート',
        };
    }
}
