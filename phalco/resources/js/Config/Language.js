export const LANGUAGE = {
    JP: 1,
    EN: 2,
};

export const LANGUAGE_TEXT = {
    [LANGUAGE.JP]: '日本語',
    [LANGUAGE.EN]: 'English',
}

export const LANGUAGE_OPTIONS = Object.values(LANGUAGE).map(value => ({
    value,
    label: LANGUAGE_TEXT[value],
}))
