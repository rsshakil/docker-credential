export const USER_STATUS = {
    SAMPLE: 1,
};

export const USER_STATUS_TEXT = {
    [USER_STATUS.SAMPLE]: 'サンプル',
}

export const USER_STATUS_OPTIONS = Object.values(USER_STATUS).map(value => ({
    value,
    label: USER_STATUS_TEXT[value],
}))
