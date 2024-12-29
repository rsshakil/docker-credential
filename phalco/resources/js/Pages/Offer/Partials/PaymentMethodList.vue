<script setup>
const props = defineProps({
    list: {
        type: Array,
        default() {
            return [
                {
                    id: 1,
                    name: "DummyPay",
                },
            ];
        },
    },
});
</script>
<template>
    <q-badge
        v-if="list"
        v-for="(paymentMethod) in list"
        :key="paymentMethod.id"
        :label="paymentMethod.name"
        class="q-pa-sm payment-method q-ma-xs"
    />
</template>
