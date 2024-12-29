<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import { ref, watch } from "vue";
import { OFFER_TYPE } from "@/Config/OfferTyoe.js";
import { useQuasar } from "quasar";
import OfferTypeSelect from "./Partials/OfferTypeSelect.vue";
import Rate from "./Partials/Rate.vue";
import RemainingProduct from "./Partials/RemainingProduct.vue";
import TradingRange from "./Partials/TradingRange.vue";
import PaymentMethodList from "./Partials/PaymentMethodList.vue";

const $q = useQuasar();

const props = defineProps({
    offers: Object,
    offerType: Number,
    products: Array,
    product: [Object, null],
});

const columns = [
    {
        name: "name",
        label: "名前",
        align: "left",
        sortable: false,
    },
    {
        name: "rate",
        label: "レート",
        align: "left",
        sortable: false,
    },
    {
        name: "amount",
        label: "商品残り|取引可能範囲",
        align: "left",
        sortable: false,
    },
    {
        name: "payment",
        label: "支払方法",
        align: "left",
        sortable: false,
    },
    {
        name: "action",
        label: "",
        align: "left",
        sortable: false,
    },
];

const pagination = ref({
    page: props.offers.current_page,
    rowsPerPage: 10,
    rowsNumber: props.offers.total,
    sortBy: "id",
    descending: false,
});

const searchForm = useForm({
    offerType: props.offerType,
    product: props.product
        ? { label: props.product.name, value: props.product.id }
        : null,
    page: props.offers.current_page,
});

const onRequest = (props) => {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;

    searchForm.page = page;
    searchForm.perPage = rowsPerPage;
    searchForm.sortBy = sortBy;
    searchForm.desc = descending;

    searchForm.get(route("offers.index"));
};
const handlePageChange = (page) => {
    searchForm.page = page;
    searchForm.get(route("offers.index"));
};


const handleOfferTypeChange = (offerType) => {
    searchForm.page = 1;
    searchForm.offerType = offerType;
    searchForm.get(route("offers.index"));
};
</script>

<template>
    <Head title="オファー一覧" />

    <MenuLayout>
        <div class="row q-gutter-lg">
            <div class="col">
                <div class="row">
                    <div class="col-auto items-left">
                        <OfferTypeSelect v-model="searchForm.offerType" @update:modelValue="handleOfferTypeChange" />
                    </div>
                    <div class="col-auto q-ml-md">
                        <q-select
                            outlined
                            v-model="searchForm.product"
                            :options="props.products.map((e) => ({label: e.name,value: e.id,}))"
                            map-options
                            emit-value
                            @update:modelValue="searchForm.get(route('offers.index'))"
                            clearable
                            class="smaller-q-selectField q-field-common"
                        >
                            <q-item v-if="!searchForm.product">
                                <q-item-section> 商品種類 </q-item-section>
                            </q-item>
                        </q-select>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="col-auto" align="right">
                    <q-btn
                        label="オファー掲載"
                        @click="router.visit(route('offers.create', { offerType: OFFER_TYPE.SELL, }) )"
                        icon="add"
                        class="button-add"
                    />
                </div>
            </div>
        </div>

        <q-table
            v-if="$q.screen.gt.sm"
            :columns="columns"
            :rows="props.offers.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            wrap-cells
            class="q-mt-md"
        >
            <template #body-cell-name="props">
                <q-td :props="props">
                    <div>
                        <span class="label-customer-name">{{
                            props.row.user.name
                        }}</span>
                        <q-badge rounded color="green" class="q-ml-sm" />
                    </div>
                    <div>
                        <q-icon name="o_timer" />{{ props.row.time_limit }}分 | xx%
                    </div>
                </q-td>
            </template>
            <template #body-cell-rate="props">
                <q-td :props="props">
                    <Rate :offer="props.row" />
                </q-td>
            </template>
            <template #body-cell-amount="props">
                <q-td :props="props">
                    <RemainingProduct :offer="props.row" />
                    <TradingRange :offer="props.row" />
                </q-td>
            </template>
            <template #body-cell-payment="props">
                <q-td :props="props" class="q-gutter-x-sm">
                    <PaymentMethodList
                        :list="props.row.currency?.payment_methods"
                    />
                </q-td>
            </template>
            <template #body-cell-action="props">
                <q-td :props="props">
                    <q-btn
                        :label="props.offerType === OFFER_TYPE.SELL ? '販売' : '購入' "
                        :class="props.offerType === OFFER_TYPE.SELL ? 'button-sell' : 'button-buy' "
                    />
                </q-td>
            </template>
            <template v-slot:pagination="props" v-if="pagination">
                <q-pagination
                    v-model="pagination.page"
                    :max="Math.ceil(pagination.rowsNumber / pagination.rowsPerPage)"
                    max-pages="5"
                    direction-links
                    outline
                    color="orange"
                    active-design="unelevated"
                    active-color="brown"
                    active-text-color="orange"
                    class="pagination"
                    @update:model-value="handlePageChange"
                />
            </template>
        </q-table>

        <q-table
            v-else
            grid
            :columns="columns"
            :rows="props.offers.data"
            row-key="id"
            v-model:pagination="pagination"
            :rows-per-page-options="[]"
            binary-state-sort
            @request="onRequest"
            wrap-cells
            class="q-mt-md"
        >
            <template #item="props">
                <div class="col-12">
                    <q-card>
                        <q-card-section>
                            <div class="row justify-between">
                                <div class="col-auto">
                                    <span class="label-customer-name">{{
                                        props.row.user.name
                                    }}</span>
                                    <q-badge
                                        rounded
                                        color="green"
                                        class="q-ml-sm"
                                    />
                                </div>
                                <div class="col-auto">
                                    <q-icon name="o_timer" />{{ props.row.time_limit }}分 | xx%
                                </div>
                            </div>

                            <div class="row justify-between">
                                <div class="col-auto">
                                    <div>
                                        <Rate :offer="props.row" />
                                    </div>
                                    <RemainingProduct :offer="props.row" />
                                    <TradingRange :offer="props.row" />

                                    <div class="q-gutter-x-sm">
                                        <PaymentMethodList :list=" props.row.currency?.payment_methods"/>
                                    </div>
                                </div>
                                <div class="col-auto self-end">
                                    <q-btn
                                        :label="props.offerType === OFFER_TYPE.SELL ? '販売' : '購入' "
                                        :class=" props.offerType === OFFER_TYPE.SELL ? 'button-sell' : 'button-buy' "
                                    />
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </template>
            <template v-slot:pagination="props" v-if="pagination">
                <q-pagination
                    v-model="pagination.page"
                    :max="Math.ceil(pagination.rowsNumber / pagination.rowsPerPage)"
                    max-pages="5"
                    direction-links
                    outline
                    color="orange"
                    active-design="unelevated"
                    active-color="brown"
                    active-text-color="orange"
                    @update:model-value="handlePageChange"
                />
            </template>
        </q-table>
    </MenuLayout>
</template>
<style lang="scss">
@import "../../../sass/input.scss";
@import "../../../sass/table.scss";
@import "../../../sass/text.scss";
@import "../../../sass/button.scss";
.q-table__container {
    thead {
        background-color: #021b38;
        th {
            color: #fff;
            &:first-child {
                text-align: left;
            }
            &:last-child {
                text-align: right;
            }
        }
    }

    tbody {
        td {
            .rate {
                font-size: 1.25rem;
            }

            &:first-child {
                text-align: left;
            }

            &:last-child {
                text-align: right;
            }
        }
    }
}
</style>
