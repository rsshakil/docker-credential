export const RATE_TYPE = {
    FIXED: 1,
    VARIABLE: 2,
};

export const RATE_TYPE_TEXT = {
    [RATE_TYPE.FIXED]: '固定レート',
    [RATE_TYPE.VARIABLE]: '変動レート',
}

export const RATE_TYPE_OPTIONS = Object.values(RATE_TYPE).map(value => ({
    value,
    label: RATE_TYPE_TEXT[value],
}))
