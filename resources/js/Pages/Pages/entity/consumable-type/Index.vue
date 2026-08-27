<script setup>
/**
 * ConsumableType Index Page
 *
 * @description
 * Registry DofusDB des types de consommables : decision (scrap) et show_in_catalog.
 */
import { Head } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import TypeManagerTable from "@/Pages/Organismes/type-management/TypeManagerTable.vue";

defineOptions({ layout: AdminArea });

defineProps({
    can: { type: Object, default: () => ({}) },
});

const { setPageTitle } = usePageTitle();
onMounted(() => setPageTitle("Types de consommables"));
</script>

<template>
    <Head title="Types de consommables" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Types DofusDB (Consommables)"
            description="« Utiliser » inclut le type dans le scrap / la maj de masse. « Catalogue » le pré-coche dans les filtres du listing consommables."
            mode="decision"
            field-label="Valider (utiliser / ne pas utiliser)"
            list-url="/api/scrapping/consumable-types"
            bulk-url="/api/scrapping/consumable-types/bulk"
            delete-url-base="/api/scrapping/consumable-types"
            move-category-url-base="/api/scrapping/consumable-types"
            current-category="consumable"
        />
    </Container>
</template>
