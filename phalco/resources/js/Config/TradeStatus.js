export const TRADE_STATUS = {
    REQUEST__WAIT_PRODUCT: 1,
    REQUEST__CHECK_PRODUCT: 2,
    UNPAID__WAIT_PAY: 3,
    UNPAID__CHECK_PAY: 4,
    PAID__WAIT_PRODUCT: 5,
    PAID__SEND_PRODUCT: 6,
    DONE: 7,
    CANCEL__REQUEST: 8,
    CANCEL__NO_RETURN: 9,
    CANCEL__WAIT_RETURN: 10,
    CANCEL__RETURNING: 11,
    CANCEL__DONE: 12,
    STOP: 13,
    STOP__RETURN: 14,
    STOP__SEND: 15,
    STOP__DONE: 16,
};

export const TRADE_STATUS_TEXT = {
    [TRADE_STATUS.REQUEST__WAIT_PRODUCT]: '申請中：商品送付待ち',
    [TRADE_STATUS.REQUEST__CHECK_PRODUCT]: '申請中：商品確認中',
    [TRADE_STATUS.UNPAID__WAIT_PAY]: '未支払：支払待ち',
    [TRADE_STATUS.UNPAID__CHECK_PAY]: '未支払： 支払確認待ち',
    [TRADE_STATUS.PAID__WAIT_PRODUCT]: '支払済：商品送付待ち',
    [TRADE_STATUS.PAID__SEND_PRODUCT]: '支払済：商品送付中',
    [TRADE_STATUS.DONE]: 'トレード完了',
    [TRADE_STATUS.CANCEL__REQUEST]: 'キャンセル：申請中',
    [TRADE_STATUS.CANCEL__NO_RETURN]: 'キャンセル：返送不要',
    [TRADE_STATUS.CANCEL__WAIT_RETURN]: 'キャンセル：返送待ち',
    [TRADE_STATUS.CANCEL__RETURNING]: 'キャンセル：返送中',
    [TRADE_STATUS.CANCEL__DONE]: 'キャンセル：返送済み',
    [TRADE_STATUS.STOP]: '停止中',
    [TRADE_STATUS.STOP__RETURN]: '停止中：返送中',
    [TRADE_STATUS.STOP__SEND]: '停止中：送付中',
    [TRADE_STATUS.STOP__DONE]: '停止済',
}

export const TRADE_STATUS_OPTIONS = Object.values(TRADE_STATUS).map(value => ({
    value,
    label: TRADE_STATUS_TEXT[value],
}))
