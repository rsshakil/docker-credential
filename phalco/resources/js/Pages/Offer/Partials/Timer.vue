<template>
    <div>
        <p>商品の送付期限: {{ countdownText }}</p>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    offer: Object,
})

const remainingTime = ref(0);   
const isExpired = ref(false);   
const countdownText = ref('');

const fetchRemainingTime = async () => {
    try {
        const response = await axios.get(route("api.get_offer_remaining_time", [props.offer.id]));
    
        remainingTime.value = response.data.remainingTime + 3;
        isExpired.value = response.data.isExpired;

        if (!isExpired.value && remainingTime.value > 0) {
            startCountdown();
        }
    } catch (error) {
        console.error("タイムアップ後のステータス更新に失敗しました:", error);
    }
};


const formatCountdownText = async () => {  
    if (isExpired.value || remainingTime.value <= 0 || isNaN(remainingTime.value)) {
        countdownText.value = 'タイムアップ';
	    window.location.reload();
    }else{
        const minutes = Math.floor(remainingTime.value / 60);
        const seconds = Math.floor(remainingTime.value % 60); 
        countdownText.value = `${minutes}分 ${seconds}秒`;
    }
};


const startCountdown = () => {
    const interval = setInterval(() => {
        if (remainingTime.value > 0) {
            remainingTime.value--;
        } else {
            isExpired.value = true;
            clearInterval(interval);  
        }
        formatCountdownText();
    }, 1000);
};

onMounted(() => {
    fetchRemainingTime();
});
</script>
