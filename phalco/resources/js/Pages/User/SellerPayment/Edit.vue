<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";
import { PAYMENT_ITEM_TYPE } from "@/Config/PaymentItemType";

const $q = useQuasar()

const props = defineProps({
    paymentMethods: Array,
    sellerPayment: Object
})

const form = useForm({
    id: props.sellerPayment ? props.sellerPayment.id : null,
    paymentMethod_id: props.sellerPayment ? props.sellerPayment.payment_method_id : null,
    seller_payment_items: props.sellerPayment ?
        props.sellerPayment.seller_payment_items.map(item => {
            const data = {
                // ↓ 定義参照用 ↓
                label: item.payment_item.name,
                options: item.payment_item.payment_options.map(option => ({label: option.name, value: option.id})),
                // ↑ 定義参照用 ↑
                type: item.payment_item.type,
                payment_item_id: item.payment_item.id,
            };
            switch (item.payment_item.type) {
                case PAYMENT_ITEM_TYPE.TEXT:
                    data.value = item.value
                    break
                case PAYMENT_ITEM_TYPE.SELECT:
                case PAYMENT_ITEM_TYPE.RADIO:
                    data.value = item.payment_options[0].id
                    break
                case PAYMENT_ITEM_TYPE.CHECKBOX:
                    data.value = item.payment_options.map(option => option.id)
                    break
                case PAYMENT_ITEM_TYPE.IMAGE:
                    data.value = item.value
                    data.value_url = item.value_url
            }
            return data;
        }) :
        [],
})

const onSubmit = () => {
    let url, mergeData
    if (props.sellerPayment) {
        url = route('seller_payment.update', {sellerPayment: props.sellerPayment.id})
        mergeData = {_method: 'PUT'}
    } else {
        url = route('seller_payment.store')
        mergeData = {}
    }

    form
        .transform(data => ({
            ...mergeData,
            id: data.id,
            paymentMethod_id: data.paymentMethod_id,
            seller_payment_items: data.seller_payment_items.map(e => ({
                type: e.type,
                payment_item_id: e.payment_item_id,
                value: e.value,
            })),
        }))
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

const onUpdatePaymentMethod = () => {
    form.seller_payment_items = props.paymentMethods.find(e => e.id === form.paymentMethod_id).payment_items.map(item => ({
        // ↓ 定義参照用 ↓
        label: item.name,
        options: item.payment_options.map(option => ({label: option.name, value: option.id})),
        // ↑ 定義参照用 ↑
        type: item.type,
        payment_item_id: item.id,
        value: item.type === PAYMENT_ITEM_TYPE.CHECKBOX ? [] : null,
    }))
}
</script>
<template>
    <Head title="支払先情報追加" />
    <MenuLayout>
        <div class="row">
            <h6>支払先情報を追加する</h6>
        </div>
        <q-card>
            <q-form @submit="onSubmit">
                <span>支払方法</span>
                <q-select
                    filled
                    v-model="form.paymentMethod_id"
                    :options="props.paymentMethods"
                    option-value="id"
                    option-label="name"
                    map-options
                    emit-value
                    @update:model-value="onUpdatePaymentMethod"
                    :error="!!form.errors.paymentMethod_id"
                    :error-message="form.errors.paymentMethod_id"
                    class="no-padding"
                    :disable="!!props.sellerPayment"
                />

                <template v-for="(item, itemKey) in form.seller_payment_items">
                    <template v-if="item.type === PAYMENT_ITEM_TYPE.TEXT">
                        <q-input
                            filled
                            v-model="item.value"
                            :label="item.label"
                            :error="!!form.errors[`seller_payment_items.${itemKey}.value`]"
                            :error-message="form.errors[`seller_payment_items.${itemKey}.value`]"
                            class="q-mt-md q-ml-lg"
                        />
                    </template>
                    <template v-else-if="item.type === PAYMENT_ITEM_TYPE.SELECT">
                        <q-select
                            filled
                            v-model="item.value"
                            :label="item.label"
                            :options="item.options"
                            emit-value
                            map-options
                            :error="!!form.errors[`seller_payment_items.${itemKey}.value`]"
                            :error-message="form.errors[`seller_payment_items.${itemKey}.value`]"
                            class="q-mt-md q-ml-lg"
                        />
                    </template>
                    <template v-else-if="item.type === PAYMENT_ITEM_TYPE.CHECKBOX">
                        <div class="q-mt-md q-ml-lg">{{ item.label }}</div>
                        <q-option-group
                            type="checkbox"
                            filled
                            inline
                            v-model="item.value"
                            :options="item.options"
                            class="q-mt-xs q-ml-lg"
                        />
                        <div
                            v-if="!!form.errors[`seller_payment_items.${itemKey}.value`]"
                            class="q-ml-xl text-negative text-caption"
                        >
                            {{ form.errors[`seller_payment_items.${itemKey}.value`] }}
                        </div>
                    </template>
                    <template v-else-if="item.type === PAYMENT_ITEM_TYPE.RADIO">
                        <div class="q-mt-md q-ml-lg">{{ item.label }}</div>
                        <q-option-group
                            type="radio"
                            filled
                            inline
                            v-model="item.value"
                            :options="item.options"
                            class="q-mt-xs q-ml-lg"
                        />
                        <div
                            v-if="!!form.errors[`seller_payment_items.${itemKey}.value`]"
                            class="q-ml-xl text-negative text-caption"
                        >
                            {{ form.errors[`seller_payment_items.${itemKey}.value`] }}
                        </div>
                    </template>
                    <template v-else-if="item.type === PAYMENT_ITEM_TYPE.IMAGE">
                        <q-file
                            filled
                            v-model="item.value"
                            :label="item.label"
                            :error="!!form.errors[`seller_payment_items.${itemKey}.value`]"
                            :error-message="form.errors[`seller_payment_items.${itemKey}.value`]"
                            class="q-mt-md q-ml-lg"
                        />
                        <template v-if="typeof item.value == 'string'">
                            <div class="q-mt-md q-ml-lg">{{ item.label }}</div>
                            <q-img
                                :src="item.value_url"
                                style="max-width: 300px;"
                                class="q-mt-xs q-ml-lg"
                            />
                        </template>
                    </template>
                </template>
                <div class="row q-mt-md q-gutter-x-md">
                    <q-btn 
                        flat 
                        label="キャンセル" 
                        @click="router.get(route('seller_payment.show'))"
                    />
                    <q-btn
                        type="submit"
                        :label="props.sellerPayment? '更新':'追加'"
                        color="primary"
                    />
                </div>
            </q-form>
        </q-card>
    </MenuLayout>
</template>

