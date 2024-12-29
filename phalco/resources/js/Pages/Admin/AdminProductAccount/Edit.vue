<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {PRODUCT_ACCOUNT_ITEM_TYPE} from "@/Config/ProductAccountItemType.js";

const $q = useQuasar()

const props = defineProps({
    adminProductAccount: Object,
    products: Array,
})

const form = useForm({
    name: props.adminProductAccount ? props.adminProductAccount.name : '',
    product_id: props.adminProductAccount ? props.adminProductAccount.product_id : null,
    is_temporary: props.adminProductAccount ? props.adminProductAccount.is_temporary : false,
    is_send: props.adminProductAccount ? props.adminProductAccount.is_send : false,
    admin_product_account_items: props.adminProductAccount ?
        props.adminProductAccount.admin_product_account_items.map(item => {
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
                    data.value = item.value_url
            }
            return data;
        }) :
        [],
})

const onSubmit = () => {
    let url, mergeData
    if (props.adminProductAccount) {
        url = route('admin.admin_product_accounts.update', {admin_product_account: props.adminProductAccount.id})
        mergeData = {_method: 'PUT'}
    } else {
        url = route('admin.admin_product_accounts.store')
        mergeData = {}
    }

    form
        .transform(data => ({
            ...mergeData,
            name: data.name,
            product_id: data.product_id,
            is_temporary: data.is_temporary,
            is_send: data.is_send,
            admin_product_account_items: data.admin_product_account_items.map(e => ({
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
    form.admin_product_account_items = props.products.find(e => e.id === form.product_id).product_account_items.map(item => ({
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
    <Head title="運営アカウント編集" />

    <AuthenticatedLayout>
        <template #header>
            運営アカウント編集
        </template>

        <q-form @submit="onSubmit">
            <q-input
                filled
                v-model="form.name"
                label="運営アカウント名"
                :error="!!form.errors.name"
                :error-message="form.errors.name"
                class="q-mt-md"
            />

            <q-select
                filled
                label="商品"
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
                :readonly="!!props.adminProductAccount"
            />

            <div class="q-mt-md">
                <q-checkbox
                    v-model="form.is_temporary"
                    label="一時預かり用"
                    :disable="!!props.adminProductAccount"
                />

                <q-checkbox
                    v-model="form.is_send"
                    label="送付用"
                    :disable="!!props.adminProductAccount"
                />
            </div>

            <template v-for="(item, itemKey) in form.admin_product_account_items">
                <template v-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.TEXT">
                    <q-input
                        filled
                        v-model="item.value"
                        :label="item.label"
                        :error="!!form.errors[`admin_product_account_items.${itemKey}.value`]"
                        :error-message="form.errors[`admin_product_account_items.${itemKey}.value`]"
                        class="q-mt-md q-ml-lg"
                        :disable="!!props.adminProductAccount"
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
                        :error="!!form.errors[`admin_product_account_items.${itemKey}.value`]"
                        :error-message="form.errors[`admin_product_account_items.${itemKey}.value`]"
                        class="q-mt-md q-ml-lg"
                        :disable="!!props.adminProductAccount"
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
                        :disable="!!props.adminProductAccount"
                    />
                    <div
                        v-if="!!form.errors[`admin_product_account_items.${itemKey}.value`]"
                        class="q-ml-xl text-negative text-caption"
                    >
                        {{ form.errors[`admin_product_account_items.${itemKey}.value`] }}
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
                        :disable="!!props.adminProductAccount"
                    />
                    <div
                        v-if="!!form.errors[`admin_product_account_items.${itemKey}.value`]"
                        class="q-ml-xl text-negative text-caption"
                    >
                        {{ form.errors[`admin_product_account_items.${itemKey}.value`] }}
                    </div>
                </template>
                <template v-else-if="item.type === PRODUCT_ACCOUNT_ITEM_TYPE.IMAGE">
                    <q-file
                        v-if="!props.adminProductAccount"
                        filled
                        v-model="item.value"
                        :label="item.label"
                        :error="!!form.errors[`admin_product_account_items.${itemKey}.value`]"
                        :error-message="form.errors[`admin_product_account_items.${itemKey}.value`]"
                        class="q-mt-md q-ml-lg"

                    />
                    <template v-else>
                        <div class="q-mt-md q-ml-lg">{{ item.label }}</div>
                        <q-img
                            :src="item.value"
                            style="max-width: 300px;"
                            class="q-mt-xs q-ml-lg"
                        />
                    </template>
                </template>
            </template>

            <div class="row q-mt-md q-gutter-x-md">
                <q-btn
                    type="submit"
                    label="保存"
                    color="primary"
                />
                <q-btn
                    type="button"
                    label="キャンセル"
                    @click="router.get(route('admin.admin_product_accounts.index'))"
                />
            </div>
        </q-form>
    </AuthenticatedLayout>
</template>
