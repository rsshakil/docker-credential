<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {STATUS_OPTIONS} from "@/Config/PostingStatus.js";
import {date} from "quasar";

const props = defineProps({
    categoryList: Array,
    announcements: Object,
    name: String,
    status: String,
    announcement_category_id: String,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'announcement_category_id',
        label: 'カテゴリ',
        field: (row) => row.announcement_category.name,
        align: 'left',
        sortable: true,
    },
    {
        name: 'name',
        label: 'タイトル',
        field: 'name',
        align: 'left',
        sortable: false,
    },
    {
        name: 'start_at',
        label: '掲載開始日時',
        field: 'start_at',
        align: 'left',
        sortable: false,
        format: (val) => date.formatDate(val, 'YYYY/MM/DD HH:mm'),
    },
    {
        name: 'end_at',
        label: '掲載終了日時',
        field: 'end_at',
        align: 'left',
        sortable: false,
        format: (val) => date.formatDate(val, 'YYYY/MM/DD HH:mm'),
    },
]

const searchForm = useForm({
    name: props.name,
    status: props.status,
    announcement_category_id: props.announcement_category_id,
    page: props.announcements.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.announcements.current_page,
    rowsPerPage: 10,
    rowsNumber: props.announcements.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.announcement.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.announcement.index'))
}
</script>

<template>
    <Head title="お知らせ一覧"/>

    <AuthenticatedLayout>
        <template #header>
            お知らせ一覧
        </template>

        <q-form @submit="search">
            <div class="row q-col-gutter-md q-mt-md">
                <q-select
                    filled
                    v-model="searchForm.announcement_category_id"
                    label="カテゴリ"
                    :options="props.categoryList"
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
                    label="お知らせ名"
                    clearable
                    class="col-4"
                />

                <q-select
                    filled
                    v-model="searchForm.status"
                    label="掲載期間"
                    :options="STATUS_OPTIONS"
                    map-options
                    emit-value
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
            @click="router.visit(route('admin.announcement.create'))"
            color="green"
            class="q-mt-md"
        />

        <q-btn
            label="カテゴリ編集"
            @click="router.visit(route('admin.announcement.create'))"
            color="green"
            class="q-ml-sm q-mt-md"
        />

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.announcements.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.announcement.edit', row)) }"
            wrap-cells
            class="q-mt-md"
        />
    </AuthenticatedLayout>
</template>
