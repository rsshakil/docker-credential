<?php

namespace App\Enums;

enum PostingStatus: int
{
    case STATUS_ALL = 1;
    case STATUS_IN_POSTING = 2;
    case STATUS_POSTING_SCHEDULE = 3;

    public function text()
    {
        return match ($this) {
            self::STATUS_ALL => 'すべて',
            self::STATUS_IN_POSTING => '掲載期間中',
            self::STATUS_POSTING_SCHEDULE => '掲載予定',
        };
    }
}
