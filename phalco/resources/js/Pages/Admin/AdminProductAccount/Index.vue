<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    adminProductAccounts: Object,
    products: Array,
    admin_product_account_id: String,
    name: String,
    product_id: Number,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'id',
        label: '運営アカウントID',
        field: 'id',
        align: 'left',
        sortable: true,
    },
    {
        name: 'product',
        label: '商品種類',
        field: 'product',
        align: 'left',
        sortable: false,
        format: val => val.name,
    },
    {
        name: 'name',
        label: '運営アカウント名',
        field: 'name',
        align: 'left',
        sortable: false,
    },
    {
        name: 'is_temporary',
        label: '一時預かり用',
        field: 'is_temporary',
        align: 'left',
        sortable: false,
    },
    {
        name: 'is_send',
        label: '送付用',
        field: 'is_send',
        align: 'left',
        sortable: false,
    },
]

const searchForm = useForm({
    admin_product_account_id: props.admin_product_account_id,
    name: props.name,
    product_id: props.product_id,
    page: props.adminProductAccounts.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.adminProductAccounts.current_page,
    rowsPerPage: 10,
    rowsNumber: props.adminProductAccounts.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.admin_product_accounts.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.admin_product_accounts.index'))
}
</script>

<template>
    <Head title="運営アカウント一覧" />

    <AuthenticatedLayout>
        <template #header>
            運営アカウント一覧
        </template>

        <q-form @submit="search">
            <div class="row q-col-gutter-md q-mt-md">
                <q-input
                    filled
                    v-model="searchForm.admin_product_account_id"
                    label="運営アカウントID"
                    clearable
                    class="col-4"
                />

                <q-select
                    filled
                    v-model="searchForm.product_id"
                    label="商品種類"
                    :options="props.products"
                    option-value="id"
                    option-label="name"
                    map-options
                    emit-value
                    clearable
                    class="col-4"
                />

                <q-input
                    filled
                    v-model="searchForm.name"
                    label="運営アカウント名"
                    clearable
                    class="col-4"
                />
            </div>
            <div class="row">
                <q-btn
                    type="submit"
                    label="検索"
                    color="primary"
                    class="col q-mt-md"
                />
            </div>
        </q-form>

        <q-btn
            label="新規"
            @click="router.visit(route('admin.admin_product_accounts.create'))"
            color="green"
            class="q-mt-md"
        />

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.adminProductAccounts.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.admin_product_accounts.edit', [row.id])) }"
            wrap-cells
            class="q-mt-md"
        >
            <template #body-cell-is_temporary="props">
                <q-td :props="props">
                    <q-icon v-if="props.value" name="check" color="green" size="sm"/>
                </q-td>
            </template>
            <template #body-cell-is_send="props">
                <q-td :props="props">
                    <q-icon v-if="props.value" name="check" color="green" size="sm"/>
                </q-td>
            </template>
        </q-table>
    </AuthenticatedLayout>
</template>
