<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";
import { PRODUCT_ACCOUNT_ITEM_TYPE } from "@/Config/ProductAccountItemType";

const $q = useQuasar()

const props = defineProps({
    products: Array,
    userProductAccount: Object
})

const form = useForm({
    id: props.userProductAccount ? props.userProductAccount.id : null,
    product_id: props.userProductAccount ? props.userProductAccount.product_id : null,
    user_product_account_items: props.userProductAccount ?
        props.userProductAccount.user_product_account_items.map(item => {
            const data = {
                // ↓ 定義参照用 ↓
                label: item.product_account_item.name,
                options: item.product_account_item.product_account_item_options.map(option => ({label: option.name, value: option.id})),
                // ↑ 定義参照用 ↑
                type: item.product_account_item.type,
                product_account_item_id: item.product_account_item.id,
            };
            switch (item.product_account_item.type) {
                case PRODUCT_ACCOUNT_ITEM_TYPE.TEXT:
                    data.value = item.value
                    break
                case PRODUCT_ACCOUNT_ITEM_TYPE.SELECT:
                case PRODUCT_ACCOUNT_ITEM_TYPE.RADIO:
                    data.value = item.product_account_item_options[0].id
                    break
                case PRODUCT_ACCOUNT_ITEM_TYPE.CHECKBOX:
                    data.value = item.product_account_item_options.map(option => option.id)
                    break
                case PRODUCT_ACCOUNT_ITEM_TYPE.IMAGE:
                    data.value = item.value
                    data.value_url = item.value_url
            }
            return data;
        }) :
        [],
})

const onSubmit = () => {
    let url, mergeData
    if (props.userProductAccount) {
        url = route('user_product_account.update', {userProductAccount: props.userProductAccount.id})
        mergeData = {_method: 'PUT'}
    } else {
        url = route('user_product_account.store')
        mergeData = {}
    }

    form
        .transform(data => ({
            ...mergeData,
            id: data.id,
            product_id: data.product_id,
            user_product_account_items: data.user_product_account_items.map(e => ({
                type: e.type,
                product_account_item_id: e.product_account_item_id,
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

const onUpdateProduct = () => {
    form.user_product_account_items = props.products.find(e => e.id === form.product_id).product_account_items.map(item => ({
        // ↓ 定義参照用 ↓
        label: item.name,
        options: item.product_account_item_options.map(option => ({label: option.name, value: option.id})),
        // ↑ 定義参照用 ↑
        type: item.type,
        product_account_item_id: item.id,
        value: item.type === PRODUCT_ACCOUNT_ITEM_TYPE.CHECKBOX ? [] : null,
    }))
}
</script>
<template>
    <Head title="商品送付先追加" />
    <MenuLayout>
        <div class="row">
            <h6>商品送付先情報を追加する</h6>
        </div>
        <q-card>
            <q-form @submit="onSubmit">
                <span>商品種類</span>
                <q-select
                    filled
                    v-model="form.product_id"
                    :options="props.products"
                    option-value="id"
                    option-label="name"
                    map-options
                    emit-value
                    @update:model-value="onUpdateProduct"
                    :error="!!form.errors.product_id"
                    :error-message="form.errors.product_id"
                    class="q-mt-md"
                    :readonly="!!props.userProductAccount"
                />

                <template v-for="(item, itemKey) in form.user_product_account_items">
                    <template v-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.TEXT">
                        <q-input
                            filled
                            v-model="item.value"
                            :label="item.label"
                            :error="!!form.errors[`user_product_account_items.${itemKey}.value`]"
                            :error-message="form.errors[`user_product_account_items.${itemKey}.value`]"
                            class="q-mt-md q-ml-lg"
                        />
                    </template>
                    <template v-else-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.SELECT">
                        <q-select
                            filled
                            v-model="item.value"
                            :label="item.label"
                            :options="item.options"
                            emit-value
                            map-options
                            :error="!!form.errors[`user_product_account_items.${itemKey}.value`]"
                            :error-message="form.errors[`user_product_account_items.${itemKey}.value`]"
                            class="q-mt-md q-ml-lg"
                        />
                    </template>
                    <template v-else-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.CHECKBOX">
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
                            v-if="!!form.errors[`user_product_account_items.${itemKey}.value`]"
                            class="q-ml-xl text-negative text-caption"
                        >
                            {{ form.errors[`user_product_account_items.${itemKey}.value`] }}
                        </div>
                    </template>
                    <template v-else-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.RADIO">
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
                            v-if="!!form.errors[`user_product_account_items.${itemKey}.value`]"
                            class="q-ml-xl text-negative text-caption"
                        >
                            {{ form.errors[`user_product_account_items.${itemKey}.value`] }}
                        </div>
                    </template>
                    <template v-else-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.IMAGE">
                        <q-file
                            filled
                            v-model="item.value"
                            :label="item.label"
                            :error="!!form.errors[`user_product_account_items.${itemKey}.value`]"
                            :error-message="form.errors[`user_product_account_items.${itemKey}.value`]"
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
                        @click="router.get(route('user_product_account.show'))"
                    />
                    <q-btn
                        type="submit"
                        :label="props.userProductAccount? '更新':'追加'"
                        color="primary"
                    />
                </div>
            </q-form>
        </q-card>
    </MenuLayout>
</template>

