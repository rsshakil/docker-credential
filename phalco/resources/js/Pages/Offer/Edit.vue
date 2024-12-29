<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import { OFFER_TYPE,OFFER_TYPE_TEXT } from "@/Config/OfferTyoe.js";
import { useQuasar } from "quasar";
import { computed, ref, onMounted, watch } from "vue";
import { RATE_TYPE, RATE_TYPE_OPTIONS } from "@/Config/RateType.js";
import { OFFER_TIME_LIMIT, OFFER_TIME_LIMIT_OPTIONS } from "@/Config/OfferTimeLimit.js";
import OfferTypeSelect from "./Partials/OfferTypeSelect.vue";
import PaymentMethodListTab from "./Partials/PaymentMethodListTab.vue";
import axios from "axios";
import OfferEditConfirmDialog from "@/Dialogs/Offer/OfferEditConfirmDialog.vue";
import TermsConditionDialog from "@/Dialogs/Offer/TermsConditionDialog.vue";
import PaymentSelectionDialog from "@/Dialogs/Offer/PaymentSelectionDialog.vue";

const $q = useQuasar();

const props = defineProps({
    errors: Object,
    offer: Object,
    products: Array,
    userProductAccount: Array,
    currencies: Array,
    userPaymentMethods: Object,
    offerType: {
        type: Number,
        default: 2,
    },
});

const rateTypeValue = props.offer ? props.offer.rate_type : RATE_TYPE.FIXED;
const timeLimitValue = props.offer ? props.offer.time_limit : OFFER_TIME_LIMIT.TIME_30;
const currencyIdValue = props.offer?.currency_id ?? (Array.isArray(props.currencies) && props.currencies.length > 0 ? props.currencies[0].id : undefined);
const productIdValue = props.offer?.product_id ?? (Array.isArray(props.products) && props.products.length > 0 ? props.products[0].id : undefined);
const offerTypeValue = props.offer ? props.offer.offer_type : props.offerType;
const form = useForm({
    offer_id: props.offer?.id,
    offer_type: offerTypeValue,
    product_id: productIdValue,
    currency_id: currencyIdValue,
    rate_type: rateTypeValue,
    rate: props.offer ? props.offer.rate : null,
    amount: props.offer ? props.offer.amount : null,
    request_amount: props.offer ? props.offer.request_amount : null,
    min_amount: props.offer ? props.offer.min_amount : props.currencies[0].min_amount,
    max_amount: props.offer ? props.offer.max_amount : null,
    time_limit: timeLimitValue,
    note: props.offer ? props.offer.note : "",
    payment_methods: props.userPaymentMethods[offerTypeValue][currencyIdValue]??[],
    terms: [],
});

const onSubmit = () => {
    $q.dialog({
        component: OfferEditConfirmDialog,
        componentProps: {
            form: form.data(),
            currencyCode: selectCurrency.value.code,
            productUnit: selectProduct.value.unit,
            displayAmount: displayAmount.value,
        },
    }).onOk(() => {
        if (props.offer) {
            form.data = () => ({
                rate_type: form.rate_type,
                rate: form.rate,
                min_amount: form.min_amount,
                max_amount: form.max_amount,
                time_limit: form.time_limit,
                payment_methods: form.payment_methods,
                note: form.note,
                terms: form.terms,
            });

            form.submit("put", route("offers.update", [props.offer.offer_no]), {
                onSuccess: (response) => {
                    $q.notify({
                        message: "オファーが作成されました",
                    });
                },
            });
        } else {
            form.submit("post", route("offers.store"), {
                onSuccess: (response) => {
                    $q.notify({
                        message: "オファーが作成されました",
                    });
                },
            });
        }
    });
};

const selectProduct = ref(props.products.find((e) => e.id === form.product_id));

const selectProductAccount = ref(props.userProductAccount.find((e) => e.product_id === form.product_id).user_product_account_items??[]);

const selectCurrency = computed(() => props.currencies.find((e) => e.id === form.currency_id));

const termItems = ref([
    {
        id: 1,
        title: "terms condition 1",
        description: "dummmy content",
        isSelected: true,
    },
    {
        id: 2,
        title: "terms condition 2",
        description: "dummmy content",
        isSelected: false,
    },
    {
        id: 3,
        title: "terms condition 3",
        description: "dummmy content",
        isSelected: true,
    },
    {
        id: 4,
        title: "terms condition 4",
        description: "dummmy content",
        isSelected: false,
    },
    {
        id: 5,
        title: "terms condition 5",
        description: "dummmy content",
        isSelected: false,
    },
]);

const productRateBase = ref(null);
const minVariableRate = ref(null);
const maxVariableRate = ref(null);
const displayAmount = ref(null);
const getProductRate = async (product_id, currency_id) => {

    const response = await axios.get(route("api.get_product_rate"), {
        params: {
            product_id: product_id,
            currency_id: currency_id,
        },
    });
    productRateBase.value = response.data.baseRate;
    minVariableRate.value = response.data.minRate;
    maxVariableRate.value = response.data.maxRate;
};

onMounted(() => {
    getProductRate(form.product_id, form.currency_id);
    getDisplayAmount();
});

const getDisplayAmount = async () => {
    let url;
    if (form.offer_type === OFFER_TYPE.BUY) {
        url = route("api.get_display_amount_buy");
    } else {
        url = route("api.get_display_amount_sell");
    }
    if(form.amount){
        const response = await axios.get(url, {
            params: {
                product_id: form.product_id,
                amount: form.amount,
            },
        });
        displayAmount.value = response.data;
        form.request_amount = response.data;
    }

};

const openTermsConditionDialog = () => {
    $q.dialog({
        component: TermsConditionDialog,
        componentProps: {
            list: termItems.value
        },
    }).onOk((e) => {
        termItems.value = [...e.value];
        let selectedTerms = termItems.value.filter((e) => e.isSelected === true);
        form.terms = selectedTerms ?? [];
    });
}

const openPaymentSelctionDialog = () => {
    $q.dialog({
        component: PaymentSelectionDialog,
        componentProps: {
            list: form.payment_methods,
            offerType:form.offer_type
        },
    }).onOk((e) => {
        form.payment_methods = [...e];
    });
}

const handleOfferTypeChange = (offerType) => {
    form.payment_methods =  props.userPaymentMethods[offerType][form.currency_id]??[];
    getDisplayAmount();
}

const minRate = computed(() => form.rate_type === RATE_TYPE.VARIABLE ? 70: minVariableRate.value);
const maxRate = computed(() => form.rate_type === RATE_TYPE.VARIABLE ? 130: maxVariableRate.value);

const updateCurrency = () => {
    form.payment_methods = props.userPaymentMethods[form.offer_type][form.currency_id]??[];
    getProductRate(form.product_id, form.currency_id);
}

const updateProduct = () => {
    selectProduct.value = props.products.find((e) => e.id === form.product_id);
    selectProductAccount.value = props.userProductAccount.find((e) => e.product_id === form.product_id).user_product_account_items??[];
    getProductRate(form.product_id, form.currency_id);
}

const amountLabel = computed(() => form.offer_type === OFFER_TYPE.BUY ? '購入したい量' : '販売したい量\n(運営への送付量)');
</script>

<template>
    <Head title="オファー内容入力" />

    <MenuLayout>
        <div class="component-center-body">
            <q-form @submit="onSubmit">
                <div v-if="$q.screen.gt.sm">

                    <h5 class="no-margin q-pb-md page-title">オファー掲載内容の入力</h5>
                    <OfferTypeSelect v-if="!props.offer" v-model="form.offer_type" @update:modelValue="handleOfferTypeChange" />
                    <q-chip v-else>
                        <q-avatar icon="bookmark" size="md" color="red" text-color="white" />
                        {{ OFFER_TYPE_TEXT[offer.offer_type] }}
                    </q-chip>

                    <q-card class="q-pa-md q-mt-sm row" flat bordered>
                        <div class="col-8 q-pr-md">
                            <q-card-section class="q-px-none q-pt-none row q-col-gutter-sm q-mb-md">
                                <div class="col-3">
                                    <label class="label-category-title">商品</label>
                                </div>
                                <div class="col-9">
                                    <q-card-section class="q-pl-none q-pt-none row">
                                        <div class="col-8">
                                            <label class="label-form-title">{{ amountLabel }}
                                                <q-icon size="sm" name="help">
                                                    <q-tooltip class="bg-black" anchor="top middle" self="center middle">
                                                        <label class="label-sub-title-info">掲載量とは、入力値でオファーを掲載するための手数料を含む量のことです。</label>
                                                    </q-tooltip>
                                                </q-icon>
                                            </label>
                                            <div class="flex items-center justify-start width-of-amount-field-area">
                                                <q-input
                                                    outlined
                                                    type="number"
                                                    filled
                                                    dense
                                                    v-model.number="form.amount"
                                                    @update:modelValue="getDisplayAmount"
                                                    :debounce="1000"
                                                    :error="!!form.errors.amount"
                                                    :error-message="form.errors.amount"
                                                    :disable="!!props.offer"
                                                    :rules="[
                                                        (val) => (!!val && !isNaN(val)) || '必須項目です',
                                                        (val) => (val>=0) || '- 受け入れられない',
                                                    ]"
                                                    :hint="selectProduct ? `掲載量:${displayAmount ?? ''}` : ''"
                                                    class="no-spin no-padding q-field-common width-of-amount-field"
                                                >
                                                </q-input>
                                                <span class="form-field-suffix">{{selectProduct?.unit}}</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label class="label-form-title">商品種類</label>
                                            <q-select
                                                outlined
                                                v-model="form.product_id"
                                                filled
                                                dense
                                                :options="props.products"
                                                option-value="id"
                                                option-label="name"
                                                map-options
                                                emit-value
                                                @update:modelValue="updateProduct"
                                                :hint="`手数料:${selectProduct?.trade_fee * 100 ?? ''}%`"
                                                :error="!!form.errors.product_id"
                                                :disable="!!props.offer"
                                                :error-message="form.errors.product_id"
                                                class="q-field-common no-padding width-of-product-select-field"
                                            />
                                        </div>
                                    </q-card-section>
                                </div>
                            </q-card-section>

                            <q-separator />

                            <q-card-section class="q-px-none row q-col-gutter-lg">
                                <div class="col-3">
                                    <label class="label-category-title">トレード設定</label>
                                </div>
                                <div class="col-9">
                                    <q-card-section class="q-pl-none q-pt-none">
                                        <label class="label-form-title">通貨種類</label>
                                        <q-select
                                            outlined
                                            filled
                                            dense
                                            v-model="form.currency_id"
                                            :options="props.currencies"
                                            option-value="id"
                                            option-label="code"
                                            map-options
                                            emit-value
                                            :disable="!!props.offer"
                                            @update:modelValue="updateCurrency"
                                            class="q-field-common width-of-currency-select-field"
                                        />
                                    </q-card-section>

                                    <q-card-section class="q-pl-none row q-col-gutter-lg">
                                        <div class="col-auto">
                                            <label class="label-form-title">レート種類</label>
                                            <q-select
                                                outlined
                                                v-model="form.rate_type"
                                                :options="RATE_TYPE_OPTIONS"
                                                option-label="label"
                                                option-value="value"
                                                map-options
                                                emit-value
                                                filled
                                                dense
                                                class="q-field-common width-of-rate-type-select-field"
                                                :hint="productRateBase && productRateBase != '' ? `参考レート: ${productRateBase}${selectCurrency.code}/${selectProduct.unit}` : ''"
                                            />
                                        </div>
                                        <div class="col-auto">
                                            <label class="label-form-title">レート
                                                <q-icon size="sm" name="help">
                                                    <q-tooltip class="bg-black" anchor="top middle" self="center middle">
                                                        <label class="label-sub-title-info">{{ `${minRate} ~ ${maxRate}の範囲で指定してください` }}</label>
                                                    </q-tooltip>
                                                </q-icon>
                                            </label>
                                            <div class="flex items-center justify-start">
                                                <q-input
                                                    outlined
                                                    type="number"
                                                    filled
                                                    dense
                                                    v-model.number="form.rate"
                                                    :rules="[
                                                        (val) => (!!val && !isNaN(val)) || '必須項目です',
                                                        (val) => (val>=0) || '- 受け入れられない',
                                                        (val) => (val>=minRate && val<=maxRate) || `${minRate} ~ ${maxRate}の範囲で指定してください`
                                                    ]"
                                                    :disable="!selectProduct"
                                                    class="no-padding no-spin q-field-common width-of-rate-type-field"
                                                    :error="!!errors.rate"
                                                    :error-message="errors.rate ? errors.rate : ''"
                                                >
                                                </q-input>
                                                <span class="form-field-suffix">
                                                    {{ form.rate_type == RATE_TYPE.FIXED ? selectCurrency.code : "%" }}
                                                </span>
                                            </div>
                                        </div>
                                    </q-card-section>

                                    <q-card-section class=" bg-alert-info">
                                        <label class="label-sub-title-info">固定レート：入力値でオファーが掲載されます。</label>
                                        <label class="label-sub-title-info">変動レート：入力値と参考レートの乗算によるレートでオファーが掲載されます。</label>
                                    </q-card-section>

                                    <q-card-section class="q-pl-none">
                                        <label class="label-form-title">トレード可能範囲</label>
                                        <div class="row q-gutter-x-lg flex items-center justify-center">
                                            <div class="col-auto">
                                                <div class="flex items-center justify-start width-of-min-max-field-area">
                                                    <q-input
                                                        outlined
                                                        type="number"
                                                        label="最小"
                                                        v-model.number="form.min_amount"
                                                        :error="!!form.errors.min_amount"
                                                        :error-message="form.errors.min_amount"
                                                        :rules="[
                                                            (val) => (!!val && !isNaN(val)) || '必須項目です',
                                                            (val) => (val>=0) || '- 受け入れられない'
                                                        ]"
                                                        filled
                                                        dense
                                                        class="no-spin no-padding q-field-common width-of-min-max-amount-field"
                                                    >
                                                    </q-input>
                                                    <span class="form-field-suffix">{{selectCurrency?.code}}</span>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="flex items-center justify-start">
                                                    <q-input
                                                        outlined
                                                        type="number"
                                                        label="最大"
                                                        v-model.number="form.max_amount"
                                                        :error="!!form.errors.max_amount"
                                                        :error-message="form.errors.max_amount"
                                                        :rules="[
                                                            (val) =>  (val === null || val === undefined || val === '') || (val>=0) || '- 受け入れられない',
                                                            (val) =>  (val === null || val === undefined || val === '') || (val >= form.min_amount) || `最低限は ${form.min_amount.toLocaleString()} です`,
                                                        ]"
                                                        filled
                                                        dense
                                                        class="no-spin no-padding q-field-common width-of-min-max-amount-field"
                                                    >
                                                    </q-input>
                                                    <span class="form-field-suffix">{{selectCurrency?.code}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </q-card-section>


                                    <q-card-section class="q-pl-none">
                                        <label class="label-form-title">支払いの時間制限</label>
                                        <q-select
                                            outlined
                                            :options="OFFER_TIME_LIMIT_OPTIONS"
                                            v-model="form.time_limit"
                                            option-label="label"
                                            option-value="value"
                                            map-options
                                            emit-value
                                            filled
                                            dense
                                            class="q-field-common width-of-currency-select-field"
                                        />
                                    </q-card-section>

                                    <q-card-actions class="q-mt-md q-pl-none" align="left">
                                        <q-btn
                                            type="button"
                                            label="キャンセル"
                                            class="button-cancel"
                                            rounded=""
                                            @click="router.get(route('offers.index'))"
                                        />
                                        <q-btn
                                            type="submit"
                                            label="掲載"
                                            color="primary"
                                            class="button-submit offer-submit-btn"
                                            rounded=""
                                        />
                                    </q-card-actions>
                                </div>
                            </q-card-section>

                        </div>
                        <div class="col-4 q-pl-md border-left-offer-create">
                            <q-card class="q-mb-md" unelevated flat>
                                <q-card-section class="row items-center justify-between text-black card-payment-item-list">
                                    <div class="text-subtitle2">商品送付先情報</div>
                                </q-card-section>
                                <q-card-section>
                                    <div class="text-body2" v-if="selectProductAccount" v-for="(account) in selectProductAccount">{{ account.product_account_item.name }}:{{ account.value }}</div>
                                </q-card-section>
                            </q-card>
                            <q-separator class="q-mb-md q-mt-md"/>

                            <div class="row items-center justify-between q-mb-md">
                                <label class="label-of-btn-title">支払い方法</label>
                                <q-btn label="追加する" rounded size="md" @click="openPaymentSelctionDialog" class="button-label-right"/>
                            </div>

                            <PaymentMethodListTab
                                v-model="form.payment_methods"
                                :offerType="form.offer_type"
                            />

                            <q-separator class="q-mb-md q-mt-md" />

                            <div class="row items-center justify-between q-mb-md">
                                <label class="label-of-btn-title">相手に求める条件</label>
                                <q-btn label="追加する" rounded class="button-label-right" @click="openTermsConditionDialog"/>
                            </div>

                            <q-badge
                                v-for="(term) in form.terms"
                                :key="term.id"
                                :label="term.title"
                                class="q-pa-sm payment-method q-ma-xs"
                            />

                            <q-separator class="q-mb-md q-mt-md" />

                            <label class="label-form-title">備考</label>
                            <q-input
                                outlined
                                type="textarea"
                                v-model="form.note"
                                class="textarea-input-field"
                            />
                        </div>
                    </q-card>
                </div>
                <div v-else>
                    <div class="row q-mb-sm">
                        <div class="col-12">
                            <h5 class="no-margin q-pb-md"> オファー掲載内容の入力 </h5>
                        </div>
                        <div class="col-12">
                            <OfferTypeSelect v-if="!props.offer" v-model="form.offer_type" @update:modelValue="handleOfferTypeChange"/>
                            <q-chip v-else>
                                <q-avatar icon="bookmark" color="red" text-color="white" />
                                {{ OFFER_TYPE_TEXT[offer.offer_type] }}
                            </q-chip>
                        </div>
                    </div>
                    <q-card class="q-pa-md" flat bordered>
                        <q-card-section class="q-pa-none ">
                            <label class="label-category-title">商品</label>
                        </q-card-section>

                        <q-card-section class="row items-center q-px-none q-pb-none">
                            <div class="col-6">
                                <div class="row justify-start items-center">
                                    <div class="preformatted col-auto label-form-title no-margin">{{ amountLabel }}</div>
                                    <div class="col-auto q-ml-sm">
                                        <q-icon size="sm" name="help">
                                            <q-tooltip class="bg-black" anchor="top middle" self="center middle">
                                                <label class="label-sub-title-info">掲載量とは、入力値でオファーを掲載するための手数料を含む量のことです。</label>
                                            </q-tooltip>
                                        </q-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 flex items-center justify-end width-of-amount-field-area">
                                <q-input
                                    outlined
                                    type="number"
                                    filled
                                    dense
                                    v-model.number="form.amount"
                                    @update:modelValue="getDisplayAmount"
                                    :debounce="1000"
                                    :error="!!form.errors.amount"
                                    :error-message="form.errors.amount"
                                    :disable="!!props.offer"
                                    :rules="[
                                        (val) => (!!val && !isNaN(val)) || '必須項目です',
                                        (val) => (val>=0) || '- 受け入れられない',
                                    ]"
                                    :hint="selectProduct ? `掲載量:${displayAmount ?? ''}` : '' "
                                    class="no-spin no-padding q-field-common width-of-amount-field"
                                >
                                </q-input>
                                <span class="form-field-suffix">{{selectProduct?.unit}}</span>
                            </div>
                        </q-card-section>
                        <q-card-section class="row q-mt-lg items-center q-px-none">
                            <div class="col-6">
                                <label class="label-form-title no-margin">商品種類</label>
                            </div>
                            <div class="col-6 flex justify-end">
                                <q-select
                                    outlined
                                    v-model="form.product_id"
                                    filled
                                    dense
                                    :options="props.products"
                                    option-value="id"
                                    option-label="name"
                                    map-options
                                    emit-value
                                    @update:modelValue="updateProduct"
                                    :hint="`手数料:${selectProduct?.trade_fee * 100 ?? ''}%`"
                                    :error="!!form.errors.product_id"
                                    :disable="!!props.offer"
                                    :error-message="form.errors.product_id"
                                    class="q-field-common no-padding width-of-product-select-field"
                                />
                            </div>
                        </q-card-section>
                        <q-separator class="q-mb-md q-mt-md" />

                        <q-card-section class="q-pa-none ">
                            <label class="label-category-title">トレード設定</label>
                        </q-card-section>

                        <q-card-section class="row items-center q-px-none">
                            <div class="col-6">
                                <label class="label-form-title no-margin">通貨種類</label>
                            </div>
                            <div class="col-6 flex justify-end">
                                <q-select
                                    outlined
                                    filled
                                    dense
                                    v-model="form.currency_id"
                                    :options="props.currencies"
                                    option-value="id"
                                    option-label="code"
                                    map-options
                                    emit-value
                                    :disable="!!props.offer"
                                    @update:modelValue="updateCurrency"
                                    class="q-field-common width-of-currency-select-field"
                                />
                            </div>
                        </q-card-section>
                        <q-card-section class="bg-alert-info q-mb-md">
                            <label class="label-sub-title-info">固定レート：入力値でオファーが掲載されます。</label>
                            <label class="label-sub-title-info">変動レート：入力値と参考レートの乗算によるレートでオファーが掲載されます。</label>
                        </q-card-section>
                        <div class="row items-center ">
                            <div class="col-6">
                                <label class="label-form-title no-margin">レート種類 </label>
                            </div>
                            <div class="col-6 relative-position flex justify-end">
                                <q-select
                                    outlined
                                    v-model="form.rate_type"
                                    :options="RATE_TYPE_OPTIONS"
                                    option-label="label"
                                    option-value="value"
                                    map-options
                                    emit-value
                                    filled
                                    dense
                                    class="q-field-common width-of-rate-type-select-field"
                                />

                            </div>
                        </div>
                        <div class="row q-mb-md">
                            <div class="q-hint-text col-12 flex items-center justify-end">
                                {{ productRateBase && productRateBase != "" ? `参考レート: ${productRateBase}${selectCurrency.code}/${selectProduct.unit}` : "" }}
                            </div>
                        </div>
                        <q-card-section class="row items-center q-px-none">
                            <div class="col-6">
                                <label class="label-form-title no-margin">レート
                                    <q-icon size="sm" name="help">
                                        <q-tooltip class="bg-black" anchor="top middle" self="center middle">
                                            <label class="label-sub-title-info">{{ `${minRate} ~ ${maxRate}の範囲で指定してください` }}</label>
                                        </q-tooltip>
                                    </q-icon>
                                </label>
                            </div>

                            <div class="col-6 flex items-center justify-end width-of-amount-field-area">
                                <q-input
                                    outlined
                                    type="number"
                                    filled
                                    dense
                                    v-model.number="form.rate"
                                    :rules="[
                                        (val) => (!!val && !isNaN(val)) || '必須項目です',
                                        (val) => (val>=0) || '- 受け入れられない',
                                        (val) => (val>=minRate && val<=maxRate) || `${minRate} ~ ${maxRate}の範囲で指定してください`
                                    ]"
                                    :disable="!selectProduct"
                                    :error="!!errors.rate"
                                    :error-message="errors.rate ? errors.rate : ''"
                                    class="no-spin no-padding q-field-common width-of-rate-type-field"
                                />
                                <span class="form-field-suffix">{{ form.rate_type == RATE_TYPE.FIXED ? selectCurrency.code : "%"}}</span>
                            </div>
                        </q-card-section>

                        <q-card-section class="row items-center q-px-none">
                            <div class="col-6">
                                <label class="label-form-title no-margin">トレード可能範囲</label>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-12 q-mb-sm">
                                        <div class="flex items-center justify-end width-of-min-max-field-area">
                                            <q-input
                                                outlined
                                                type="number"
                                                label="最小"
                                                v-model.number="form.min_amount"
                                                :error="!!form.errors.min_amount"
                                                :error-message="form.errors.min_amount"
                                                :rules="[
                                                    (val) => (!!val && !isNaN(val)) || '必須項目です',
                                                    (val) => (val>=0) || '- 受け入れられない'
                                                ]"
                                                filled
                                                dense
                                                class="no-spin no-padding q-field-common width-of-min-max-amount-field"
                                            >
                                            </q-input>
                                            <span class="form-field-suffix">{{ selectCurrency?.code }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 flex items-center justify-end">
                                <q-input
                                    outlined
                                    type="number"
                                    label="最大"
                                    v-model.number="form.max_amount"
                                    :error="!!form.errors.max_amount"
                                    :error-message="form.errors.max_amount"
                                    :rules="[
                                        (val) => (val === null || val === undefined || val === '') || (val>=0) || '- 受け入れられない',
                                        (val) => (val === null || val === undefined || val === '') || (val && val >= form.min_amount) || `最低限は ${form.min_amount.toLocaleString()} です`,
                                    ]"
                                    filled
                                    dense
                                    class="no-spin no-padding q-field-common width-of-min-max-amount-field"
                                >
                                </q-input>
                                <span class="form-field-suffix">{{ selectCurrency?.code }}</span>
                            </div>
                        </q-card-section>

                        <q-card-section class="row items-center q-px-none">
                            <div class="col-6">
                                <label class="label-form-title no-margin">支払いの時間制限</label>
                            </div>
                            <div class="col-6 flex justify-end">
                                <q-select
                                    outlined
                                    :options="OFFER_TIME_LIMIT_OPTIONS"
                                    v-model="form.time_limit"
                                    option-label="label"
                                    option-value="value"
                                    map-options
                                    emit-value
                                    filled
                                    dense
                                    class="q-field-common width-of-currency-select-field"
                                />
                            </div>
                        </q-card-section>
                        <q-separator class="q-mb-md q-mt-md" />
                        <q-card-section class="row items-center justify-between text-black card-payment-item-list">
                            <div class="text-subtitle2">商品送付先情報</div>
                        </q-card-section>
                        <q-card-section>
                            <div class="text-body2" v-if="selectProductAccount" v-for="(account) in selectProductAccount">{{ account.product_account_item.name }}:{{ account.value }}</div>
                        </q-card-section>

                        <q-separator class="q-mb-md" />

                        <q-card-section class="col-12 q-px-none">
                            <div class="row items-center justify-between q-mb-md">
                                <label class="label-of-btn-title">支払い方法</label>
                                <q-btn label="追加する" rounded size="md" @click="openPaymentSelctionDialog" class="button-label-right"/>
                            </div>

                            <PaymentMethodListTab
                                v-model="form.payment_methods"
                                :offerType="form.offer_type"
                            />
                        </q-card-section>

                        <q-separator class="q-mb-md q-mt-md" />

                        <q-card-section class="col-12 q-px-none">
                            <div class="row items-center justify-between q-mb-md">
                                <label class="label-of-btn-title">相手に求める条件</label>
                                <q-btn label="追加する" rounded class="button-label-right" @click="openTermsConditionDialog"/>
                            </div>
                        </q-card-section>
                        <q-card-section class="col-12 q-px-none">

                            <q-badge
                                v-for="(term) in form.terms"
                                :key="term.id"
                                :label="term.title"
                                class="q-pa-sm payment-method q-ma-xs"
                            />
                        </q-card-section>

                        <q-separator class="q-mb-md q-mt-md" />

                        <q-card-section class="col-12  q-px-none">
                            <label class="label-form-title">備考</label>
                            <q-input
                                outlined
                                type="textarea"
                                v-model="form.note"
                                class="textarea-input-field"
                            />
                        </q-card-section>
                        <q-card-section class="q-mt-md q-gutter-x-md flex flex-start q-px-none">
                            <q-btn
                                type="button"
                                label="キャンセル"
                                class="button-cancel"
                                rounded=""
                                @click="router.get(route('offers.index'))"
                            />
                            <q-btn
                                type="submit"
                                label="掲載"
                                color="primary"
                                class="button-submit offer-submit-btn"
                                rounded=""
                            />
                        </q-card-section>
                    </q-card>
                </div>
            </q-form>
        </div>
    </MenuLayout>
</template>
<style>
@import "../../../sass/card.scss";
@import "../../../sass/button.scss";
@import "../../../sass/input.scss";
@import "../../../sass/text.scss";
</style>
