export const ROLE = {
    SV: 1,
    OP: 2,
};

export const ROLE_TEXT = {
    [ROLE.SV]: '全権限',
    [ROLE.OP]: 'オペレーター権限',
}

export const ROLE_OPTIONS = Object.values(ROLE).map(value => ({
    value,
    label: ROLE_TEXT[value],
}))
