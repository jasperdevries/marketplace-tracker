<template>
    <AppLayout title="Items">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Items
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg px-3">
                    <Overview :columns="columns"
                              :rows="items.data"
                              :currentPage="items.current_page"
                              :pages="items.links"
                              :create-route="route('items.create')"
                              :sortColumn="$page['props']['sort']"
                              :sortDirection="$page['props']['sort_dir']" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Overview from "../../Components/Overview.vue";
const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
});
const columns = {
    market_hash_name: {
        text: 'Name',
        class: '',
        sortable: true,
    },
};
</script>
