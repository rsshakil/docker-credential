<script setup>
import { ref, watch } from "vue";
import { useDialogPluginComponent } from 'quasar'
import { OFFER_TYPE } from "@/Config/OfferTyoe.js";

const props = defineProps({
    list: Array,
    offerType: Number,
});

defineEmits([
    ...useDialogPluginComponent.emits
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const paymentMethodsList = ref(JSON.parse(JSON.stringify(props.list)));
const errorMessage = ref('');
const update = (e) => {
    const index = Array.isArray(paymentMethodsList.value) ? paymentMethodsList.value.findIndex((item) => item.id === e.id) : -1;
    if (index !== -1 && paymentMethodsList.value) {
        paymentMethodsList.value[index] = { ...paymentMethodsList.value[index], ...e };
    }
};

const addPayemntMethod = () => {
    let selectedPaymentItems = paymentMethodsList.value && paymentMethodsList.value.filter((item) => item.is_active);
    if (selectedPaymentItems.length > 0) {
        onDialogOK([...paymentMethodsList.value]);
        errorMessage.value = "";
    } else {
        errorMessage.value = "お支払い方法を選択してください";
    }
};
</script>
<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-dialog-plugin width-modal">
            <q-card-section unelevated flat class="items-center justify-start row bg-primary text-white card-payment-item-list">
                <div class="text-subtitle2">支払い方法</div>
            </q-card-section>

            <q-separator />

            <q-card-section
                unelevated
                flat
                style="max-height: 50vh"
                class="scroll"
            >
                <q-card
                    class="my-card q-mb-md"
                    v-for="(item) in paymentMethodsList"
                    :key="item.id"
                >
                    <q-card-section
                        class="row items-center justify-start bg-primary text-white no-padding"
                    >
                        <q-checkbox
                            :model-value="item.is_active===1"
                            :debounce="1000"
                            @click="update({...item,is_active:item.is_active===1?0:1})"
                            color="orange"
                            size="md"
                            :error="!!errorMessage"
                        />
                        <div class="text-subtitle2">{{ item.payment_method.name }}</div>
                    </q-card-section>

                    <q-card-section v-if="offerType == OFFER_TYPE.SELL">
                        <div class="text-body2" v-if="item.seller_payment_items" v-for="(paymentOptions) in item.seller_payment_items">
                            {{ paymentOptions.payment_item.name }}:{{ paymentOptions.value }}
                        </div>
                    </q-card-section>
                </q-card>
                <div v-if="errorMessage" class="text-negative">{{ errorMessage }}</div>
            </q-card-section>

            <q-card-actions align="right" class="text-primary">
                <q-btn
                    label="キャンセル"
                    class="button-cancel"
                    @click="onDialogCancel"
                    v-close-popup
                />
                <q-btn
                    label="追加"
                    class="button-submit"
                    @click="addPayemntMethod"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
