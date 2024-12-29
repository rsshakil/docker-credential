<script setup>
import {PRODUCT_ACCOUNT_ITEM_TYPE} from "@/Config/ProductAccountItemType.js";

const props = defineProps({
    title: String,
    data: Array,
})
</script>

<template>
    <q-list>
        <q-item>
            <q-item-section>{{ props.title }}</q-item-section>
        </q-item>
        <q-item v-for="item in props.data" class="q-pl-lg">
            <q-item-section>{{ item.product_account_item.name }}</q-item-section>
            <q-item-section>
                <template v-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.TEXT">
                    {{ item.value }}
                </template>
                <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.SELECT">
                    {{ item.product_account_item_options[0].name }}
                </template>
                <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.CHECKBOX">
                    {{ item.product_account_item_options.map(e => e.name).join(',') }}
                </template>
                <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.RADIO">
                    {{ item.product_account_item_options[0].name }}
                </template>
                <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.IMAGE">
                    <q-img
                        :src="item.value_url"
                        style="max-width: 300px;"
                    />
                </template>
            </q-item-section>
        </q-item>
    </q-list>
</template>
