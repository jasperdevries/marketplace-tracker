<template>
    <AppLayout title="Items">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Create Item
            </h2>
        </template>
        <div>
            <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
                <div>
                    <FormSection @submitted="saveItem">
                        <template #title>
                            Create Item
                        </template>

                        <template #description>
                            Create a new item to track the price of.
                        </template>

                        <template #form>

                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="market_hash_name" value="Market hash name"/>
                                <TextInput id="market_hash_name"
                                           v-model="form.market_hash_name"
                                           type="text"
                                           class="mt-1 block w-full"/>
                                <InputError :message="form.errors.market_hash_name" class="mt-2"/>
                            </div>

                        </template>

                        <template #actions>
                            <div class="w-full">
                                <label class="float-left">
                                    <Checkbox v-model:checked="form.create_another"/>
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Create another item</span>
                                </label>
                            </div>

                            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                                Saved.
                            </ActionMessage>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Save
                            </PrimaryButton>
                        </template>
                    </FormSection>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import FormSection from "../../Components/FormSection.vue";
import AppLayout from "../../Layouts/AppLayout.vue";
import ActionMessage from "../../Components/ActionMessage.vue";
import PrimaryButton from "../../Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import Checkbox from "../../Components/Checkbox.vue";
import InputLabel from "../../Components/InputLabel.vue";
import TextInput from "../../Components/TextInput.vue";
import InputError from "../../Components/InputError.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    create_another: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const form = useForm({
    market_hash_name: null,
    create_another: props.create_another,
});

const saveItem = () => {
    if (props.item.id) {
        form.put(route('items.update', props.item.id), {
            preserveScroll: true,
            onSuccess: () => {
                if (form.create_another) {
                    form.reset('market_hash_name');
                }
            },
        });
    } else {
        form.post(route('items.store'), {
            preserveScroll: true,
            onSuccess: () => {
                if (form.create_another) {
                    form.reset('market_hash_name');
                }
            },
        });
    }
};
</script>
