<script setup>
import { useDialogPluginComponent } from 'quasar'

const props = defineProps({
    toStopForm: Object,
})

defineEmits([
    ...useDialogPluginComponent.emits
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const onSubmit = () => {
    onDialogOK();
}
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <div>
            <q-form @submit="onSubmit">
                <q-card class="q-dialog-plugin">
                    <q-card-section>
                        掲載差し止め理由
                    </q-card-section>
                    <q-card-section class="q-gutter-x-md">
                        <q-btn
                            label="詐欺"
                            @click="() => {props.toStopForm.reason = '詐欺'}"
                        />
                        <q-btn
                            label="悪質なユーザー"
                            @click="() => {props.toStopForm.reason = '悪質なユーザー'}"
                        />
                    </q-card-section>
                    <q-card-section>
                        <q-input
                            filled
                            type="textarea"
                            v-model="props.toStopForm.reason"
                            label="理由"
                            class="q-mt-md"
                            :error="!!props.toStopForm.errors.reason"
                            :error-message="props.toStopForm.errors.reason"
                            :rules="[
                                value => !!value || '必須項目',
                            ]"
                        />
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn type="submit" color="red" label="掲載差し止め" />
                        <q-btn label="キャンセル" @click="onDialogCancel" />
                    </q-card-actions>
                </q-card>
            </q-form>
        </div>
    </q-dialog>
</template>
