<template>
    <AppLayout title="Items">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Items
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 overflow-hidden bg-gray-800 px-3 shadow-xl sm:rounded-lg lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-8 p-6">

                    <MarketplaceItem v-for="item in listItems"
                                     @add="addItem"
                                     :item="item"
                                     :key="item.market_hash_name"/>

                    <div v-if="items.length > maxIndex" class="flex flex-col items-center justify-center">
                        <primary-button type="button" @click.prevent="maxIndex += 23">
                            Load more
                        </primary-button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MarketplaceItem from "../../Components/MarketplaceItem.vue";
import {computed, ref} from "vue";
import { router } from '@inertiajs/vue3'
import PrimaryButton from "../../Components/PrimaryButton.vue";

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const maxIndex = ref(23);

const listItems = computed(() => {
    return props.items?.filter((item, index) => index < maxIndex.value);
});

const addItem = async (market_hash_name) => {
    console.log(market_hash_name);
    await axios.post(route('inventory.store'), {market_hash_name});
    router.reload({ only: ['items'] });
};
</script>
