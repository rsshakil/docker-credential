<script setup>
const props = defineProps({
    offer: {
        type: Object,
        default(rowProps) {
            return {
                display_rate: 150.0,
                currency: {
                    code: "JPY",
                },
            };
        },
    },
});
</script>
<template>
    <span class="rate">{{ props.offer.display_rate }}</span>
    {{ props.offer.currency.code }}
</template>
