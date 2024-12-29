<?php

namespace App\Enums;

enum Language: int
{
    case JP = 1;
    case EN = 2;

    public function text()
    {
        return match ($this) {
            self::JP => '日本語',
            self::EN => 'English',
        };
    }
}
