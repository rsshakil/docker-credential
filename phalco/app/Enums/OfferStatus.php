<?php

namespace App\Enums;

enum OfferStatus: int
{
    case WAIT_PUBLISH__WAIT_PRODUCT = 1;
    case WAIT_PUBLISH__CHECK_PRODUCT = 2;
    case PUBLISHING__ACTIVE = 3;
    case PUBLISHING__PAUSE = 4;
    case REJECT__CHECKING = 5;
    case REJECT__RETURNING = 6;
    case REJECT__DONE = 7;
    case CANCEL__WAIT_TRADE = 8;
    case CANCEL__NO_RETURN = 9;
    case CANCEL__WAIT_RETURN = 10;
    case CANCEL__RETURNING = 11;
    case CANCEL__DONE = 12;
    case STOP = 13;

    public function text()
    {
        return match ($this) {
            self::WAIT_PUBLISH__WAIT_PRODUCT => '掲載待ち：商品送付待ち',
            self::WAIT_PUBLISH__CHECK_PRODUCT => '掲載待ち：商品確認待ち',
            self::PUBLISHING__ACTIVE => '掲載中：アクティブ',
            self::PUBLISHING__PAUSE => '掲載中：申請受付休止中',
            self::REJECT__CHECKING => '却下：却下中',
            self::REJECT__RETURNING => '却下：返送中',
            self::REJECT__DONE => '却下：却下済',
            self::CANCEL__WAIT_TRADE => 'キャンセル：トレード完了待ち',
            self::CANCEL__NO_RETURN => 'キャンセル：返送不要',
            self::CANCEL__WAIT_RETURN => 'キャンセル：返送待ち',
            self::CANCEL__RETURNING => 'キャンセル：返送中',
            self::CANCEL__DONE => 'キャンセル：返送完了',
            self::STOP => '掲載差し止め',
        };
    }
}
