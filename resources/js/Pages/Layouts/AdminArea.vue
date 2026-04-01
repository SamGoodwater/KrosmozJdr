<script setup>
/**
 * Layout administration : bandeau principal du site + barre de navigation horizontale au-dessus du contenu.
 */
import Main from '@/Pages/Layouts/Main.vue';
import AdminSidebarNav from '@/Pages/Admin/_components/AdminSidebarNav.vue';
</script>

<template>
    <Main>
        <div class="mx-auto flex w-full max-w-[1700px] flex-col gap-4">
            <AdminSidebarNav />
            <div class="min-w-0 flex-1 rounded-2xl border border-base-content/5 bg-base-100/20 p-1 min-h-[60vh]">
                <div class="rounded-xl p-3 sm:p-4 lg:p-5">
                    <slot />
                </div>
            </div>
        </div>
    </Main>
</template>
