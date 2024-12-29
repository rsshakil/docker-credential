<?php

namespace App\Enums;

enum KycStatus: int
{
    case UNCONFIRMED = 1;
    case REVIEWING = 2;
    case REJECTED = 3;
    case CONFIRMED = 4;

    public function text()
    {
        return match ($this) {
            self::UNCONFIRMED => '未確認',
            self::REVIEWING => '審査中',
            self::REJECTED => '却下',
            self::CONFIRMED => '確認済',
        };
    }
}
