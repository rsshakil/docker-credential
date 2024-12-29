<script setup>
import { computed } from "vue";
import { OFFER_TYPE } from "@/Config/OfferTyoe.js";

const props = defineProps({
    offerType: Number,
});
const paymentItems = defineModel(Array);
const activePaymentItems = computed(() =>
    paymentItems.value?.filter((item) => item.is_active) ?? []
);
</script>
<template>
    <q-card class="q-mb-md" unelevated flat v-for="(item) in activePaymentItems" :key="item.id">
        <q-card-section class="row items-center justify-between text-black card-payment-item-list">
            <div class="text-subtitle2">{{ item.payment_method.name }}</div>
        </q-card-section>

        <q-card-section v-if="offerType == OFFER_TYPE.SELL">
            <div class="text-body2" v-if="item.seller_payment_items" v-for="(paymentOptions) in item.seller_payment_items">
                {{ paymentOptions.payment_item.name }}:{{ paymentOptions.value }}
            </div>
        </q-card-section>
    </q-card>
</template>
