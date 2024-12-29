<script setup>
import {Head} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";

const props = defineProps([
    'foo',
]);
</script>

<template>
    <Head title="ダッシュボード" />

    <MenuLayout>
        <div>
            props: {{ props.foo }}
        </div>

        <div>
            ziggy route: {{ route('dashboard') }}
        </div>
    </MenuLayout>
</template>
