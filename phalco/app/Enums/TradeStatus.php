<?php

namespace App\Enums;

enum TradeStatus: int
{
    case REQUEST__WAIT_PRODUCT = 1;
    case REQUEST__CHECK_PRODUCT = 2;
    case UNPAID__WAIT_PAY = 3;
    case UNPAID__CHECK_PAY = 4;
    case PAID__WAIT_PRODUCT = 5;
    case PAID__SEND_PRODUCT = 6;
    case DONE = 7;
    case CANCEL__REQUEST = 8;
    case CANCEL__NO_RETURN = 9;
    case CANCEL__WAIT_RETURN = 10;
    case CANCEL__RETURNING = 11;
    case CANCEL__DONE = 12;
    case STOP = 13;
    case STOP__RETURN = 14;
    case STOP__SEND = 15;
    case STOP__DONE = 16;

    public function text()
    {
        return match ($this) {
            self::REQUEST__WAIT_PRODUCT => '申請中：商品送付待ち',
            self::REQUEST__CHECK_PRODUCT => '申請中：商品確認中',
            self::UNPAID__WAIT_PAY => '未支払：支払待ち',
            self::UNPAID__CHECK_PAY => '未支払： 支払確認待ち',
            self::PAID__WAIT_PRODUCT => '支払済：商品送付待ち',
            self::PAID__SEND_PRODUCT => '支払済：商品送付中',
            self::DONE => 'トレード完了',
            self::CANCEL__REQUEST => 'キャンセル：申請中',
            self::CANCEL__NO_RETURN => 'キャンセル：返送不要',
            self::CANCEL__WAIT_RETURN => 'キャンセル：返送待ち',
            self::CANCEL__RETURNING => 'キャンセル：返送中',
            self::CANCEL__DONE => 'キャンセル：返送済み',
            self::STOP => '停止中',
            self::STOP__RETURN => '停止中：返送中',
            self::STOP__SEND => '停止中：送付中',
            self::STOP__DONE => '停止済',
        };
    }

    const ONGOING_TRADE_STATUSES = [
        self::REQUEST__WAIT_PRODUCT,
        self::REQUEST__CHECK_PRODUCT,
        self::UNPAID__WAIT_PAY,
        self::UNPAID__CHECK_PAY,
        self::PAID__WAIT_PRODUCT,
        self::PAID__SEND_PRODUCT,
    ];
}
