<script setup>
import { computed } from 'vue';

const props = defineProps({
    offer: {
        type: Object,
        default: () => ({
            min_amount: 150,
            max_amount: 3000,
        }),
    },
});
const maxAmount = computed(() => props.offer?.display_rate*props.offer?.display_amount);
</script>
<template>
    <div class="row">
        <div class="col-auto stock text-left">取引可能範囲</div>
        <div class="col text-left">
            {{ props.offer?.min_amount ? props.offer.min_amount.toLocaleString() : 0 }} ~
            {{ maxAmount ? maxAmount.toLocaleString() : 0 }} {{ props.offer.currency.code }}
        </div>
    </div>
</template>
