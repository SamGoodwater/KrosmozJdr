<script setup>
/**
 * Page Favoris — `/favoris`
 */
import { Head } from "@inertiajs/vue3";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";
import FavoritesPanel from "@/Pages/Organismes/entity/FavoritesPanel.vue";
</script>

<template>
    <Head title="Mes favoris" />
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-4 p-2 sm:p-4">
        <PageHeader title="Mes favoris" />
        <div class="rounded-box border-glass-sm bg-base-100/40 p-4 bd-blur-sm sm:p-6">
            <FavoritesPanel surface="page" :show-open-page-button="false" />
        </div>
    </section>
</template>
