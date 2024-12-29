<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {OFFER_TYPE_OPTIONS, OFFER_TYPE_TEXT} from "@/Config/OfferTyoe.js";
import {date} from "quasar";
import {PROBLEM_REPORT_STATUS_OPTIONS, PROBLEM_REPORT_STATUS_TEXT} from "@/Config/ProblemReportStatus.js";
import {TRADE_STATUS_OPTIONS, TRADE_STATUS_TEXT} from "@/Config/TradeStatus.js";

const props = defineProps({
    trades: Object,
    products: Array,
    trade_no: String,
    buyer_no: String,
    buyer_name: String,
    buyer_email: String,
    seller_no: String,
    seller_name: String,
    seller_email: String,
    product_id: Number,
    offer_type: Number,
    trade_status: Number,
    buyer_problem_report_status: Number,
    seller_problem_report_status: Number,
    sort_by: String,
    desc: Boolean,
})

const tableRef = ref(null)

const columns = [
    {
        name: 'trade_no',
        label: 'トレード番号',
        field: 'trade_no',
        align: 'left',
        sortable: true,
    },
    {
        name: 'product',
        label: '商品種類',
        field: 'product',
        align: 'left',
        sortable: false,
        format: (val) => val.name,
    },
    {
        name: 'offer_type',
        label: 'オファー種類',
        field: (row) => row.offer.offer_type,
        align: 'left',
        sortable: false,
        format: (val) => OFFER_TYPE_TEXT[val],
    },
    {
        name: 'trade_amount',
        label: '数量',
        field: 'trade_amount',
        align: 'left',
        sortable: false,
        format: (val) => val.toLocaleString(),
    },
    {
        name: 'trade_status',
        label: 'ステータス',
        field: 'trade_status',
        align: 'left',
        sortable: false,
        format: (val) => TRADE_STATUS_TEXT[val],
    },
    {
        name: 'requested_at',
        label: '開始日時',
        field: 'requested_at',
        align: 'left',
        sortable: true,
        format: (val) => date.formatDate(val, 'YYYY/MM/DD HH:mm'),
    },
    {
        name: 'buyer',
        label: 'Buyer',
        field: (row) => row.buyer,
        align: 'left',
        sortable: false,
        format: (val) => val.name,
    },
    {
        name: 'seller',
        label: 'Seller',
        field: (row) => row.seller,
        align: 'left',
        sortable: false,
        format: (val) => val.name,
    },
    {
        name: 'buyer_problem_report_status',
        label: 'Buyer問題報告ステータス',
        field: 'buyer_problem_report_status',
        align: 'left',
        sortable: false,
        format: (val) => PROBLEM_REPORT_STATUS_TEXT[val],
    },
    {
        name: 'seller_problem_report_status',
        label: 'Seller問題報告ステータス',
        field: 'seller_problem_report_status',
        align: 'left',
        sortable: false,
        format: (val) => PROBLEM_REPORT_STATUS_TEXT[val],
    },
]

const searchForm = useForm({
    trade_no: props.trade_no,
    buyer_no: props.buyer_no,
    buyer_name: props.buyer_name,
    buyer_email: props.buyer_email,
    seller_no: props.seller_no,
    seller_name: props.seller_name,
    seller_email: props.seller_email,
    product_id: props.product_id,
    offer_type: props.offer_type,
    trade_status: props.trade_status,
    buyer_problem_report_status: props.buyer_problem_report_status,
    seller_problem_report_status: props.seller_problem_report_status,
    page: props.trades.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.trades.current_page,
    rowsPerPage: 10,
    rowsNumber: props.trades.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (props) => {
    const {page, rowsPerPage, sortBy, descending} = props.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.trades.index'))
}

const search = () => {
    searchForm.page = 1
    searchForm.get(route('admin.trades.index'))
}
</script>

<template>
    <Head title="トレード一覧" />

    <AuthenticatedLayout>
        <template #header>
            トレード一覧
        </template>

        <q-form @submit="search">
            <q-list bordered class="q-mt-md">
                <q-expansion-item expand-separator icon="search" label="検索条件">
                    <q-card>
                        <q-card-section>
                            <div class="row q-col-gutter-md">
                                <q-input
                                    filled
                                    v-model="searchForm.trade_no"
                                    label="トレード番号"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.buyer_no"
                                    label="Buyer 番号"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.buyer_name"
                                    label="Buyer ニックネーム"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.buyer_email"
                                    label="Buyer メールアドレス"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.seller_no"
                                    label="Seller 番号"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.seller_name"
                                    label="Seller ニックネーム"
                                    clearable
                                    class="col-4"
                                />

                                <q-input
                                    filled
                                    v-model="searchForm.seller_email"
                                    label="Seller メールアドレス"
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
                                    v-model="searchForm.trade_status"
                                    label="ステータス"
                                    :options="TRADE_STATUS_OPTIONS"
                                    map-options
                                    emit-value
                                    clearable
                                    class="col-4"
                                />

                                <q-select
                                    filled
                                    v-model="searchForm.buyer_problem_report_status"
                                    label="Buyer 問題報告ステータス"
                                    :options="PROBLEM_REPORT_STATUS_OPTIONS"
                                    map-options
                                    emit-value
                                    clearable
                                    class="col-4"
                                />

                                <q-select
                                    filled
                                    v-model="searchForm.seller_problem_report_status"
                                    label="Seller 問題報告ステータス"
                                    :options="PROBLEM_REPORT_STATUS_OPTIONS"
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
                        </q-card-section>
                    </q-card>
                </q-expansion-item>
            </q-list>
        </q-form>

        <q-table
            ref="tableRef"
            :columns="columns"
            :rows="props.trades.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            @row-click="(evt, row, index) => { router.visit(route('admin.trades.show', [row.id])) }"
            wrap-cells
            class="q-mt-md"
        >
            <template #body-cell-buyer="props">
                <q-td :props="props">
                    <div>
                        {{ props.value }}
                    </div>
                    <div>
                        {{ props.row.buyer.email }}
                    </div>
                </q-td>
            </template>
            <template #body-cell-seller="props">
                <q-td :props="props">
                    <div>
                        {{ props.value }}
                    </div>
                    <div>
                        {{ props.row.seller.email }}
                    </div>
                </q-td>
            </template>
        </q-table>
    </AuthenticatedLayout>
</template>
