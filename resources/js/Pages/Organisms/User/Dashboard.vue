<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { defineProps } from "vue";

const page = usePage();
const user = ref(page.props.user);
const verifiedEmail = ref(page.props.verifiedEmail);
const panoplies = ref(page.props.panoplies);
const resources = ref(page.props.resources);

const props = defineProps({
    debug: String,
});
</script>

<template>
    <Head title="Mon compte" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Mon compte
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                    <div class="p-6 text-red-500">{{ debug }}</div>
                    <!-- Message de débogage -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
