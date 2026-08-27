<script setup>
/**
 * ItemType Index Page
 *
 * @description
 * Registry DofusDB des types d’équipements : decision (scrap) et show_in_catalog (filtre catalogue).
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
onMounted(() => setPageTitle("Types d’équipements"));
</script>

<template>
    <Head title="Types d’équipements" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Types DofusDB (Équipements)"
            description="« Utiliser » inclut le type dans le scrap / la maj de masse. « Catalogue » le pré-coche dans les filtres du listing objets."
            mode="decision"
            field-label="Valider (utiliser / ne pas utiliser)"
            list-url="/api/scrapping/item-types"
            bulk-url="/api/scrapping/item-types/bulk"
            delete-url-base="/api/scrapping/item-types"
            move-category-url-base="/api/scrapping/item-types"
            current-category="equipment"
        />
    </Container>
</template>
