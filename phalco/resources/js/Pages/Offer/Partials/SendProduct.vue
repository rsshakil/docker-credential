<script setup>
import { OFFER_STATUS } from "@/Config/OfferStatus.js";
import Timer from "./Timer.vue";

const props = defineProps({
    offer: Object,
    userProductAccount: Object,
});
const emit = defineEmits(['sendItem']);

const productSend = () => {
    emit("sendItem", OFFER_STATUS.WAIT_PUBLISH__CHECK_PRODUCT);
}

</script>
<template>
    <q-card class="q-pa-lg q-mb-md full-width full-height flex-center" flat bordered>  

        <div class="text-center q-mb-md">
            <Timer :offer="props.offer"/>
            <p class="text-caption">※送付期限を過ぎた場合、自動でキャンセルされます。</p>
        </div>

        <div class="q-pa-md bg-grey-3 q-mb-md">
            <div class="q-ml-sm">
                <p class="q-py-sm no-margin">運営のアカウント情報</p>
            </div>
            <q-separator spaced />
            <div class="q-ml-sm">
                <div class="text-body2" v-if="offer?.admin_product_account?.admin_product_account_items" v-for="(productAccountItem) in offer?.admin_product_account?.admin_product_account_items">{{ productAccountItem.product_account_item.name }}:{{ productAccountItem.value }}</div>
            </div>
        </div>

        <div class="q-pa-md bg-amber-1 q-mb-md">
            <div class="q-ml-sm">
                <p class="q-py-sm no-margin">あなたのアカウント情報</p>
            </div>
            <q-separator spaced />
            <div class="q-ml-sm">
                <div class="text-body2" v-if="userProductAccount" v-for="(productAccountItem) in userProductAccount.user_product_account_items">{{ productAccountItem.product_account_item.name }}:{{ productAccountItem.value }}</div>
            </div>
        </div>

        <div class="flex flex-center">
            <q-btn label="送付完了" class="button-submit" rounded @click="productSend" />
        </div>

    </q-card>
</template>