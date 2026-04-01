<script setup>
/**
 * Layout administration : bandeau principal du site + colonne navigation dédiée à l’espace admin.
 */
import Main from '@/Pages/Layouts/Main.vue';
import AdminSidebarNav from '@/Pages/Admin/_components/AdminSidebarNav.vue';
</script>

<template>
    <Main>
        <div class="flex flex-col gap-6 lg:flex-row w-full max-w-[1700px] mx-auto">
            <AdminSidebarNav class="shrink-0 lg:w-56" />
            <div class="flex-1 min-w-0">
                <slot />
            </div>
        </div>
    </Main>
</template>
