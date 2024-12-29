import {copyToClipboard} from "quasar";

export const copyText = async (quasar, text) => {
    try {
        await copyToClipboard(text)
        quasar.notify({
            message: 'クリップボードにコピーしました',
        })
    } catch (error) {
        quasar.notify({
            type: 'negative',
            message: 'クリップボードにコピーできません',
        })
    }
}
