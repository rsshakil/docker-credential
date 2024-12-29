<script setup>
import { useDialogPluginComponent } from "quasar";
const props = defineProps({
    title: {
        type: String,
        default: "Confirmation",
    },
    message: {
        type: String,
        required: true,
    },
});
defineEmits([...useDialogPluginComponent.emits]);
const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent();
</script>
<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-dialog-plugin width-modal">
            <q-card-section>
                <div class="text-h6">{{ title }}</div>
                <div>{{ message }}</div>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn label="キャンセル" class="button-cancel" @click="onDialogCancel"/>
                <q-btn label="確認する" class="button-confirm" @click="onDialogOK"/>
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>