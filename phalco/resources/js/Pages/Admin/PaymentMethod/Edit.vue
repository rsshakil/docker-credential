<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {computed} from "vue";
import {MULTI_PAYMENT_ITEM_TYPE, PAYMENT_ITEM_TYPE, PAYMENT_ITEM_TYPE_OPTIONS} from "@/Config/PaymentItemType.js";

const $q = useQuasar()

const props = defineProps({
    paymentMethod: Object,
    currencies: Array,
})

const form = useForm({
    name: props.paymentMethod ? props.paymentMethod.name : '',
    logo: null,
    currency_id: props.paymentMethod ? props.paymentMethod.currency_id : null,
    change_payment_items: false,
    payment_items: props.paymentMethod ? props.paymentMethod.payment_items : [],
})

const onSubmit = () => {
    let url, mergeData
    if (props.paymentMethod) {
        url = route('admin.payment_methods.update', {payment_method: props.paymentMethod.id})
        mergeData = {_method: 'PUT'}
    } else {
        url = route('admin.payment_methods.store')
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

const addPaymentItem = () => {
    form.payment_items.push({
        type: PAYMENT_ITEM_TYPE.TEXT,
        name: '',
        payment_options: [],
    })
}

const removePaymentItem = (itemKey) => {
    form.payment_items.splice(itemKey, 1)
}

const addPaymentOption = (itemKey) => {
    form.payment_items[itemKey].payment_options.push({
        name: '',
    })
}

const removePaymentOption = (itemKey, optionKey) => {
    form.payment_items[itemKey].payment_options.splice(optionKey, 1)
}

const editPaymentItems = computed(() => {
    return !props.paymentMethod || form.change_payment_items
})
</script>

<template>
    <Head title="支払方法編集" />

    <AuthenticatedLayout>
        <template #header>
            支払方法編集
        </template>

        <q-form @submit="onSubmit">
            <q-input
                filled
                v-model="form.name"
                label="支払方法名"
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
                v-if="props.paymentMethod?.logo_url"
                :src="props.paymentMethod.logo_url"
                style="max-width: 100px;"
                class="q-mt-sm"
            />

            <q-select
                filled
                label="通貨"
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

            <div class="row q-gutter-x-md q-mt-md">
                <div class="col">
                    <q-checkbox
                        v-if="props.paymentMethod"
                        v-model="form.change_payment_items"
                        label="支払方法の定義を変更する"
                    />
                </div>
                <div class="col-auto">
                    <q-btn
                        v-if="editPaymentItems"
                        icon="add"
                        label="支払方法の項目定義"
                        color="green"
                        dense
                        @click="addPaymentItem"
                    />
                </div>
            </div>
            <q-card
                v-for="(item, itemKey) in form.payment_items"
                :key="itemKey"
                flat
                bordered
                class="q-mt-md"
            >
                <q-card-section class="row justify-end">
                    <q-btn
                        v-if="editPaymentItems"
                        icon="delete"
                        label="支払方法の項目定義"
                        color="red"
                        dense
                        @click="removePaymentItem(itemKey)"
                    />
                </q-card-section>
                <q-card-section class="row q-col-gutter-md">
                    <q-input
                        filled
                        v-model="item.name"
                        label="項目名"
                        :error="!!form.errors[`payment_items.${itemKey}.name`]"
                        :error-message="form.errors[`payment_items.${itemKey}.name`]"
                        class="col"
                        :readonly="!editPaymentItems"
                    />
                    <q-select
                        filled
                        v-model="item.type"
                        :options="PAYMENT_ITEM_TYPE_OPTIONS"
                        map-options
                        emit-value
                        label="タイプ"
                        class="col"
                        :readonly="!editPaymentItems"
                    />
                </q-card-section>

                <q-card-section v-if="MULTI_PAYMENT_ITEM_TYPE.includes(item.type)">
                    <div class="row justify-end">
                        <q-btn
                            v-if="editPaymentItems"
                            icon="add"
                            label="項目のオプション定義"
                            color="green"
                            dense
                            @click="addPaymentOption(itemKey)"
                            class="q-mt-md"
                        />
                    </div>
                    <div
                        v-for="(option, optionKey) in item.payment_options"
                        :key="optionKey"
                        class="row q-mt-md"
                    >
                        <q-input
                            filled
                            v-model="option.name"
                            label="項目名"
                            :error="!!form.errors[`payment_items.${itemKey}.payment_options.${optionKey}.name`]"
                            :error-message="form.errors[`payment_items.${itemKey}.payment_options.${optionKey}.name`]"
                            class="col"
                            :readonly="!editPaymentItems"
                        >
                            <template #append v-if="editPaymentItems">
                                <q-btn
                                    icon="delete"
                                    color="red"
                                    dense
                                    @click="removePaymentOption(itemKey, optionKey)"
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
                    @click="router.get(route('admin.payment_methods.index'))"
                />
            </div>
        </q-form>
    </AuthenticatedLayout>
</template>
