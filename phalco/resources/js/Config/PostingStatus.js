export const STATUS = {
    STATUS_ALL: 1,
    STATUS_IN_POSTING: 2,
    STATUS_POSTING_SCHEDULE: 3,
};

export const STATUS_TEXT = {
    [STATUS.STATUS_ALL]: 'すべて',
    [STATUS.STATUS_IN_POSTING]: '掲載期間中',
    [STATUS.STATUS_POSTING_SCHEDULE]: '掲載予定',
};

export const STATUS_OPTIONS = Object.values(STATUS).map(value => ({
    value,
    label: STATUS_TEXT[value],
}))
