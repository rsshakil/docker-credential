export const OFFER_TYPE = {
    BUY: 1,
    SELL: 2,
};

export const OFFER_TYPE_TEXT = {
    [OFFER_TYPE.BUY]: '購入',
    [OFFER_TYPE.SELL]: '販売',
}

export const OFFER_TYPE_OPTIONS = Object.values(OFFER_TYPE).map(value => ({
    value,
    label: OFFER_TYPE_TEXT[value],
}))
