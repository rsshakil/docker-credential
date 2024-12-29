<script setup>
import {PRODUCT_ACCOUNT_ITEM_TYPE} from "@/Config/ProductAccountItemType.js";
import {Link} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {copyText} from "@/Utils/CopyText.js";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    isShowProductAccountItem: Boolean,
    seller: Boolean,
    buyer: Boolean,
})
</script>

<template>
    <q-card>
        <q-card-section>
            <Link :href="route('admin.users.show', {user: props.user.id})" class="text-body1">
                {{ props.seller ? 'Seller' : (props.buyer ? 'Buyer' : '') }}: {{ props.user.name }}
            </Link>
        </q-card-section>
        <q-list>
            <slot />

            <q-separator />

            <template v-for="item in props.user.user_product_accounts[0].user_product_account_items">
                <q-item>
                    <q-item-section>{{ item.product_account_item.name }}</q-item-section>
                    <q-item-section v-if="isShowProductAccountItem">
                        <template v-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.TEXT">
                            <div>
                                <q-btn
                                    icon="content_copy" color="secondary" dense rounded size="sm"
                                    @click="copyText($q, item.value)"
                                />
                                {{ item.value }}
                            </div>
                        </template>
                        <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.SELECT">
                            <div>
                                <q-btn
                                    icon="content_copy" color="secondary" dense rounded size="sm"
                                    @click="copyText($q, item.product_account_item_options[0].name)"
                                />
                                {{ item.product_account_item_options[0].name }}
                            </div>
                        </template>
                        <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.CHECKBOX">
                            <div>
                                <q-btn
                                    icon="content_copy" color="secondary" dense rounded size="sm"
                                    @click="copyText($q, item.product_account_item_options.map(e => e.name).join(','))"
                                />
                                {{ item.product_account_item_options.map(e => e.name).join(',') }}
                            </div>
                        </template>
                        <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.RADIO">
                            <div>
                                <q-btn
                                    icon="content_copy" color="secondary" dense rounded size="sm"
                                    @click="copyText($q, item.product_account_item_options[0].name)"
                                />
                                {{ item.product_account_item_options[0].name }}
                            </div>
                        </template>
                        <template v-else-if="item.product_account_item.type === PRODUCT_ACCOUNT_ITEM_TYPE.IMAGE">
                            <q-img
                                :src="item.value_url"
                                style="max-width: 300px;"
                            />
                        </template>
                    </q-item-section>
                </q-item>
            </template>
        </q-list>
    </q-card>
</template>
