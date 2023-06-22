<script setup>
import TextInput from "./TextInput.vue";
import {ref} from "vue";
import {ChevronDoubleRightIcon} from "@heroicons/vue/24/solid";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import { Line } from 'vue-chartjs';
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)


const searchValue = ref(null);
const searchResults = ref(null);
const selectedItem = ref(null);
const graphData = ref(null);

const search = async () => {
    graphData.value = null;
    selectedItem.value = null;
    if (searchValue.value.length < 3) {
        searchResults.value = null;
        return;
    }

    const {data} = await axios.get(route('item-graph.search-items', {search: searchValue.value}));

    searchResults.value = data;
};

const getItem = async (item) => {
    selectedItem.value = item;
    const {data} = await axios.get(route('item-graph.data', {item: selectedItem.value.id}));


    graphData.value = data;
    console.log(graphData.value);
};

const reset = () => {
    searchValue.value = null;
    searchResults.value = null;
    selectedItem.value = null;
    graphData.value = null;
};

</script>

<template>
    <div class="w-full">
        <div class="px-4 py-3 grid grid-cols-12">
            <TextInput v-model="searchValue" class="h-10 col-span-10 px-4" placeholder="min. 3 characters" @keydown.enter.prevent="search" />
            <button @click="search" class="col-span-2">Search</button>
        </div>
        <hr>
        <div class="px-4 py-3">
            <div v-if="selectedItem">
                <div class="grid grid-cols-12 mb-6">
                    <h2 class="font-semibold leading-tight text-gray-800 text-l dark:text-gray-200 col-span-11">
                        {{ selectedItem.market_hash_name }}
                    </h2>
                    <button @click="reset">Reset</button>
                </div>


                <Line :data="graphData" v-if="graphData" />


            </div>
            <div v-else-if="searchResults">
                <table class="mt-4 w-full table-auto border-collapse">
                    <tbody>
                    <tr v-for="item in searchResults" @click="getItem(item)" class="border border-t-gray-300 cursor-pointer">
                        <td class="px-2 py-4">{{ item.market_hash_name }}</td>
                        <td>
                            <ChevronDoubleRightIcon class="w-6 h-6"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <p v-else>Search for an item in the field above</p>
        </div>
    </div>
</template>
