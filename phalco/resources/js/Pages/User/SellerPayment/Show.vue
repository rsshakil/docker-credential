<script setup>
import {Head, router} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";
import AggregationComponent from "../AggregationComponent.vue";
import { PAYMENT_ITEM_TYPE } from "@/Config/PaymentItemType";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    userSellerPayments: Object
})

const onDelete = (userSellerPaymentId) => {
    $q.dialog({
        message: '支払先情報を削除しますか?',
        ok: {
            label: '確認'
        },
        cancel: {
            label: 'キャンセル'
        },
        persistent: true
    }).onOk(() => {
        const url =  route('seller_payment.delete', [userSellerPaymentId])
        router.delete(url, {
            onSuccess: () => {
                $q.notify({
                    message: '支払先情報を削除しました',
                });
            },
        })
    })
}

const onActive = (userSellerPayment) => {
    $q.dialog({
        message: userSellerPayment.is_active == 1?'無効にしますか?':'有効にしますか?',
        ok: {
            label: '確認'
        },
        cancel: {
            label: 'キャンセル'
        },
        persistent: true
    }).onOk(() => {
        const url =  route('seller_payment.toggle_active', [userSellerPayment.id])
        router.put(url, null, {
            onSuccess: () => {
                $q.notify({
                    message: userSellerPayment.is_active == 1?'無効にしました':'有効にしました',
                });
            },
        })
    })
}
</script>
<template>
    <Head title="支払先情報一覧" />
    <MenuLayout>
        <AggregationComponent
            :user="props.user"
            :sameUser="true"
        />
        <q-card>
            <div class="row">
                <h6 class="no-padding no-margin">支払先情報</h6>
                <q-btn
                    label="追加"
                    @click="router.visit(route('seller_payment.create'))"
                />
            </div>
            <q-list class="row" bordered v-for="(userSellerPayment, index) in props.userSellerPayments" :key="index">
                <q-card 
                    class="col-12"
                    bordered 
                    :style="userSellerPayment.is_active == 1? '':'background-color: #cbcbcb'"
                >
                    <q-expansion-item
                        expand-icon-toggle
                        expand-separator
                    >
                        <template v-slot:header>
                            <q-card class="col-6">
                                <h6 class="no-padding no-margin">{{ userSellerPayment.payment_method.name }}</h6>
                                <q-btn
                                    align="right"
                                    label="編集"
                                    @click="router.visit(route('seller_payment.edit', [userSellerPayment.id]))"
                                />
                                <q-btn
                                    align="right"
                                    label="削除"
                                    @click="onDelete(userSellerPayment.id)"
                                />
                                <q-btn
                                    align="right"
                                    :label="userSellerPayment.is_active == 1? '無効にする':'有効にする'"
                                    @click="onActive(userSellerPayment)"
                                />
                            </q-card>
                        </template>
                        <q-list bordered v-for="(item, index) in userSellerPayment.seller_payment_items" :key="index">
                            <q-item>
                                <q-item-section>
                                    {{ item.payment_item.name }}
                                </q-item-section>
                                <q-item-section>
                                    <template v-if="item.payment_item.type == PAYMENT_ITEM_TYPE.TEXT">
                                        <span>
                                            {{ item.value  }}
                                        </span>
                                    </template>
                                    <template v-if="item.payment_item.type == PAYMENT_ITEM_TYPE.SELECT||
                                        item.payment_item.type == PAYMENT_ITEM_TYPE.RADIO">
                                        <span>
                                            {{ item.payment_options[0].name  }}
                                        </span>
                                    </template>
                                    <template v-if="item.payment_item.type == PAYMENT_ITEM_TYPE.CHECKBOX">
                                        <span v-for="(userOption) in item.payment_options">
                                            {{ userOption.name  }}
                                        </span>
                                    </template>
                                    <template v-if="item.payment_item.type == PAYMENT_ITEM_TYPE.IMAGE">
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

