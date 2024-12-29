<script setup>
import { ref } from "vue";
import { useDialogPluginComponent } from 'quasar'
import { RATE_TYPE } from "@/Config/RateType.js";
import { OFFER_TYPE } from "@/Config/OfferTyoe.js";

const props = defineProps({
    form: Object,
    currencyCode: String,
    productUnit: String,
    displayAmount: Number,
});

defineEmits([
    ...useDialogPluginComponent.emits
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()
const activePaymentMethods = ref(props.form.payment_methods.filter(e =>e.is_active));
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-dialog-plugin width-modal">
            <q-card-section>
                この内容で掲載します
            </q-card-section>
            <q-card-section>
                <q-list bordered separator>
                    <q-item>
                        <q-item-section>
                            <q-item-label>レート</q-item-label>
                        </q-item-section>
                        <q-item-section>
                            {{ props.form.rate }} {{ props.form.rate_type.value === RATE_TYPE.FIXED ? props.currencyCode : '%' }}/{{ props.productUnit }}
                        </q-item-section>
                    </q-item>
                    <q-item>
                        <q-item-section>
                            <q-item-label>{{ props.form.offer_type === OFFER_TYPE.BUY ? '購入したい量' : '販売したい量（運営への送付量）' }}</q-item-label>
                        </q-item-section>
                        <q-item-section>
                            {{ props.form.amount }} {{ props.productUnit }}
                        </q-item-section>
                    </q-item>
                    <q-item>
                        <q-item-section>
                            <q-item-label>掲載量</q-item-label>
                        </q-item-section>
                        <q-item-section>
                            {{ props.displayAmount }}
                        </q-item-section>
                    </q-item>
                    <q-item>
                        <q-item-section>
                            <q-item-label>トレード可能範囲</q-item-label>
                        </q-item-section>
                        <q-item-section>
                            <div>最小 {{ props.form.min_amount }} {{ props.currencyCode }}</div>
                            <div v-if="!!props.form.max_amount">最大 {{ props.form.max_amount }} {{ props.currencyCode }}</div>
                        </q-item-section>
                    </q-item>
                    <q-item>
                        <q-item-section>
                            <q-item-label>支払い方法</q-item-label>
                        </q-item-section>
                        <q-item-section>
                            <div class="payment-method-list">
                                <q-badge
                                    v-if="activePaymentMethods"
                                    v-for="(paymentMethod) in activePaymentMethods"
                                    :key="paymentMethod.id"
                                    :label="paymentMethod.payment_method.name"
                                    class="q-pa-sm payment-method q-ma-xs"
                                />
                            </div>

                            <p class="pay-notify">この支払い方法はすべてのオファーに影響します</p>
                        </q-item-section>
                    </q-item>
                </q-list>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn class="button-cancel" label="キャンセル" @click="onDialogCancel" />
                <q-btn class="button-add" label="掲載" @click="onDialogOK" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
