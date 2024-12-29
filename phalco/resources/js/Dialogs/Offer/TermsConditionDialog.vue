<script setup>
import { ref } from "vue";
import { useDialogPluginComponent } from 'quasar'

const props = defineProps({
    list: {
        type: Array,
        default() {
            return [
                {
                    id: 1,
                    title: "DummyTerms",
                    description: "Dummy",
                    isSelected: true,
                },
            ];
        },
    }
});

defineEmits([
    ...useDialogPluginComponent.emits
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const termsItems = ref(JSON.parse(JSON.stringify(props.list)));
const update = (e) => {
    const index = Array.isArray(termsItems.value) ? termsItems.value.findIndex((item) => item.id === e.id) : -1;

    if (index !== -1 && termsItems.value) {
        termsItems.value[index] = { ...termsItems.value[index], ...e };
    }
};

const add = () => {
    onDialogOK(termsItems);
};
</script>
<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-dialog-plugin width-modal">
            <q-card-section
                unelevated
                flat
                class="items-center justify-start row bg-primary text-white card-payment-item-list"
            >
                <div class="text-subtitle2">Terms condition</div>
            </q-card-section>

            <q-separator />

            <q-card-section style="max-height: 50vh" class="scroll">
                <q-card
                    class="my-card q-mb-md"
                    v-for="(item) in termsItems"
                    :key="item.id"
                >
                    <q-card-section
                        class="row items-center justify-start bg-primary text-white no-padding"
                    >
                        <q-checkbox
                            v-model="item.isSelected"
                            @click="update(item)"
                            color="orange"
                            size="md"
                        />
                        <div class="text-subtitle2">{{ item.title }}</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="text-body2">{{ item.title }}</div>
                        <div class="text-body2">dummmy</div>
                    </q-card-section>
                </q-card>
            </q-card-section>

            <q-card-actions align="right" class="text-primary">
                <q-btn
                    label="キャンセル"
                    class="button-cancel"
                    @click="onDialogCancel"
                    v-close-popup
                />
                <q-btn label="追加" class="button-submit" @click="add" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
