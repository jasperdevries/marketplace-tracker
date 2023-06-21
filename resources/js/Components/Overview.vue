<template>
    <div :class="typeof container_class !== 'undefined' ? container_class : 'py-3'" class="text-white">

        <div class="mb-6 grid w-full grid-cols-2 px-4 sm:px-0">
            <div class="mb-5 text-2xl font-bold"
                 :class="{
                    'col-span-2': typeof search !== 'undefined'
                 }"
                 v-if="title && title.length > 0"
                 v-html="title"></div>

            <div v-if="typeof search !== 'undefined'">
                <TextInput type="text"
                       v-model.lazy="search"
                       class="mt-1 w-full rounded-md shadow-sm focus:ring focus:ring-gray-200 focus:ring-opacity-50 lg:w-1/2"
                       placeholder="Zoeken..."
                       ref="searchField"/>
            </div>
            <div>
                <Link class="float-right mt-2 ml-4 rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition bg-transparant border-secondary text-secondary hover:border-secondary-600 hover:text-secondary-600 focus:outline-none disabled:opacity-25"
                              v-if="createRoute"
                              :href="createRoute">
                    {{ createButtonLabel ?? 'Create new' }}
                </Link>
            </div>
        </div>
        <div class="mb-6 w-full px-4 sm:px-0">
            <span v-for="(button, index) in extraButtons">
                <a v-if="button.type === 'download'"
                   :href="button.route"
                   class="whitespace-nowrap border border-transparent px-4 py-2 text-xs font-semibold normal-case tracking-normal text-white transition focus:outline-none disabled:opacity-25 sm:uppercase sm:tracking-widest"
                   :class="getButtonClasses(button, index)">
                   {{ button.label }}
                </a>
                <Link v-else-if="button.type === 'post'" :href="button.route"
                              method="post"
                              :data="{ rows: selectedItems }"
                              as="button"
                              type="button"
                              class="whitespace-nowrap border border-transparent px-4 py-2 text-xs font-semibold normal-case tracking-normal text-white transition focus:outline-none disabled:opacity-25 sm:uppercase sm:tracking-widest"
                              :class="getButtonClasses(button, index)">
                    {{ button.label }}
                </Link>
                <Link v-else
                              :href="button.route"
                              class="whitespace-nowrap border border-transparent px-4 py-2 text-xs font-semibold normal-case tracking-normal text-white transition focus:outline-none disabled:opacity-25 sm:uppercase sm:tracking-widest"
                              :class="getButtonClasses(button, index)">
                    {{ button.label }}
                </Link>
            </span>
        </div>

        <div class="w-full overflow-hidden shadow-xl sm:rounded-lg">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow sm:rounded-lg">
                            <table class="min-w-full divide-y divide-slate-600" ref="table">
                                <thead class="bg-slate-500">
                                <tr>
                                    <th v-if="typeof selectable !== 'undefined' && selectable"></th>
                                    <th v-for="(col, index) in this.columns"
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        <div :class="col.type === 'url' || col.type === 'bool' ? 'flex justify-center' : ''">
                                            <span v-if="col.sortable" class="float-left mr-3 cursor-pointer">
                                                <span v-if="index === this.sortColumn">
                                                    <BarsArrowDownIcon v-if="this.sortDirection === 'desc'" @click="setSort(index, 'asc')" class="h-4"></BarsArrowDownIcon>
                                                    <BarsArrowUpIcon v-if="this.sortDirection === 'asc'" @click="setSort(index, 'desc')" class="h-4"></BarsArrowUpIcon>
                                                </span>
                                                <Bars4Icon v-else class="h-4" @click="setSort(index, 'asc')"></Bars4Icon>
                                            </span>
                                            {{ col.text }}
                                        </div>
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-slate-700 divide-y divide-slate-600">
                                <tr v-for="row in rows" v-if="rows.length > 0">
                                    <th v-if="typeof selectable !== 'undefined' && selectable">
                                        <Checkbox id="selected_items"
                                                  class="mx-2"
                                                  :value="row.guid"
                                                  v-model:checked="selectedItems"></Checkbox>
                                    </th>
                                    <td class="whitespace-nowrap px-6 py-4" v-for="(col, index) in this.columns" :class="col.class">
                                        <img v-if="col.type === 'img'" :src="row[index]" :alt="col.text" class="h-10 w-10 rounded-full">
                                        <a v-else-if="col.type === 'url' && row[index]" :href="row[index]" target="_blank">
                                            <div v-if="col.icon" class="flex justify-center">
                                                <component :is="col.icon" class="h-5"></component>
                                            </div>
                                            <span v-else>{{ row[index] }}</span>
                                        </a>
                                        <div v-else-if="col.type === 'bool'" class="flex justify-center">
                                            <CheckIcon v-if="col.value(row)" class="h-6 text-green-600"></CheckIcon>
                                            <XMarkIcon v-else-if="col.show_false?.(row)" class="h-6 text-red-600"></XMarkIcon>
                                        </div>
                                        <span v-else-if="col.type === 'date'">
                                                {{ formatDate(row[index]) }}
                                            </span>
                                        <span v-else-if="col.type === 'datetime'">
                                                {{ formatDateTime(row[index]) }}
                                            </span>
                                        <span v-else-if="col.type === 'time'">
                                                {{ formatTime(row[index]) }}
                                            </span>
                                        <span v-else-if="col.type === 'function'">
                                                {{ col.value(row) }}
                                            </span>
                                        <span v-else>{{ row[index] }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <span v-for="action in actions">
                                                <span v-if="(action.show === undefined || !action.show(row))">
                                                    <button v-if="action.method === 'delete'"
                                                            :class="action.class"
                                                            @click="deleteRow(route(action.route, { [action['route_param']] : row[action['route_field']] }), row.deletable_text)"
                                                            class="ml-2 inline-flex items-center rounded-md border border-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-white transition focus:outline-none disabled:opacity-25">
                                                        <component v-if="action.type && action.type === 'component'" :is="action.component" class="h-4"></component>
                                                        <span v-else>{{ action.text }}</span>
                                                    </button>
                                                    <Link as="button"
                                                                  v-else
                                                                  :href="route(action.route, { [action['route_param']] : row[action['route_field']] })"
                                                                  :class="action.class"
                                                                  :method="action.method"
                                                                  class="ml-2 inline-flex items-center rounded-md border px-3 py-2 text-xs font-semibold uppercase tracking-widest text-white transition focus:outline-none disabled:opacity-25">
                                                        <component v-if="action.type && action.type === 'component'" :is="action.component" class="h-4"></component>
                                                        <span v-else>{{ action.text }}</span>
                                                    </Link>
                                                </span>
                                            </span>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td :colspan="Object.keys(columns).length + 1" class="py-2 text-center">
                                        No rows found
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="typeof pages !== 'undefined' && pages.length > 3">
        <div class="flex flex-wrap justify-center pb-10">
            <template v-for="link in pages">
                <div v-if="link.url === null"
                     class="mr-1 mb-1 rounded border px-4 py-3 text-sm"
                     :class="{ 'ml-auto': link.label === 'Next' }" v-html="link.label">
                </div>
                <Link :data="{search: this.search}"
                              v-else
                              class="mr-1 mb-1 rounded border px-4 py-3 text-sm hover:border-secondary-500 hover:bg-slate-900 focus:border-secondary-500"
                              :replace="true"
                              :class="{ 'bg-slate-700  border-secondary-500': link.active, 'ml-auto': link.label === 'Next' }"
                              :href="link.url"
                              v-html="link.label">
                </Link>
            </template>
        </div>
    </div>
</template>

<script>
import TextInput from '@/Components/TextInput.vue';
import {
    BarsArrowDownIcon,
    BarsArrowUpIcon,
    Bars4Icon,
    CheckIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';
import Checkbox from "./Checkbox.vue";
import Swal from "sweetalert2";
import {Link} from "@inertiajs/vue3";

export default {
    name: 'Overview',
    components: {
        Checkbox,
        TextInput,
        BarsArrowDownIcon,
        BarsArrowUpIcon,
        Bars4Icon,
        CheckIcon,
        XMarkIcon,
        Link,
    },
    props: [
        'columns',
        'rows',
        'actions',
        'createRoute',
        'createButtonLabel',
        'pages',
        'currentPage',
        'sortColumn',
        'sortDirection',
        'title',
        'extraButtons',
        'container_class',
        'selectable',
    ],

    data: () => ({
        searchTimer: null,
        selectedItems: [],
        search: null,
    }),

    watch: {
        search: 'searchRows'
    },

    methods: {
        getButtonClasses(button, index) {
            let classes = 'bg-slate-500 hover:bg-slate-600';
            if (button.classes) {
                classes = button.classes;
            }

            if (index === 0) {
                classes += ' rounded-l-md';
            }

            if (index + 1 === this.extraButtons.length) {
                classes += ' rounded-r-md';
            }

            return classes;
        },

        async searchRows() {
            await this.getRows(this.sortColumn, this.sortDirection);
        },

        setSort(column, direction) {
            console.debug('Set sort: ', [column, direction]);
            this.getRows(column, direction);
        },

        async getRows(sort_column, sort_direction) {
            await this.$inertia.get(
                this.route(this.route().current(), this.route().params),
                {
                    search: this.search,
                    sort: sort_column,
                    sort_dir: sort_direction,
                },
                {preserveState: true}
            );
        },

        async deleteRow(route, deletable_text) {
            const promise = await Swal.fire({
                title: 'Are you sure?',
                text: deletable_text ?? 'Are you sure you want to delete this?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            });

            if (promise.isConfirmed) {
                this.$inertia.delete(route);
            }
        },
    },
};
</script>
