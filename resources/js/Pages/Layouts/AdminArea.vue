<script setup>
/**
 * Layout administration : bandeau principal du site + barre de navigation horizontale au-dessus du contenu.
 */
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import Main from '@/Pages/Layouts/Main.vue';
import AdminSidebarNav from '@/Pages/Admin/_components/AdminSidebarNav.vue';
import ContentManagementNav from '@/Pages/Admin/_components/ContentManagementNav.vue';

const page = usePage();

const isContentArea = computed(() => {
    const p = page.url.split("?")[0];
    return (
        p.startsWith("/admin/content")
        || p.startsWith("/admin/effects")
        || p.startsWith("/admin/sub-effects")
        || p.startsWith("/admin/languages")
        || p.startsWith("/admin/characteristics")
    );
});

/** Force le remontage de la page Inertia quand l’URL ou le composant change (layout persistant). */
const pageRenderKey = computed(() => `${page.component ?? ""}:${page.url ?? ""}`);
</script>

<template>
    <Main>
        <div class="mx-auto flex w-full max-w-[1700px] flex-col gap-4">
            <ContentManagementNav v-if="isContentArea" />
            <AdminSidebarNav v-else />
            <div class="min-w-0 flex-1 rounded-2xl border border-base-content/5 bg-base-100/20 p-1 min-h-[60vh]">
                <div class="rounded-xl p-3 sm:p-4 lg:p-5">
                    <div :key="pageRenderKey">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </Main>
</template>
