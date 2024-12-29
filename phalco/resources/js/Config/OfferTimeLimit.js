export const OFFER_TIME_LIMIT = {
    TIME_30: 30,
    TIME_15: 15,
};

export const OFFER_TIME_LIMIT_TEXT = {
    [OFFER_TIME_LIMIT.TIME_30]: '30分',
    [OFFER_TIME_LIMIT.TIME_15]: '15分',
}

export const OFFER_TIME_LIMIT_OPTIONS = Object.values(OFFER_TIME_LIMIT).map(value => ({
    value,
    label: OFFER_TIME_LIMIT_TEXT[value],
}))
