<script setup>
import { computed } from "vue";
import { OFFER_TYPE } from "@/Config/OfferTyoe.js";

const value = defineModel(Number);
const isBuySelected = computed(() => value.value === OFFER_TYPE.BUY);
const isSellSelected = computed(() => value.value === OFFER_TYPE.SELL);
</script>
<template>
    <q-btn-group outline class="offer-type-btn-group">
        <q-btn label="購入" :class="{ 'button-buy' : isBuySelected }" @click="value = OFFER_TYPE.BUY"/>
        <q-btn label="販売" :class="{ 'button-sell' : isSellSelected }" @click="value = OFFER_TYPE.SELL"/>
    </q-btn-group>
</template>
