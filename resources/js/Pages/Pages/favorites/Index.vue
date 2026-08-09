<script setup>
/**
 * Page Favoris — `/favoris`
 */
import { Head } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import FavoritesPanel from "@/Pages/Organismes/entity/FavoritesPanel.vue";

usePageTitle().setPageTitle("Mes favoris");
</script>

<template>
    <Head title="Mes favoris" />
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-4 p-2 sm:p-4">
        <div class="rounded-box border-glass-sm bg-base-100/40 p-4 bd-blur-sm sm:p-6">
            <FavoritesPanel surface="page" :show-open-page-button="false" />
        </div>
    </section>
</template>
