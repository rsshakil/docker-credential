<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    products: Object,
    product_id: String,
    name: String,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'id',
        label: '商品ID',
        field: 'id',
        align: 'left',
        sortable: true,
    },
    {
        name: 'name',
        label: '商品名',
        field: 'name',
        align: 'left',
        sortable: false,
    },
]

const searchForm = useForm({
    product_id: props.product_id,
    name: props.name,
    page: props.products.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.products.current_page,
    rowsPerPage: 10,
    rowsNumber: props.products.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.products.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.products.index'))
}
</script>

<template>
    <Head title="商品一覧" />

    <AuthenticatedLayout>
        <template #header>
            商品一覧
        </template>

        <q-form @submit="search">
            <div class="row q-col-gutter-md q-mt-md">
                <q-input
                    filled
                    v-model="searchForm.product_id"
                    label="商品ID"
                    clearable
                    class="col-4"
                />

                <q-input
                    filled
                    v-model="searchForm.name"
                    label="商品名"
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
            @click="router.visit(route('admin.products.create'))"
            color="green"
            class="q-mt-md"
        />

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.products.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.products.edit', [row.id])) }"
            wrap-cells
            class="q-mt-md"
        />
    </AuthenticatedLayout>
</template>
