<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    currencies: Object,
    currency_id: String,
    name: String,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'id',
        label: '通貨ID',
        field: 'id',
        align: 'left',
        sortable: true,
    },
    {
        name: 'name',
        label: '通貨名',
        field: 'name',
        align: 'left',
        sortable: false,
    },
    {
        name: 'code',
        label: '通貨コード',
        field: 'code',
        align: 'left',
        sortable: false,
    },
]

const searchForm = useForm({
    currency_id: props.currency_id,
    name: props.name,
    page: props.currencies.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.currencies.current_page,
    rowsPerPage: 10,
    rowsNumber: props.currencies.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.currency.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.currency.index'))
}
</script>

<template>
    <Head title="通貨一覧"/>

    <AuthenticatedLayout>
        <template #header>
            通貨一覧
        </template>

        <q-form @submit="search">
            <div class="row q-col-gutter-md q-mt-md">
                <q-input
                    filled
                    v-model="searchForm.currency_id"
                    label="通貨ID"
                    clearable
                    class="col-4"
                />

                <q-input
                    filled
                    v-model="searchForm.name"
                    label="通貨名"
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
            @click="router.visit(route('admin.currency.create'))"
            color="green"
            class="q-mt-md"
        />

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.currencies.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.currency.edit', row)) }"
            wrap-cells
            class="q-mt-md"
        />
    </AuthenticatedLayout>
</template>
