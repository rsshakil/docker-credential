export const PAYMENT_ITEM_TYPE = {
    TEXT: 1,
    SELECT: 2,
    CHECKBOX: 3,
    RADIO: 4,
    IMAGE: 5,
};

export const PAYMENT_ITEM_TYPE_TEXT = {
    [PAYMENT_ITEM_TYPE.TEXT]: 'テキスト',
    [PAYMENT_ITEM_TYPE.SELECT]: 'セレクトボックス',
    [PAYMENT_ITEM_TYPE.CHECKBOX]: 'チェックボックス',
    [PAYMENT_ITEM_TYPE.RADIO]: 'ラジオボタン',
    [PAYMENT_ITEM_TYPE.IMAGE]: '画像',
}

export const PAYMENT_ITEM_TYPE_OPTIONS = Object.values(PAYMENT_ITEM_TYPE).map(value => ({
    value,
    label: PAYMENT_ITEM_TYPE_TEXT[value],
}))

export const MULTI_PAYMENT_ITEM_TYPE = [
    PAYMENT_ITEM_TYPE.SELECT,
    PAYMENT_ITEM_TYPE.CHECKBOX,
    PAYMENT_ITEM_TYPE.RADIO,
]
