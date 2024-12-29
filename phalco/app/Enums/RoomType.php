<?php

namespace App\Enums;

enum RoomType: int
{
    case USER_TO_USER = 1;
    case BUYER_TO_ADMIN = 2;
    case SELLER_TO_ADMIN = 3;

    public function text()
    {
        return match ($this) {
            self::USER_TO_USER => 'ユーザーとユーザー',
            self::BUYER_TO_ADMIN => '購入者とアドミン',
            self::SELLER_TO_ADMIN => '販売者とアドミン',
        };
    }
}
