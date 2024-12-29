<script setup>
import { Head, router } from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import { OFFER_TYPE_TEXT } from "@/Config/OfferTyoe.js";
import { OFFER_TIME_LIMIT_TEXT } from "@/Config/OfferTimeLimit.js";
import { OFFER_STATUS_TEXT, OFFER_STATUS, CANCELABLE_OFFER_STATUSES, EDITABLE_OFFER_STATUSES } from "@/Config/OfferStatus.js";
import { computed } from "vue";
import { useQuasar } from "quasar";
import { RATE_TYPE_TEXT } from "@/Config/RateType.js";
import SendProduct from "./Partials/SendProduct.vue";
import ConfirmDialog from "@/Dialogs/ConfirmDialog.vue";

const $q = useQuasar();

const props = defineProps({
    offer: Object,
    userProductAccount: Object,
    userPaymentMethods: Object
});

const canCancel = computed(() => CANCELABLE_OFFER_STATUSES.includes(props.offer.offer_status));
const canEdit = computed(() => EDITABLE_OFFER_STATUSES.includes(props.offer.offer_status));

const openConfirmationDialog = async (toRoute) => {
    $q.dialog({
        component: ConfirmDialog,
        componentProps: {
            title: "確認",
            message: "本気ですか?"
        },
    }).onOk((e) => {
        router.post(route(toRoute, [props.offer.offer_no]), null, {
            onSuccess: (response) => {
                $q.notify({
                    message: "オファーのステータスが正常に更新されました",
                });
            },
        });
    });
}

const activePaymentMethods = computed(() => {
    const paymentMethodList = props.userPaymentMethods[props.offer.offer_type][props.offer.currency_id] ?? [];
    return paymentMethodList.filter((item) => item.is_active) ?? [];
});
</script>

<template>
    <Head title="オファー詳細" />

    <MenuLayout>
        <div class="component-center-body">
            <div class="row">
                <div class="col-12">
                    <h5 class="no-margin q-pb-md page-title">オファー詳細</h5>
                    <h6 class="no-margin">{{ OFFER_STATUS_TEXT[offer.offer_status] }}</h6>
                </div>
            </div>

            <div class="row">
              
                <div class="col-md-7 col-lg-7 col-xl-7 col-xs-12 q-pa-sm">
                    <q-card class="">

                        <q-card-section>
                            <div class="q-gutter-md">
                                <div class="row justify-between">
                                    <div class="text-caption text-black  title-left-text">オファーID</div>
                                    <div class="text-body1 title-right-text">{{ offer.id }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black  title-left-text">オファー種類</div>
                                    <div class="text-body1 title-right-text">{{ OFFER_TYPE_TEXT[offer.offer_type] }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black  title-left-text">商品種類</div>
                                    <div class="text-body1 title-right-text">{{ offer.product.name }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black  title-left-text">総数量</div>
                                    <div class="text-body1 title-right-text">{{ offer.request_amount.toLocaleString() }} {{ offer.product.unit }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">掲載量</div>
                                    <div class="text-body1 title-right-text">{{ offer.amount.toLocaleString() }} {{ offer.product.unit }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">レート設定</div>
                                    <div class="text-body1 title-right-text">{{ RATE_TYPE_TEXT[offer.rate_type] }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">レート</div>
                                    <div class="text-body1 title-right-text">{{ offer.display_rate }} {{ offer.currency.code }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">取引可能範囲</div>
                                    <div class="text-body1 title-right-text">{{ offer.min_amount.toLocaleString() }} ~ {{ offer.max_amount ? offer.max_amount.toLocaleString() : '' }} {{ offer.product.unit }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">支払い期間</div>
                                    <div class="text-body1 title-right-text">{{ OFFER_TIME_LIMIT_TEXT[offer.time_limit] }}</div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">商品送付先情報</div>
                                    <div class="text-body1 title-right-text">
                                        <div class="text-body2" v-if="userProductAccount" v-for="(productAccountItem) in userProductAccount.user_product_account_items">{{ productAccountItem.product_account_item.name }}:{{ productAccountItem.value }}</div>
                                    </div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">運営のアカウント情報</div>
                                    <div class="text-body1 title-right-text">
                                        <div class="text-body2" v-if="offer?.admin_product_account" v-for="(productAccountItem) in offer.admin_product_account.admin_product_account_items">{{ productAccountItem.product_account_item.name }}:{{ productAccountItem.value }}</div>
                                    </div>
                                </div>


                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">支払方法</div>
                                    <div class="text-body1 title-right-text">
                                        <div class="payment-method-list">
                                            <q-badge
                                                v-if="activePaymentMethods"
                                                v-for="(paymentMethod) in activePaymentMethods"
                                                :key="paymentMethod?.id"
                                                :label="paymentMethod?.payment_method?.name"
                                                class="q-pa-sm payment-method q-ma-xs"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">相手に求める条件</div>
                                    <div class="text-body1 title-right-text"></div>
                                </div>

                                <div class="row justify-between">
                                    <div class="text-caption text-black title-left-text">備考</div>
                                    <div class="text-body1 title-right-text">{{ offer.note }}</div>
                                </div>
                            </div>
                        </q-card-section>
                        <q-card-actions class=" row justify-center items-center q-gutter-sm q-pa-md">
                            <q-btn label="掲載キャンセル" v-if="canCancel" @click="openConfirmationDialog('offers.cancel')" class="button-cancel" rounded/>
                            <q-btn label="掲載一時停止" v-if="props.offer.offer_status === OFFER_STATUS.PUBLISHING__ACTIVE" @click="openConfirmationDialog('offers.pause')" class="button-suspend" rounded/>
                            <q-btn label="掲載再開" v-if="props.offer.offer_status === OFFER_STATUS.PUBLISHING__PAUSE" @click="openConfirmationDialog('offers.active')" class="button-suspend" rounded/>
                            <q-btn label="編集" v-if="canEdit" @click="router.get(route('offers.edit', { offer: props.offer.id}))" class="button-edit" rounded/>
                        </q-card-actions>
                    </q-card>
                </div>
                <div class="col-md-5 col-lg-5 col-xl-5 col-xs-12 q-pa-sm">
                    <SendProduct v-if="offer.offer_status === OFFER_STATUS.WAIT_PUBLISH__WAIT_PRODUCT"
                        :offer="offer"
                        @sendItem="openConfirmationDialog('offers.send_product')"
                        :userProductAccount="userProductAccount"
                    />
                    
                </div>
            </div>
        </div>
    </MenuLayout>
</template>
<style>
@import "../../../sass/card.scss";
@import "../../../sass/button.scss";
@import "../../../sass/text.scss";
@import "../../../sass/input.scss";
</style>