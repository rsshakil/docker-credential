<script setup>
import { useDialogPluginComponent } from 'quasar'

const props = defineProps({
    rejectForm: Object,
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
                        本人確認申請を却下します
                    </q-card-section>
                    <q-card-section>
                        <q-input
                            filled
                            type="textarea"
                            v-model="props.rejectForm.rejected_reason"
                            label="却下理由"
                            class="q-mt-md"
                            :error="!!props.rejectForm.errors.rejected_reason"
                            :error-message="props.rejectForm.errors.rejected_reason"
                            :rules="[
                                value => !!value || '必須項目',
                            ]"
                        />
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn type="submit" color="red" label="却下" />
                        <q-btn label="キャンセル" @click="onDialogCancel" />
                    </q-card-actions>
                </q-card>
            </q-form>
        </div>
    </q-dialog>
</template>
