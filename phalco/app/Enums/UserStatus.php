<?php

namespace App\Enums;

enum UserStatus: int
{
    case SAMPLE = 1;

    public function text()
    {
        return match ($this) {
            self::SAMPLE => 'サンプル',
        };
    }
}
