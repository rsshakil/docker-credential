<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    administrators: Object,
    administrator_id: Number,
    name: String,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'id',
        label: 'アドミンユーザーID',
        field: 'id',
        align: 'left',
        sortable: true,
    },
    {
        name: 'name',
        label: '名前',
        field: 'name',
        align: 'left',
        sortable: false,
    },
]

const searchForm = useForm({
    administrator_id: props.administrator_id,
    name: props.name,
    page: props.administrators.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.administrators.current_page,
    rowsPerPage: 10,
    rowsNumber: props.administrators.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.administrators.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.administrators.index'))
}
</script>

<template>
    <Head title="アドミンユーザー一覧"/>

    <AuthenticatedLayout>
        <template #header>
            アドミンユーザー一覧
        </template>

        <q-form @submit="search">
            <div class="row q-col-gutter-md q-mt-md">
                <q-input
                    filled
                    v-model="searchForm.administrator_id"
                    label="アドミンユーザーID"
                    clearable
                    class="col-4"
                />

                <q-input
                    filled
                    v-model="searchForm.name"
                    label="名前"
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
            @click="router.visit(route('admin.administrators.create'))"
            color="green"
            class="q-mt-md"
        />

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.administrators.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.administrators.edit', [row.id])) }"
            wrap-cells
            class="q-mt-md"
        />
    </AuthenticatedLayout>
</template>
