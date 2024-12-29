<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {
    MULTI_PRODUCT_ACCOUNT_ITEM_TYPE,
    PRODUCT_ACCOUNT_ITEM_TYPE,
    PRODUCT_ACCOUNT_ITEM_TYPE_OPTIONS
} from "@/Config/ProductAccountItemType.js";
import {computed} from "vue";

const $q = useQuasar()

const props = defineProps({
    product: Object,
    currencies: Array,
})

const form = useForm({
    name: props.product ? props.product.name : '',
    logo: null,
    unit: props.product ? props.product.unit : '',
    scale: props.product ? props.product.scale : '',
    currency_id: props.product ? props.product.currency_id : null,
    base_currency_rate: props.product ? props.product.base_currency_rate : '',
    trade_fee: props.product ? props.product.trade_fee : '',
    refund_fee: props.product ? props.product.refund_fee : '',
    change_product_account_items: false,
    product_account_items: props.product ? props.product.product_account_items : [],
})

const onSubmit = () => {
    let url, mergeData
    if (props.product) {
        url = route('admin.products.update', {product: props.product.id})
        mergeData = {_method: 'PUT'}
    } else {
        url = route('admin.products.store')
        mergeData = {}
    }

    form.transform(data => ({...data, ...mergeData}))
        .post(url, {
            preserveScroll: true,
            onSuccess: () => {
                $q.notify({
                    message: '保存しました',
                })
            },
            onError: (error) => {
                $q.notify({
                    type: 'negative',
                    message: Object.values(error).join(', '),
                    html: true,
                })
            }
        })
}

const addProductAccountItem = () => {
    form.product_account_items.push({
        type: PRODUCT_ACCOUNT_ITEM_TYPE.TEXT,
        name: '',
        product_account_item_options: [],
    })
}

const removeProductAccountItem = (itemKey) => {
    form.product_account_items.splice(itemKey, 1)
}

const addProductAccountOption = (itemKey) => {
    form.product_account_items[itemKey].product_account_item_options.push({
        name: '',
    })
}

const removeProductAccountOption = (itemKey, optionKey) => {
    form.product_account_items[itemKey].product_account_item_options.splice(optionKey, 1)
}

const editProductAccountItems = computed(() => {
    return !props.product || form.change_product_account_items
})
</script>

<template>
    <Head title="商品編集" />

    <AuthenticatedLayout>
        <template #header>
            商品編集
        </template>

        <q-form @submit="onSubmit">
            <q-input
                filled
                v-model="form.name"
                label="商品名"
                :error="!!form.errors.name"
                :error-message="form.errors.name"
                class="q-mt-md"
            />

            <q-file
                filled
                v-model="form.logo"
                label="ロゴ"
                clearable
                :error="!!form.errors.logo"
                :error-message="form.errors.logo"
                class="q-mt-md"
            >
                <template #prepend>
                    <q-icon name="image" />
                </template>
            </q-file>
            <q-img
                v-if="props.product?.logo_url"
                :src="props.product.logo_url"
                style="max-width: 100px;"
                class="q-mt-sm"
            />

            <q-input
                filled
                v-model="form.unit"
                label="単位"
                :error="!!form.errors.unit"
                :error-message="form.errors.unit"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.scale"
                label="小数桁数"
                :error="!!form.errors.scale"
                :error-message="form.errors.scale"
                class="q-mt-md"
            />

            <q-select
                filled
                label="ベース通貨"
                v-model="form.currency_id"
                :options="props.currencies"
                option-value="id"
                option-label="code"
                map-options
                emit-value
                :error="!!form.errors.currency_id"
                :error-message="form.errors.currency_id"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.base_currency_rate"
                label="ベース通貨レート"
                :error="!!form.errors.base_currency_rate"
                :error-message="form.errors.base_currency_rate"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.trade_fee"
                label="トレード手数料"
                :error="!!form.errors.trade_fee"
                :error-message="form.errors.trade_fee"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.refund_fee"
                label="返金手数料"
                :error="!!form.errors.refund_fee"
                :error-message="form.errors.refund_fee"
                class="q-mt-md"
            />

            <div class="row q-gutter-x-md q-mt-md">
                <div class="col">
                    <q-checkbox
                        v-if="props.product"
                        v-model="form.change_product_account_items"
                        label="商品送付先の定義を変更する"
                    />
                </div>
                <div class="col-auto">
                    <q-btn
                        v-if="editProductAccountItems"
                        icon="add"
                        label="商品送付先の項目定義"
                        color="green"
                        dense
                        @click="addProductAccountItem"
                    />
                </div>
            </div>
            <q-card
                v-for="(item, itemKey) in form.product_account_items"
                :key="itemKey"
                flat
                bordered
                class="q-mt-md"
            >
                <q-card-section class="row justify-end">
                    <q-btn
                        v-if="editProductAccountItems"
                        icon="delete"
                        label="商品送付先の項目定義"
                        color="red"
                        dense
                        @click="removeProductAccountItem(itemKey)"
                    />
                </q-card-section>
                <q-card-section class="row q-col-gutter-md">
                    <q-input
                        filled
                        v-model="item.name"
                        label="項目名"
                        :error="!!form.errors[`product_account_items.${itemKey}.name`]"
                        :error-message="form.errors[`product_account_items.${itemKey}.name`]"
                        class="col"
                        :readonly="!editProductAccountItems"
                    />
                    <q-select
                        filled
                        v-model="item.type"
                        :options="PRODUCT_ACCOUNT_ITEM_TYPE_OPTIONS"
                        map-options
                        emit-value
                        label="タイプ"
                        class="col"
                        :readonly="!editProductAccountItems"
                    />
                </q-card-section>

                <q-card-section v-if="MULTI_PRODUCT_ACCOUNT_ITEM_TYPE.includes(item.type)">
                    <div class="row justify-end">
                        <q-btn
                            v-if="editProductAccountItems"
                            icon="add"
                            label="項目のオプション定義"
                            color="green"
                            dense
                            @click="addProductAccountOption(itemKey)"
                            class="q-mt-md"
                        />
                    </div>
                    <div
                        v-for="(option, optionKey) in item.product_account_item_options"
                        :key="optionKey"
                        class="row q-mt-md"
                    >
                        <q-input
                            filled
                            v-model="option.name"
                            label="項目名"
                            :error="!!form.errors[`product_account_items.${itemKey}.product_account_item_options.${optionKey}.name`]"
                            :error-message="form.errors[`product_account_items.${itemKey}.product_account_item_options.${optionKey}.name`]"
                            class="col"
                            :readonly="!editProductAccountItems"
                        >
                            <template #append v-if="editProductAccountItems">
                                <q-btn
                                    icon="delete"
                                    color="red"
                                    dense
                                    @click="removeProductAccountOption(itemKey, optionKey)"
                                    class=""
                                />
                            </template>
                        </q-input>
                    </div>
                </q-card-section>
            </q-card>

            <div class="row q-mt-md q-gutter-x-md">
                <q-btn
                    type="submit"
                    label="保存"
                    color="primary"
                />
                <q-btn
                    type="button"
                    label="キャンセル"
                    @click="router.get(route('admin.products.index'))"
                />
            </div>
        </q-form>
    </AuthenticatedLayout>
</template>
