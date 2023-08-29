<template>

    <div class="p-6 flex items-center flex-col justify-between text-gray-400 border border-gray-600 rounded-2xl" :class="{
        'border-green-600': item.history === 'very_old',
        'border-green-300': item.history === 'old',
        'border-orange-600': item.history === 'medium',
        'border-orange-300': item.history === 'new',
        'border-red-600': item.history === 'very_new',
    }">
        <img :src="'https://community.akamai.steamstatic.com/economy/image/' + item.icon_url" :alt="item.market_hash_name">
        {{ item.market_hash_name }}

        <PrimaryButton v-if="item.history === 'no_history'" type="button" class="self-stretch mt-3" @click.prevent="emit('add', item.market_hash_name)">
            <span class="mx-auto">Add</span>
        </PrimaryButton>
        <div v-else class="text-gray-600">
            Already added
        </div>
    </div>

</template>
<script setup>
import PrimaryButton from "./PrimaryButton.vue";


const emit = defineEmits(['add'])

defineProps({
    item: {
        type: Object,
        required: true,
    },
});
</script>
