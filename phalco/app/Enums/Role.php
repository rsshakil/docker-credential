<?php

namespace App\Enums;

enum Role: int
{
    case SV = 1;
    case OP = 2;

    public function text()
    {
        return match ($this) {
            self::SV => '全権限',
            self::OP => 'オペレーター権限',
        };
    }
}
