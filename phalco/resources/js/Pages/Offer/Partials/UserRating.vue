<script setup>
const props = defineProps({
    rating: {
        type: [Number, String],
        default: 0,
    },
});
</script>
<template>{{ rating }}%</template>
