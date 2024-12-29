<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {USER_STATUS_OPTIONS, USER_STATUS_TEXT} from "@/Config/UserStatus.js";
import {KYC_STATUS_OPTIONS, KYC_STATUS_TEXT} from "@/Config/KycStatus.js";

const props = defineProps({
    users: Object,
    user_no: String,
    email: String,
    name: String,
    user_status: Number,
    kyc_status: Number,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'user_no',
        label: 'ユーザー番号',
        field: 'user_no',
        align: 'left',
        sortable: true,
    },
    {
        name: 'name',
        label: 'ユーザー名',
        field: 'name',
        align: 'left',
        sortable: false,
    },
    {
        name: 'email',
        label: 'メールアドレス',
        field: 'email',
        align: 'left',
        sortable: false,
    },
    {
        name: 'user_status',
        label: 'ユーザーステータス',
        field: 'user_status',
        align: 'left',
        sortable: false,
        format: (val) => USER_STATUS_TEXT[val],
    },
    {
        name: 'kyc_status',
        label: 'KYCステータス',
        field: 'kyc_status',
        align: 'left',
        sortable: false,
        format: (val) => KYC_STATUS_TEXT[val],
    },
]

const searchForm = useForm({
    user_no: props.user_no,
    email: props.email,
    name: props.name,
    user_status: props.user_status,
    kyc_status: props.kyc_status,
    page: props.users.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.users.current_page,
    rowsPerPage: 10,
    rowsNumber: props.users.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.users.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.users.index'))
}
</script>

<template>
    <Head title="ユーザー一覧" />

    <AuthenticatedLayout>
        <template #header>
            ユーザー一覧
        </template>

        <q-form @submit="search">
            <div class="row q-col-gutter-md q-mt-md">
                <q-input
                    filled
                    v-model="searchForm.user_no"
                    label="ユーザー番号"
                    clearable
                    class="col-4"
                />

                <q-input
                    filled
                    v-model="searchForm.email"
                    label="メールアドレス"
                    clearable
                    class="col-4"
                />

                <q-input
                    filled
                    v-model="searchForm.name"
                    label="ユーザー名"
                    clearable
                    class="col-4"
                />

                <q-select
                    filled
                    v-model="searchForm.user_status"
                    label="ユーザーステータス"
                    :options="USER_STATUS_OPTIONS"
                    map-options
                    emit-value
                    clearable
                    class="col-4"
                />

                <q-select
                    filled
                    v-model="searchForm.kyc_status"
                    label="KYCステータス"
                    :options="KYC_STATUS_OPTIONS"
                    map-options
                    emit-value
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

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.users.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.users.show', [row.id])) }"
            wrap-cells
            class="q-mt-md"
        />
    </AuthenticatedLayout>
</template>
