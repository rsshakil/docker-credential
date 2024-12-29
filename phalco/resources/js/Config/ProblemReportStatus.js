export const PROBLEM_REPORT_STATUS = {
    NONE: 1,
    NEED_CONTACT: 2,
    IN_CONTACT: 3,
    DONE: 4,
};

export const PROBLEM_REPORT_STATUS_TEXT = {
    [PROBLEM_REPORT_STATUS.NONE]: '報告なし',
    [PROBLEM_REPORT_STATUS.NEED_CONTACT]: '要対応',
    [PROBLEM_REPORT_STATUS.IN_CONTACT]: '対応中',
    [PROBLEM_REPORT_STATUS.DONE]: '対応完了',
}

export const PROBLEM_REPORT_STATUS_OPTIONS = Object.values(PROBLEM_REPORT_STATUS).map(value => ({
    value,
    label: PROBLEM_REPORT_STATUS_TEXT[value],
}))
