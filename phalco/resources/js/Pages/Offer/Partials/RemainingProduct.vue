<script setup>
const props = defineProps({
    offer: {
        type: Object,
        default() {
            return {
                display_amount: 250.0,
            };
        },
    },
});
</script>
<template>
    <div class="row">
        <div class="col-auto stock text-left">商品残り</div>
        <div class="col-auto text-left">
            {{ props.offer?.amount ? props.offer.display_amount.toLocaleString():0 }}
            {{ props.offer.product.unit }}
        </div>
    </div>
</template>
