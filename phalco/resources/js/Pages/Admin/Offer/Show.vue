<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {OFFER_STATUS, OFFER_STATUS_TEXT} from "@/Config/OfferStatus.js";
import {RATE_TYPE, RATE_TYPE_TEXT} from "@/Config/RateType.js";
import {OFFER_TYPE, OFFER_TYPE_TEXT} from "@/Config/OfferTyoe.js";
import {useQuasar} from "quasar";
import {PROBLEM_REPORT_STATUS_TEXT} from "@/Config/ProblemReportStatus.js";
import {computed} from "vue";
import OfferStopDialog from "@/Dialogs/Admin/Offer/OfferStopDialog.vue";
import User from "@/Components/Admin/OfferTrade/User.vue";
import ProductAccountData from "@/Components/Admin/OfferTrade/ProductAccountData.vue";
import {copyText} from "@/Utils/CopyText.js";

const $q = useQuasar()

const props = defineProps({
    offer: Object,
    sendAdminProductAccounts: Array,
})

// 掲載中アクティブに変更
const toPublishingActive = () => {
    router.post(route('admin.offers.to_publishing_active', props.offer.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// 却下：却下中に変更
const toRejectChecking = () => {
    router.post(route('admin.offers.to_reject_checking', props.offer.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// 却下：返送中に変更
const toRejectReturning = () => {
    router.post(route('admin.offers.to_reject_returning', props.offer.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// 却下：却下済に変更
const toRejectDone = () => {
    router.post(route('admin.offers.to_reject_done', props.offer.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// キャンセル返送中に変更
const toCancelReturning = () => {
    router.post(route('admin.offers.to_cancel_returning', props.offer.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// キャンセル完了に変更
const toCancelDoneForm = useForm({
    admin_product_account_id: null,
})
const toCancelDone = () => {
    toCancelDoneForm.post(route('admin.offers.to_cancel_done', props.offer.id), {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

// 掲載差し止めに変更
const toStopForm = useForm({
    reason: '',
})
const toStop = () => {
    $q.dialog({
        component: OfferStopDialog,
        componentProps: {toStopForm}
    }).onOk(() => {
        toStopForm.post(route('admin.offers.to_stop', props.offer.id), {
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
                    message: '掲載差し止め理由を確認してください',
                })
            }
        })
    })
}

// 掲載差し止め解除
const toStopCancel = () => {
    router.post(route('admin.offers.to_stop_cancel', props.offer.id), null, {
        onSuccess: () => {
            $q.notify({
                message: 'オファーステータスを変更しました',
            })
        },
    })
}

const isShowProductAccountItem = computed(() => [
    OFFER_STATUS.WAIT_PUBLISH__CHECK_PRODUCT,
    OFFER_STATUS.CANCEL__RETURNING,
].includes(props.offer.offer_status))

// ユーザー領域の返送量の表示
const isShowReturnAmount = computed(() => [
    OFFER_STATUS.CANCEL__WAIT_RETURN,
    OFFER_STATUS.CANCEL__RETURNING,
    OFFER_STATUS.CANCEL__DONE,
].includes(props.offer.offer_status))
</script>

<template>
    <Head title="オファー詳細" />

    <AuthenticatedLayout>
        <template #header>
            オファー詳細
        </template>

        <q-banner class="bg-light-blue text-white full-width">
            <template v-if="props.offer.offer_status === OFFER_STATUS.WAIT_PUBLISH__WAIT_PRODUCT">
                商品送付待ち
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.WAIT_PUBLISH__CHECK_PRODUCT">
                受取商品が総数量と同量であるか確認し承認をしてください。
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.PUBLISHING__ACTIVE">
                オファー掲載中
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.PUBLISHING__PAUSE">
                オファー申請受付休止中
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.REJECT__CHECKING">
                確認してください
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.REJECT__RETURNING">
                返送対応が終了したら返送完了を押してください
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.REJECT__DONE">
                却下済
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__WAIT_TRADE">
                トレード完了待ち
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__WAIT_RETURN">
                返送の準備ができたら返送開始を押してください
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__RETURNING">
                返送対応が終了したら返送完了を押してください
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__NO_RETURN">
                返送不要
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__DONE">
                返送完了
            </template>
            <template v-else-if="props.offer.offer_status === OFFER_STATUS.STOP">
                掲載差し止め
            </template>
        </q-banner>

        <div class="row justify-end q-col-gutter-x-md q-mt-md">
            <div class="col-auto">
                <q-btn
                    label="トレード一覧"
                    @click="router.visit(route('admin.trades.index', {offer_id: props.offer.id}))"
                />
            </div>
            <div class="col-auto">
                <q-btn
                    label="履歴の閲覧"
                    @click="router.visit(route('admin.offer_histories.index', props.offer.id))"
                />
            </div>
        </div>

        <div class="row q-col-gutter-md q-mt-md">
            <div class="col-6">

                <q-badge
                    :label="OFFER_STATUS_TEXT[props.offer.offer_status]"
                    color="grey"
                    class="q-pa-sm"
                    style="font-size: 1rem"
                />
                <q-badge
                    :label="'問題報告ステータス：' + PROBLEM_REPORT_STATUS_TEXT[props.offer.problem_report_status]"
                    color="grey"
                    class="q-pa-sm q-ml-md"
                    style="font-size: 1rem"
                />

                <template v-if="props.offer.offer_status === OFFER_STATUS.WAIT_PUBLISH__CHECK_PRODUCT">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="承認"
                            color="green"
                            @click="toPublishingActive"
                        />
                        <q-btn
                            label="却下"
                            color="red"
                            @click="toRejectChecking"
                        />
                    </div>
                </template>
                <template v-if="props.offer.offer_status === OFFER_STATUS.REJECT__CHECKING">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="要返送"
                            color="green"
                            @click="toRejectReturning"
                        />
                        <q-btn
                            label="返送不要"
                            color="green"
                            @click="toRejectDone"
                        />
                    </div>
                </template>
                <template v-if="props.offer.offer_status === OFFER_STATUS.REJECT__RETURNING">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="返送完了"
                            color="green"
                            @click="toRejectDone"
                        />
                    </div>
                </template>
                <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__WAIT_RETURN">
                    <div class="row q-gutter-md q-mt-md">
                        <q-btn
                            label="返送開始"
                            color="green"
                            @click="toCancelReturning"
                        />
                    </div>
                </template>
                <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__RETURNING">
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
                                label="返送完了"
                                color="green"
                                @click="toCancelDone"
                            />
                        </div>
                    </div>
                </template>
                <template v-else-if="props.offer.offer_status === OFFER_STATUS.CANCEL__DONE">
                    <div class="row q-gutter-md items-center q-mt-md">
                        <div class="col-auto">
                            返送に使用したアカウント：{{ props.offer.latest_offer_history.admin_product_account.name }}
                        </div>
                    </div>
                </template>

                <User
                    v-bind="{[props.offer.offer_type === OFFER_TYPE.BUY ? 'buyer' : 'seller']: true}"
                    :user="props.offer.user"
                    :is-show-product-account-item="isShowProductAccountItem"
                    class="q-mt-md"
                >
                    <q-item>
                        <q-item-section>商品種類</q-item-section>
                        <q-item-section>{{ props.offer.product.name }}</q-item-section>
                    </q-item>

                    <q-item v-if="isShowReturnAmount">
                        <q-item-section>返送量</q-item-section>
                        <q-item-section>
                            <div>
                                <q-btn
                                    icon="content_copy" color="secondary" dense rounded size="sm"
                                    @click="copyText($q, props.offer.return_amount)"
                                />
                                {{ props.offer.return_amount.toLocaleString() }} {{ props.offer.product.unit }}</div>
                            <div style="font-size: .675rem">総数量 {{ props.offer.amount.toLocaleString() }} - 返送手数料 {{ props.offer.product.refund_fee.toLocaleString() }}</div>
                        </q-item-section>
                    </q-item>
                    <q-item v-else>
                        <q-item-section>総数量</q-item-section>
                        <q-item-section>{{ props.offer.amount.toLocaleString() }} {{ props.offer.product.unit }}</q-item-section>
                    </q-item>
                </User>

                <q-card class="q-mt-md">
                    <q-list separator>
                        <q-item>
                            <q-item-section>オファー番号</q-item-section>
                            <q-item-section>{{ props.offer.offer_no }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>オファー種類</q-item-section>
                            <q-item-section>{{ OFFER_TYPE_TEXT[props.offer.offer_type] }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>商品種類</q-item-section>
                            <q-item-section>{{ props.offer.product.name }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>総数量</q-item-section>
                            <q-item-section>{{ props.offer.amount.toLocaleString() }} {{ props.offer.product.unit }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>掲載量</q-item-section>
                            <q-item-section>{{ props.offer.display_amount.toLocaleString() }} {{ props.offer.product.unit }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>レート設定</q-item-section>
                            <q-item-section>{{ RATE_TYPE_TEXT[props.offer.rate_type] }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>レート</q-item-section>
                            <q-item-section>
                                <template v-if="props.offer.rate_type === RATE_TYPE.FIXED">
                                    {{ props.offer.rate }} {{ props.offer.currency.code }}
                                </template>
                                <template v-else>
                                    {{ props.offer.rate }} %
                                </template>
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>取引可能範囲</q-item-section>
                            <q-item-section>
                                <template v-if="props.offer.max_amount">
                                    最小 {{ props.offer.min_amount.toLocaleString() }} {{ props.offer.currency.code }} ~ 最大 {{ props.offer.max_amount.toLocaleString() }} {{ props.offer.currency.code }}
                                </template>
                                <template v-else>
                                    最小 {{ props.offer.min_amount.toLocaleString() }} {{ props.offer.currency.code }}
                                </template>
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>支払期間</q-item-section>
                            <q-item-section>{{ props.offer.time_limit }}:00</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>相手に求める条件</q-item-section>
                            <q-item-section></q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>支払方法</q-item-section>
                            <q-item-section v-if="props.offer.offer_type === OFFER_TYPE.BUY">
                                {{ props.offer.user.buyer_payments.map(e => e.payment_method.name).join(',') }}
                            </q-item-section>
                            <q-item-section v-else>
                                {{ props.offer.user.seller_payments.map(e => e.payment_method.name).join(',') }}
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>備考</q-item-section>
                            <q-item-section>{{ props.offer.note }}</q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>ステータス</q-item-section>
                            <q-item-section>{{ OFFER_STATUS_TEXT[props.offer.offer_status] }}</q-item-section>
                        </q-item>
                    </q-list>
                    <q-separator />
                    <ProductAccountData
                        title="運営アカウント："
                        :data="props.offer.admin_product_account.admin_product_account_items"
                    />
                    <q-separator />
                    <ProductAccountData
                        title="オファー掲載者のアカウント："
                        :data="props.offer.user.user_product_accounts[0].user_product_account_items"
                    />
                </q-card>

                <div
                    v-if="[OFFER_STATUS.PUBLISHING__ACTIVE, OFFER_STATUS.PUBLISHING__PAUSE, OFFER_STATUS.STOP].includes(props.offer.offer_status)"
                    class="row q-col-gutter-x-md q-mt-md"
                >
                    <div v-if="props.offer.offer_status !== OFFER_STATUS.STOP" class="col-auto">
                        <q-btn
                            label="掲載差し止め"
                            color="red"
                            @click="toStop"
                        />
                    </div>
                    <template v-else>
                        <div class="col-auto">
                            <q-btn
                                label="掲載差し止め解除"
                                color="red"
                                @click="toStopCancel"
                            />
                        </div>
                        <div class="col">
                            <div class="text-caption">掲載差し止め理由</div>
                            <div style="word-break: break-word">
                                {{ props.offer.latest_offer_history.reason }}
                            </div>
                        </div>
                    </template>
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
