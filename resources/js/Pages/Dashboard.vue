<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {defineProps} from 'vue';

defineProps({
    lastAddedItems: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

        <div class="py-12 text-gray-800 dark:text-gray-200">
            <div class="mx-auto grid max-w-7xl grid-cols-3 gap-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent lg:p-8">
                        <h2 class="font-semibold leading-tight text-gray-800 text-l dark:text-gray-200">
                            Last added items
                        </h2>

                        <table class="table-auto mt-4 border-collapse">
                            <tbody>
                            <tr v-for="item in lastAddedItems">
                                <td class="border border-t-gray-300 py-4 px-2">{{ item.market_hash_name }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
