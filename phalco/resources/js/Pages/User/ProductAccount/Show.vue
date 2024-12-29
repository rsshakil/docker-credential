<script setup>
import {Head, router} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";
import AggregationComponent from "../AggregationComponent.vue";
import { PRODUCT_ACCOUNT_ITEM_TYPE } from "@/Config/ProductAccountItemType";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    userProductAccounts: Object
})

const onDelete = (userProductAccountId) => {
    $q.dialog({
        message: '商品送付先を削除しますか?',
        ok: {
            label: '確認'
        },
        cancel: {
            label: 'キャンセル'
        },
        persistent: true
    }).onOk(() => {
        const url =  route('user_product_account.delete', [userProductAccountId])
        router.delete(url, {
            onSuccess: () => {
                $q.notify({
                    message: '商品送付先を削除しました',
                });
            },
        })
    })
}
</script>
<template>
    <Head title="商品送付先一覧" />
    <MenuLayout>
        <AggregationComponent
            :user="props.user"
            :sameUser="true"
        />
        <q-card>
            <div class="row">
                <h6 class="no-padding no-margin">商品送付先情報</h6>
                <q-btn
                    label="追加"
                    @click="router.visit(route('user_product_account.create'))"
                />
            </div>

            <q-list class="row" bordered v-for="(userProductAccount, index) in props.userProductAccounts" :key="index">
                <q-card class="col-12" bordered>
                    <q-expansion-item
                        expand-icon-toggle
                        expand-separator
                    >
                        <template v-slot:header>
                            <q-card class="col-6">
                                <h6 class="no-padding no-margin">{{ userProductAccount.product.name }}</h6>
                                <q-btn
                                    align="right"
                                    label="編集"
                                    @click="router.visit(route('user_product_account.edit', [userProductAccount.id]))"
                                />
                                <q-btn
                                    align="right"
                                    label="削除"
                                    @click="onDelete(userProductAccount.id)"
                                />
                            </q-card>
                        </template>
                        <q-list bordered v-for="(item, index) in userProductAccount.user_product_account_items" :key="index">
                            <q-item>
                                <q-item-section>
                                    {{ item.product_account_item.name }}
                                </q-item-section>
                                <q-item-section>
                                    <template v-if="item.product_account_item.type == PRODUCT_ACCOUNT_ITEM_TYPE.TEXT">
                                        <span>
                                            {{ item.value  }}
                                        </span>
                                    </template>
                                    <template v-else-if="item.product_account_item.type == PRODUCT_ACCOUNT_ITEM_TYPE.SELECT||
                                        item.product_account_item.type == PRODUCT_ACCOUNT_ITEM_TYPE.RADIO">
                                        <span>
                                            {{ item.product_account_item_options[0].name  }}
                                        </span>
                                    </template>
                                    <template v-else-if="item.product_account_item.type == PRODUCT_ACCOUNT_ITEM_TYPE.CHECKBOX">
                                        <span v-for="(userOption) in item.product_account_item_options">
                                            {{ userOption.name  }}
                                        </span>
                                    </template>
                                    <template v-else-if="item.product_account_item.type == PRODUCT_ACCOUNT_ITEM_TYPE.IMAGE">
                                        <q-img
                                            v-if="item.value_url"
                                            :src="item.value_url"
                                            style="max-width: 100px;"
                                            class="q-mt-sm"
                                        />
                                    </template>
                                </q-item-section>
                            </q-item>
                        </q-list>
                    </q-expansion-item>
                </q-card>
            </q-list>
        </q-card>
    </MenuLayout>
</template>

