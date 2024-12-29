<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {date} from "quasar";
import {TRADE_STATUS_TEXT} from "@/Config/TradeStatus.js";

const props = defineProps({
    trade: Object,
    tradeHistories: Object,
    sort_by: String,
    desc: Boolean,
})

const columns = [
    {
        name: 'operator',
        label: '変更者',
        field: 'operator',
        align: 'left',
        sortable: false,
        format: (val) => val.name,
    },
    {
        name: 'created_at',
        label: '時間',
        field: 'created_at',
        align: 'left',
        sortable: false,
        format: (val) => date.formatDate(val, 'YYYY/MM/DD HH:mm'),
    },
    {
        name: 'result_status',
        label: '変更後ステータス',
        field: (row) => TRADE_STATUS_TEXT[row.result_status],
        align: 'left',
        sortable: false,
    },
    {
        name: 'reason',
        label: '理由',
        field: 'reason',
        align: 'left',
        sortable: false,
    },
    {
        name: 'admin_product_account',
        label: '運営アカウント',
        field: 'admin_product_account',
        align: 'left',
        sortable: false,
        format: (val) => val?.name,
    },
]

const searchForm = useForm({
    page: props.tradeHistories.current_page,
    sort_by: props.sort_by,
    desc: props.desc,
})

const pagination = ref({
    page: props.tradeHistories.current_page,
    rowsPerPage: 10,
    rowsNumber: props.tradeHistories.total,
    sortBy: props.sort_by,
    descending: props.desc,
})

const onRequest = (requestProps) => {
    const {page, rowsPerPage, sortBy, descending} = requestProps.pagination

    searchForm.page = page
    // searchForm.per_page = rowsPerPage
    searchForm.sort_by = sortBy
    searchForm.desc = descending

    searchForm.get(route('admin.trade_histories.index', props.offer.id))
}
</script>

<template>
    <Head title="トレード履歴" />

    <AuthenticatedLayout>
        <template #header>
            トレード履歴
        </template>

        <div class="row justify-end q-col-gutter-x-md q-mt-md">
            <div class="col-auto">
                <q-btn
                    label="トレード詳細へ"
                    @click="router.visit(route('admin.trades.show', props.trade.id))"
                />
            </div>
        </div>

        <q-table
            :columns="columns"
            :rows="props.tradeHistories.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            wrap-cells
            class="q-mt-md"
        />
    </AuthenticatedLayout>
</template>
