<script setup>
/**
 * ItemType Index Page
 *
 * @description
 * Gestion des types DofusDB détectés pour les équipements (registry: decision).
 */
import { Head } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import TypeManagerTable from "@/Pages/Organismes/type-management/TypeManagerTable.vue";

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
            description="Décider quels typeId DofusDB sont autorisés pour l’import d’équipements."
            mode="decision"
            field-label="Valider (utiliser / ne pas utiliser)"
            list-url="/api/scrapping/item-types"
            bulk-url="/api/scrapping/item-types/bulk"
            delete-url-base="/api/scrapping/item-types"
        />
    </Container>
</template>

