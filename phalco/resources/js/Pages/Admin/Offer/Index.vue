<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {OFFER_TYPE_OPTIONS, OFFER_TYPE_TEXT} from "@/Config/OfferTyoe.js";
import {OFFER_STATUS_OPTIONS, OFFER_STATUS_TEXT} from "@/Config/OfferStatus.js";
import {date} from "quasar";
import {PROBLEM_REPORT_STATUS_OPTIONS, PROBLEM_REPORT_STATUS_TEXT} from "@/Config/ProblemReportStatus.js";

const props = defineProps({
    offers: Object,
    products: Array,
    offer_no: String,
    user_no: String,
    user_email: String,
    user_name: String,
    product_id: Number,
    offer_type: Number,
    offer_status: Number,
    problem_report_status: Number,
    amount_from: Number,
    amount_to: Number,
    requested_at_from: String,
    requested_at_to: String,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'offer_no',
        label: 'オファー番号',
        field: 'offer_no',
        align: 'left',
        sortable: true,
    },
    {
        name: 'name',
        label: 'ユーザー',
        field: (row) => row.user.name,
        align: 'left',
        sortable: false,
    },
    {
        name: 'product',
        label: '商品種類',
        field: (row) => row.product.name,
        align: 'left',
        sortable: false,
    },
    {
        name: 'offer_type',
        label: 'オファー種類',
        field: 'offer_type',
        align: 'left',
        sortable: false,
        format: (val) => OFFER_TYPE_TEXT[val],
    },
    {
        name: 'amount',
        label: '総数量',
        field: 'amount',
        align: 'left',
        sortable: false,
        format: (val) => val.toLocaleString(),
    },
    {
        name: 'offer_status',
        label: 'ステータス',
        field: 'offer_status',
        align: 'left',
        sortable: false,
        format: (val) => OFFER_STATUS_TEXT[val],
    },
    {
        name: 'requested_at',
        label: '申請日時',
        field: 'requested_at',
        align: 'left',
        sortable: true,
        format: (val) => date.formatDate(val, 'YYYY/MM/DD HH:mm'),
    },
    {
        name: 'problem_report_status',
        label: '問題報告ステータス',
        field: 'problem_report_status',
        align: 'left',
        sortable: false,
        format: (val) => PROBLEM_REPORT_STATUS_TEXT[val],
    },
]

const searchForm = useForm({
    offer_no: props.offer_no,
    user_no: props.user_no,
    user_email: props.user_email,
    user_name: props.user_name,
    product_id: props.product_id,
    offer_type: props.offer_type,
    offer_status: props.offer_status,
    problem_report_status: props.problem_report_status,
    amount_from: props.amount_from,
    amount_to: props.amount_to,
    requested_at_from: props.requested_at_from ? date.formatDate(props.requested_at_from, 'YYYY-MM-DD HH:mm') : null,
    requested_at_to: props.requested_at_to ? date.formatDate(props.requested_at_to, 'YYYY-MM-DD HH:mm') : null,
    page: props.offers.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.offers.current_page,
    rowsPerPage: 10,
    rowsNumber: props.offers.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.offers.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.offers.index'))
}
</script>

<template>
    <Head title="オファー一覧" />

    <AuthenticatedLayout>
        <template #header>
            オファー一覧
        </template>

        <q-form @submit="search">
            <q-list bordered class="q-mt-md">
                <q-expansion-item expand-separator icon="search" label="検索条件">
                    <q-card>
                        <q-card-section>
                            <div class="row q-col-gutter-md">
                                <q-input
                                    filled
                                    v-model="searchForm.offer_no"
                                    label="オファー番号"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.user_no"
                                    label="ユーザー番号"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.user_email"
                                    label="メールアドレス"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.user_name"
                                    label="ニックネーム"
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

                                <q-select
                                    filled
                                    v-model="searchForm.offer_type"
                                    label="オファー種類"
                                    :options="OFFER_TYPE_OPTIONS"
                                    map-options
                                    emit-value
                                    clearable
                                    class="col-4"
                                />

                                <q-select
                                    filled
                                    v-model="searchForm.offer_status"
                                    label="ステータス"
                                    :options="OFFER_STATUS_OPTIONS"
                                    map-options
                                    emit-value
                                    clearable
                                    class="col-4"
                                />

                                <q-select
                                    filled
                                    v-model="searchForm.problem_report_status"
                                    label="問題報告ステータス"
                                    :options="PROBLEM_REPORT_STATUS_OPTIONS"
                                    map-options
                                    emit-value
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.amount_from"
                                    label="総数量（開始）"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.amount_to"
                                    label="総数量（終了）"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.requested_at_from"
                                    label="申請日時（開始）"
                                    clearable
                                    class="col-4"
                                >
                                    <template #prepend>
                                        <q-icon name="event" class="cursor-pointer">
                                            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                <q-date v-model="searchForm.requested_at_from" mask="YYYY-MM-DD HH:mm">
                                                    <div class="row items-center justify-end">
                                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                                    </div>
                                                </q-date>
                                            </q-popup-proxy>
                                        </q-icon>
                                    </template>

                                    <template #append>
                                        <q-icon name="access_time" class="cursor-pointer">
                                            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                <q-time v-model="searchForm.requested_at_from" mask="YYYY-MM-DD HH:mm" format24h>
                                                    <div class="row items-center justify-end">
                                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                                    </div>
                                                </q-time>
                                            </q-popup-proxy>
                                        </q-icon>
                                    </template>
                                </q-input>

                                <q-input
                                    filled
                                    v-model="searchForm.requested_at_to"
                                    label="申請日時（終了）"
                                    clearable
                                    class="col-4"
                                >
                                    <template #prepend>
                                        <q-icon name="event" class="cursor-pointer">
                                            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                <q-date v-model="searchForm.requested_at_to" mask="YYYY-MM-DD HH:mm">
                                                    <div class="row items-center justify-end">
                                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                                    </div>
                                                </q-date>
                                            </q-popup-proxy>
                                        </q-icon>
                                    </template>

                                    <template #append>
                                        <q-icon name="access_time" class="cursor-pointer">
                                            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                <q-time v-model="searchForm.requested_at_to" mask="YYYY-MM-DD HH:mm" format24h>
                                                    <div class="row items-center justify-end">
                                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                                    </div>
                                                </q-time>
                                            </q-popup-proxy>
                                        </q-icon>
                                    </template>
                                </q-input>
                            </div>

                            <div class="row">
                                <q-btn
                                    type="submit"
                                    label="検索"
                                    color="primary"
                                    class="col q-mt-md"
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </q-expansion-item>
            </q-list>
        </q-form>

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.offers.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.offers.show', [row.id])) }"
            wrap-cells
            class="q-mt-md"
        >
            <template #body-cell-name="props">
                <q-td :props="props">
                    <div>
                        {{ props.value }}
                    </div>
                    <div>
                        {{ props.row.user.email }}
                    </div>
                </q-td>
            </template>
        </q-table>
    </AuthenticatedLayout>
</template>
