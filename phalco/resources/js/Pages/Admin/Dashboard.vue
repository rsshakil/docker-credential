<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";

const props = defineProps([
    'foo',
]);
</script>

<template>
    <Head title="[管理]ダッシュボード" />

    <AuthenticatedLayout>
        <div>
            管理画面
        </div>
    </AuthenticatedLayout>
</template>
