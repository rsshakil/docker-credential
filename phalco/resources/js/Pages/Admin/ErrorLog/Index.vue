<script setup>
import {useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {date} from "quasar";

const props = defineProps({
    errorLogs: Object,
    errors: Object,
});

const form = useForm({
    date: date.formatDate(new Date(), 'YYYY/MM/DD'),
});

const search = () => {
    form.get(route('admin.errorlog.index'), {preserveState: true});
};

const columns = [
    {
        name: 'file',
        label: 'ファイル名',
        field: (row) => row.file + ':' + row.line_no,
        align: 'left',
        sortable: false,
    },
    {
        name: 'message',
        label: 'エラーメッセージ',
        field: 'message',
        align: 'left',
    },
    {
        name: 'date',
        label: '発生日',
        field: 'date',
        align: 'right',
        sortable: true,
        headerStyle: 'width: 90px',
    },
    {
        name: 'count',
        label: '回数',
        field: 'count',
        align: 'right',
        sortable: true,
        headerStyle: 'width: 70px',
    },
];
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            エラーログ
        </template>
        <q-table
            dense
            wrap-cells
            separator="cell"
            :rows="props.errorLogs"
            :columns="columns"
            row-key="id"
            :rows-per-page-options="[0]"
        >
            <template v-slot:top>
                <q-space />
                <div class="row q-gutter-x-sm items-start">
                    <q-input
                        dense
                        hide-bottom-space
                        filled
                        v-model="form.date"
                        mask="date"
                        :error="!!props.errors?.date"
                        :error-message="props.errors?.date"
                    >
                        <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" />
                            <q-popup-proxy cover transition-show="scale" transition-hide="scale" ref="datePopup">
                                <q-date
                                    no-unset
                                    today-btn
                                    v-model="form.date"
                                    @update:model-value="$refs.datePopup.hide(); search();"
                                >
                                    <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="閉じる" color="primary"/>
                                    </div>
                                </q-date>
                            </q-popup-proxy>
                        </template>
                    </q-input>
                    <q-btn class="full-height" label="検索" color="primary" @click="search" />
                </div>
            </template>
        </q-table>
    </AuthenticatedLayout>
</template>
