<script setup>
import {Head} from "@inertiajs/vue3";
import AdminMenuLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";

const props = defineProps({
    paymentMethod: Object,
});

</script>

<template>
    <Head title="支払方法詳細" />

    <AdminMenuLayout>
        <template  #header>
            支払方法詳細
        </template>
        <div class="row items-center q-gutter-sm">
            <div class="text-white text-center bg-green">
                <q-btn
                    label="支払方法編集"
                    @click="router.visit('admin.payment_method.create')"
                />
            </div>
        </div>
        <q-card class="my-card">
            <q-card-section horizontal>
                <q-card-section class="col-5 flex flex-center">
                    <q-img
                        class="rounded-borders"
                        :src=props.paymentMethod.logo_path
                        style="height: 50px; max-width: 50px"
                    />
                </q-card-section>

                <q-list>
                    <q-item>
                        <q-item-section>
                            <q-item-label>支払方法名称</q-item-label>
                            <q-item-label caption>{{ props.paymentMethod.name }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-item>
                        <q-item-section>
                            <q-item-label>通貨</q-item-label>
                            <q-item-label caption>{{ props.paymentMethod.currency.name }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-item>
                        <q-item-section>
                            <q-item-label>支払方法の項目タイプ</q-item-label>
                            <q-item-label
                                v-for="(item, index) in props.paymentMethod.payment_items"
                                :key="index"
                                caption
                            >
                                {{ item.name }}
                            </q-item-label>
                        </q-item-section>
                    </q-item>
                </q-list>
            </q-card-section>
        </q-card>
    </AdminMenuLayout>
</template>
