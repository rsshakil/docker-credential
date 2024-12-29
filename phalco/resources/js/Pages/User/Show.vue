<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";
import AggregationComponent from "./AggregationComponent.vue";
import { ref } from "vue";
import { OFFER_TYPE } from "@/Config/OfferTyoe";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    offers: Object,
})

const columns = [
    {
        name: 'name',
        label: '名前',
        field: (row) => row.user.name,
        align: 'left',
        sortable: false,
    },
    {
        name: 'rate',
        label: '価格',
        align: 'left',
        sortable: false,
    },
    {
        name: 'amount',
        label: '商品残り|取引可能範囲',
        align: 'left',
        sortable: false,
    },
    {
        name: 'payment',
        label: '支払方法',
        align: 'left',
        sortable: false,
    },
    {
        name: 'action',
        label: '',
        align: 'left',
        sortable: false,
    },
]

const isLanguageEdit = ref(false);

const form = useForm({
    language: props.user.language,
})

const onLanguageEdit = () => {
    const url= route('profile.update')

    form.submit('put', url, {
        onSuccess: () => {
            $q.notify({
                message: '変更しました',
            })
            isLanguageEdit.value = false
        },
        onError: (error) => {
            $q.notify({
                type: 'negative',
                message: Object.values(error).join(', '),
                html: true,
            })
        }
    })
}
</script>
<template>
    <Head title="アカウント情報" />
    <MenuLayout>
        <AggregationComponent
            :user="props.user"
            :sameUser="false"
        />

        <q-table
            :columns="columns"
            :rows="props.offers"
            row-key="id"
            class="q-mt-md"
        >
            <template #body-cell-name="props">
                <q-td :props="props">
                    <div>
                        <span>{{ props.value }}</span>
                        <q-badge rounded color="green" class="q-ml-sm" />
                    </div>
                    <div>
                        <q-icon name="o_timer" />xx分|xx%
                    </div>
                </q-td>
            </template>
            <template #body-cell-rate="props">
                <q-td :props="props">
                    <span class="rate">{{ props.row.rate }}</span> {{ props.row.currency.code }}
                </q-td>
            </template>
            <template #body-cell-amount="props">
                <q-td :props="props">
                    <div class="row">
                        <div class="col-auto amount">商品残り</div>
                        <div class="col-auto">{{ props.row.display_amount.toLocaleString() }} {{ props.row.product.unit }}</div>
                    </div>
                    <div class="row">
                        <div class="col-auto amount">取引可能範囲</div>
                        <div class="col">{{ props.row.min_amount.toLocaleString() }} ~ {{ props.row.max_amount?.toLocaleString() }} {{ props.row.currency.code }}</div>
                    </div>
                </q-td>
            </template>
            <template #body-cell-payment="props">
                <q-td :props="props" class="q-gutter-x-sm">
                    <q-badge
                        label="銀行振込"
                        color="grey-4"
                        text-color="black"
                        class="q-pa-sm"
                    />
                    <q-badge
                        label="LinePay"
                        color="grey-4"
                        text-color="black"
                        class="q-pa-sm"
                    />
                    <q-badge
                        label="PayPay"
                        color="grey-4"
                        text-color="black"
                        class="q-pa-sm"
                    />
                </q-td>
            </template>
            <template #body-cell-action="prps">
                <q-td :props="prps">
                    <q-btn
                        :label="prps.offer_type === OFFER_TYPE.SELL ? '購入' : '販売'"
                        :color="prps.offer_type === OFFER_TYPE.SELL ? 'green' : 'red'"
                    />
                </q-td>
            </template>
        </q-table>
    </MenuLayout>
</template>

