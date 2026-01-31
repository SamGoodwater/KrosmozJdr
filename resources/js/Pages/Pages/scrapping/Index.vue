<script setup>
/**
 * Page Scrapping (wrapper)
 *
 * @description
 * La logique de scrapping de la page `/scrapping` est centralisée dans `ScrappingDashboard`.
 * D'autres composants (ex: modal) peuvent réutiliser une logique similaire, mais cette page
 * vise une UX "batch" complète (search → select → simulate/import + analyse des effets).
 */
import { Head } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

import Container from "@/Pages/Atoms/data-display/Container.vue";
import ScrappingDashboard from "@/Pages/Organismes/scrapping/ScrappingDashboard.vue";

const { setPageTitle } = usePageTitle();
onMounted(() => setPageTitle("Gestion du Scrapping"));

// Après une grosse refonte, HMR peut laisser un module incohérent en mémoire.
// On force un reload complet sur update de cette page.
if (import.meta?.hot) {
    import.meta.hot.accept(() => {
        import.meta.hot.invalidate();
    });
}
</script>

<template>
    <Head title="Gestion du Scrapping" />

    <Container class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Gestion du Scrapping</h1>
                <p class="text-primary-200 mt-2">Importez des données depuis DofusDB vers KrosmozJDR</p>
            </div>
        </div>

        <ScrappingDashboard />
    </Container>
</template>
