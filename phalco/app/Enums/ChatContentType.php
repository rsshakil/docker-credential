<?php

namespace App\Enums;

enum ChatContentType: int
{
    case MESSAGE = 1;
    case IMAGE = 2;

    public function text()
    {
        return match ($this) {
            self::MESSAGE => 'メッセージ',
            self::IMAGE => '画像',
        };
    }
}
