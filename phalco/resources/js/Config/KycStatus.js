export const KYC_STATUS = {
    UNCONFIRMED: 1,
    REVIEWING: 2,
    REJECTED: 3,
    CONFIRMED: 4,
};

export const KYC_STATUS_TEXT = {
    [KYC_STATUS.UNCONFIRMED]: '未確認',
    [KYC_STATUS.REVIEWING]: '審査中',
    [KYC_STATUS.REJECTED]: '却下',
    [KYC_STATUS.CONFIRMED]: '確認済',
}

export const KYC_STATUS_OPTIONS = Object.values(KYC_STATUS).map(value => ({
    value,
    label: KYC_STATUS_TEXT[value],
}))
