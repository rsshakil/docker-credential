<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {OFFER_TYPE_TEXT} from "@/Config/OfferTyoe.js";
import {useQuasar} from "quasar";
import {PROBLEM_REPORT_STATUS_TEXT} from "@/Config/ProblemReportStatus.js";
import {computed} from "vue";
import {TRADE_STATUS, TRADE_STATUS_TEXT} from "@/Config/TradeStatus.js";
import User from "@/Components/Admin/OfferTrade/User.vue";
import ProductAccountData from "@/Components/Admin/OfferTrade/ProductAccountData.vue";
import {PAYMENT_ITEM_TYPE} from "@/Config/PaymentItemType.js";
import {RATE_TYPE_TEXT} from "@/Config/RateType.js";
import TradeStopDialog from "@/Dialogs/Admin/Trade/TradeStopDialog.vue";
import {copyText} from "@/Utils/CopyText.js";

const $q = useQuasar()

const props = defineProps({
    trade: Object,
    sendAdminProductAccounts: Array,
})

const isSellerShowProductAccountItem = computed(() => ([
    TRADE_STATUS.REQUEST__CHECK_PRODUCT,
    TRADE_STATUS.CANCEL__RETURNING,
    TRADE_STATUS.STOP__RETURN,
].includes(props.trade.trade_status)))

const isBuyerShowProductAccountItem = computed(() => ([
    TRADE_STATUS.PAID__SEND_PRODUCT,
    TRADE_STATUS.STOP__SEND,
].includes(props.trade.trade_status)))

const isShowReturnAmount = computed(() => [
    TRADE_STATUS.CANCEL__WAIT_RETURN,
    TRADE_STATUS.CANCEL__RETURNING,
    TRADE_STATUS.CANCEL__DONE,
].includes(props.trade.trade_status))

// トレード停止可能か
const canStop = computed(() => ![
    TRADE_STATUS.DONE,
    TRADE_STATUS.CANCEL__NO_RETURN,
    TRADE_STATUS.CANCEL__DONE,
    TRADE_STATUS.STOP,
    TRADE_STATUS.STOP__RETURN,
    TRADE_STATUS.STOP__SEND,
    TRADE_STATUS.STOP__DONE,
].includes(props.trade.trade_status))

// トレード停止解除可能か
const canStopCancel = computed(() => [
    TRADE_STATUS.STOP,
    TRADE_STATUS.STOP__RETURN,
    TRADE_STATUS.STOP__SEND,
].includes(props.trade.trade_status))

// tレード停止中か
const isStop = computed(() => [
    TRADE_STATUS.STOP,
    TRADE_STATUS.STOP__RETURN,
    TRADE_STATUS.STOP__SEND,
    TRADE_STATUS.STOP__DONE,
].includes(props.trade.trade_status))

// 未支払：支払待ち
const toUnpaidWaitPay = () => {
    router.post(route('admin.trades.to_unpaid_wait_pay', props.trade.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'トレードステータスを変更しました',
            })
        },
    })
}

// 支払済：商品送付待ち
const toPaidSendProduct = () => {
    router.post(route('admin.trades.to_paid_send_product', props.trade.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'トレードステータスを変更しました',
            })
        },
    })
}

// トレード完了
const toDoneForm = useForm({
    admin_product_account_id: null,
})
const toDone = () => {
    toDoneForm.post(route('admin.trades.to_done', props.trade.id), {
        onSuccess: () => {
            $q.notify({
                message: 'トレードステータスを変更しました',
            })
        },
    })
}

// キャンセル：返送中
const toCancelReturning = () => {
    router.post(route('admin.trades.to_cancel_returning', props.trade.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'トレードステータスを変更しました',
            })
        },
    })
}

// キャンセル：完了
const toCancelDoneForm = useForm({
    admin_product_account_id: null,
})
const toCancelDone = () => {
    toCancelDoneForm.post(route('admin.trades.to_cancel_done', props.trade.id), {
        onSuccess: () => {
            $q.notify({
                message: 'トレードステータスを変更しました',
            })
        },
    })
}

// 停止中
const toStopForm = useForm({
    reason: '',
})
const toStop = () => {
    $q.dialog({
        component: TradeStopDialog,
        componentProps: {toStopForm},
    }).onOk(() => {
        toStopForm.post(route('admin.trades.to_stop', props.trade.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                $q.notify({
                    message: 'オファーステータスを変更しました',
                })
            },
            onError: (error) => {
                $q.notify({
                    type: 'negative',
                    message: 'トレード停止理由を確認してください',
                })
            }
        })
    })
}

// 停止解除
const toStopCancel = () => {
    router.post(route('admin.trades.to_stop_cancel', props.trade.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// 停止中：送付中
const toStopSend = () => {
    router.post(route('admin.trades.to_stop_send', props.trade.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        }
    })
}

// 停止中：返送中
const toStopReturn = () => {
    router.post(route('admin.trades.to_stop_return', props.trade.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        }
    })
}

// 停止済
const toStopDoneForm = useForm({
    admin_product_account_id: null,
})
const toStopDone = () => {
    toStopDoneForm.post(route('admin.trades.to_stop_done', props.trade.id), {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        }
    })
}
</script>

<template>
    <Head title="トレード詳細" />

    <AuthenticatedLayout>
        <template #header>
            トレード詳細
        </template>

        <q-banner class="bg-light-blue text-white full-width">
            <template v-if="props.trade.trade_status === TRADE_STATUS.REQUEST__WAIT_PRODUCT">
                商品送付待ち
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.REQUEST__CHECK_PRODUCT">
                受取商品が総数量と同量であるか確認し承認をしてください。
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.UNPAID__WAIT_PAY">
                支払待ち
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.UNPAID__CHECK_PAY">
                支払確認待ち
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.PAID__WAIT_PRODUCT">
                商品送付待ち
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.PAID__SEND_PRODUCT">
                送付対応が終了したら、送付に使用したアカウントを選択して、送付完了を押してください
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.DONE">
                トレード完了
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__REQUEST">
                申請中
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__WAIT_RETURN">
                返送の準備ができたら返送開始を押してください
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__RETURNING">
                返送対応が終了したら、送付に使用したアカウントを選択して、返送完了を押してください
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__DONE">
                返送済
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__NO_RETURN">
                返送不要
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.STOP">
                対応方法を選択し、対応完了を押してください
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.STOP__RETURN">
                対応方法を選択し、対応完了を押してください
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.STOP__SEND">
                対応方法を選択し、対応完了を押してください
            </template>
            <template v-else-if="props.trade.trade_status === TRADE_STATUS.STOP__DONE">
                停止済
            </template>
        </q-banner>

        <div class="row justify-end q-col-gutter-x-md q-mt-md">
            <div class="col-auto">
                <q-btn
                    label="オファーへ"
                    @click="router.visit(route('admin.offers.show', props.trade.offer_id))"
                />
            </div>
            <div class="col-auto">
                <q-btn
                    label="履歴の閲覧"
                    @click="router.visit(route('admin.trade_histories.index', props.trade.id))"
                />
            </div>
        </div>

        <div class="row q-col-gutter-md q-mt-md">
            <div class="col-6">

                <q-badge
                    :label="TRADE_STATUS_TEXT[props.trade.trade_status]"
                    color="grey"
                    class="q-pa-sm"
                    style="font-size: 1rem"
                />
                <q-badge
                    color="grey"
                    class="q-pa-sm q-ml-md"
                    style="font-size: 1rem"
                >
                    Buyer問題報告ステータス<br>{{ PROBLEM_REPORT_STATUS_TEXT[props.trade.buyer_problem_report_status] }}
                </q-badge>
                <q-badge
                    color="grey"
                    class="q-pa-sm q-ml-md"
                    style="font-size: 1rem"
                >
                    Seller問題報告ステータス<br>{{ PROBLEM_REPORT_STATUS_TEXT[props.trade.seller_problem_report_status] }}
                </q-badge>

                <template v-if="props.trade.trade_status === TRADE_STATUS.REQUEST__CHECK_PRODUCT">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="承認"
                            color="green"
                            @click="toUnpaidWaitPay"
                        />
                        <q-btn
                            label="却下"
                            color="red"
                            @click=""
                        />
                    </div>
                </template>
                <template v-else-if="props.trade.trade_status === TRADE_STATUS.PAID__WAIT_PRODUCT">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="送付開始"
                            color="green"
                            @click="toPaidSendProduct"
                        />
                    </div>
                </template>
                <template v-else-if="props.trade.trade_status === TRADE_STATUS.PAID__SEND_PRODUCT">
                    <div class="row q-gutter-md items-center q-mt-md">
                        <div class="col">
                            <q-select
                                filled
                                dense
                                v-model="toDoneForm.admin_product_account_id"
                                label="送付に使用したアカウント"
                                :options="props.sendAdminProductAccounts"
                                option-value="id"
                                option-label="name"
                                map-options
                                emit-value
                                hide-bottom-space
                                :error="!!toDoneForm.errors.admin_product_account_id"
                                :error-message="toDoneForm.errors.admin_product_account_id"
                            />
                        </div>
                        <div class="col-auto">
                            <q-btn
                                label="送付完了"
                                color="green"
                                @click="toDone"
                            />
                        </div>
                    </div>
                </template>
                <template v-else-if="props.trade.trade_status === TRADE_STATUS.DONE">
                    <div class="row q-gutter-md items-center q-mt-md">
                        <div class="col-auto">
                            返送に使用したアカウント：{{ props.trade.latest_trade_history.admin_product_account.name }}
                        </div>
                    </div>
                </template>
                <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__WAIT_RETURN">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="返送開始"
                            color="green"
                            @click="toCancelReturning"
                        />
                    </div>
                </template>
                <template v-else-if="props.trade.trade_status === TRADE_STATUS.CANCEL__RETURNING">
                    <div class="row q-gutter-md items-center q-mt-md">
                        <div class="col">
                            <q-select
                                filled
                                dense
                                v-model="toCancelDoneForm.admin_product_account_id"
                                label="送付に使用したアカウント"
                                :options="props.sendAdminProductAccounts"
                                option-value="id"
                                option-label="name"
                                map-options
                                emit-value
                                hide-bottom-space
                                :error="!!toCancelDoneForm.errors.admin_product_account_id"
                                :error-message="toCancelDoneForm.errors.admin_product_account_id"
                            />
                        </div>
                        <div class="col-auto">
                            <q-btn
                                label="送付完了"
                                color="green"
                                @click="toCancelDone"
                            />
                        </div>
                    </div>
                </template>
                <template v-else-if="props.trade.trade_status === TRADE_STATUS.STOP">
                    <div class="row q-gutter-md items-center q-mt-md">
                        <div class="col-auto">
                            <q-btn
                                label="対応不要"
                                color="green"
                                @click="toStopDone"
                            />
                        </div>
                        <div class="col-auto">
                            <q-btn
                                label="Buyerへ送付"
                                no-caps
                                color="green"
                                @click="toStopSend"
                            />
                        </div>
                        <div class="col-auto">
                            <q-btn
                                label="Sellerへ返送"
                                no-caps
                                color="green"
                                @click="toStopReturn"
                            />
                        </div>
                    </div>
                </template>
                <template v-else-if="[TRADE_STATUS.STOP__SEND, TRADE_STATUS.STOP__RETURN].includes(props.trade.trade_status)">
                    <div class="row q-gutter-md items-center q-mt-md">
                        <div class="col">
                            <q-select
                                filled
                                dense
                                v-model="toStopDoneForm.admin_product_account_id"
                                label="送付に使用したアカウント"
                                :options="props.sendAdminProductAccounts"
                                option-value="id"
                                option-label="name"
                                map-options
                                emit-value
                                hide-bottom-space
                                :error="!!toStopDoneForm.errors.admin_product_account_id"
                                :error-message="toStopDoneForm.errors.admin_product_account_id"
                            />
                        </div>
                        <div class="col-auto">
                            <q-btn
                                label="対応完了"
                                no-caps
                                color="green"
                                @click="toStopDone"
                            />
                        </div>
                    </div>
                </template>

                <div class="row q-mt-md q-col-gutter-x-md">
                    <div class="col">
                        <User
                            seller
                            :user="props.trade.seller"
                            :is-show-product-account-item="isSellerShowProductAccountItem"
                        >
                            <q-item>
                                <q-item-section>商品種類</q-item-section>
                                <q-item-section>{{ props.trade.product.name }}</q-item-section>
                            </q-item>

                            <q-item v-if="isShowReturnAmount">
                                <q-item-section>返送量</q-item-section>
                                <q-item-section>
                                    <div>
                                        <q-btn
                                            icon="content_copy" color="secondary" dense rounded size="sm"
                                            @click="copyText($q, props.trade.return_amount)"
                                        />
                                        {{ props.trade.return_amount.toLocaleString() }} {{ props.trade.product.unit }}</div>
                                    <div style="font-size: .675rem">総数量 {{ props.trade.trade_amount.toLocaleString() }} - 返送手数料 {{ props.trade.product.refund_fee.toLocaleString() }}</div>
                                </q-item-section>
                            </q-item>
                            <q-item v-else>
                                <q-item-section>送付量</q-item-section>
                                <q-item-section v-if="props.trade.trade_status !== TRADE_STATUS.PAID__SEND_PRODUCT">{{ props.trade.send_trade_amount.toLocaleString() }} {{ props.trade.product.unit }}</q-item-section>
                            </q-item>
                        </User>
                    </div>

                    <div class="col">
                        <User
                            buyer
                            :user="props.trade.buyer"
                            :is-show-product-account-item="isBuyerShowProductAccountItem"
                        >
                            <q-item>
                                <q-item-section>商品種類</q-item-section>
                                <q-item-section>{{ props.trade.product.name }}</q-item-section>
                            </q-item>
                            <q-item>
                                <q-item-section>取引量</q-item-section>
                                <q-item-section v-if="props.trade.trade_status !== TRADE_STATUS.REQUEST__CHECK_PRODUCT">
                                    {{ props.trade.receipt_trade_amount.toLocaleString() }} {{ props.trade.product.unit }}
                                </q-item-section>
                            </q-item>
                        </User>
                    </div>
                </div>

                <q-card class="q-mt-md">
                    <q-list separator>
                        <q-item>
                            <q-item-section>トレード番号</q-item-section>
                            <q-item-section>{{ props.trade.trade_no }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>オファーID</q-item-section>
                            <q-item-section>{{ props.trade.offer_id }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>オファー種類</q-item-section>
                            <q-item-section>{{ OFFER_TYPE_TEXT[props.trade.offer.offer_type] }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>商品種類</q-item-section>
                            <q-item-section>{{ props.trade.product.name }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>送付量</q-item-section>
                            <q-item-section>{{ props.trade.send_trade_amount.toLocaleString() }} {{ props.trade.product.unit }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>取引量</q-item-section>
                            <q-item-section>{{ props.trade.receipt_trade_amount.toLocaleString() }} {{ props.trade.product.unit }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>レート設定</q-item-section>
                            <q-item-section>{{ RATE_TYPE_TEXT[props.trade.offer.rate_type] }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>レート</q-item-section>
                            <q-item-section>
                                {{ props.trade.rate }} {{ props.trade.offer.currency.code }}
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>支払先情報</q-item-section>
                            <q-item-section>
                                <q-item-label caption>{{ props.trade.seller_payment.payment_method.name }}</q-item-label>
                                <q-list separator dense>
                                    <q-item v-for="item in props.trade.seller_payment.seller_payment_items">
                                        <q-item-section>{{ item.payment_item.name }}</q-item-section>
                                        <q-item-section>
                                            <template v-if="item.payment_item.type === PAYMENT_ITEM_TYPE.TEXT">
                                                {{ item.value }}
                                            </template>
                                            <template v-else-if="item.payment_item.type === PAYMENT_ITEM_TYPE.SELECT">
                                                {{ item.payment_options[0].name }}
                                            </template>
                                            <template v-else-if="item.payment_item.type === PAYMENT_ITEM_TYPE.CHECKBOX">
                                                {{ item.payment_options.map(e => e.name).join(',') }}
                                            </template>
                                            <template v-else-if="item.payment_item.type === PAYMENT_ITEM_TYPE.RADIO">
                                                {{ item.payment_options[0].name }}
                                            </template>
                                            <template v-else-if="item.payment_item.type === PAYMENT_ITEM_TYPE.IMAGE">
                                                <q-img
                                                    :src="item.value_url"
                                                    style="max-width: 300px;"
                                                />
                                            </template>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>ステータス</q-item-section>
                            <q-item-section>{{ TRADE_STATUS_TEXT[props.trade.trade_status] }}</q-item-section>
                        </q-item>
                    </q-list>
                    <q-separator />
                    <ProductAccountData
                        title="運営アカウント："
                        :data="props.trade.admin_product_account.admin_product_account_items"
                    />
                    <q-separator />
                    <ProductAccountData
                        title="Sellerアカウント："
                        :data="props.trade.seller.user_product_accounts[0].user_product_account_items"
                    />
                    <q-separator />
                    <ProductAccountData
                        title="Buyerアカウント："
                        :data="props.trade.buyer.user_product_accounts[0].user_product_account_items"
                    />
                </q-card>

                <div class="row q-col-gutter-x-md q-mt-md">
                    <div v-if="canStop" class="col-auto">
                        <q-btn
                            label="トレード停止"
                            color="red"
                            @click="toStop"
                        />
                    </div>
                    <div v-else-if="canStopCancel" class="col-auto">
                        <q-btn
                            label="トレード停止解除"
                            color="red"
                            @click="toStopCancel"
                        />
                    </div>
                    <div v-if="isStop" class="col">
                        <div class="text-caption">トレード停止理由</div>
                        <div style="word-break: break-word">
                        {{ props.trade.latest_stop_trade_history.reason }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <q-card>
                    <q-card-section class="text-right q-gutter-md">
                        <q-btn
                            label="要対応"
                        />
                        <q-btn
                            label="対応済"
                        />
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        チャット内容
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        メッセージ
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
