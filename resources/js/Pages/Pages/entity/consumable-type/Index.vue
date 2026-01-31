<script setup>
/**
 * ConsumableType Index Page
 *
 * @description
 * Gestion des types DofusDB détectés pour les consommables (registry: decision).
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
onMounted(() => setPageTitle("Types de consommables"));
</script>

<template>
    <Head title="Types de consommables" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Types DofusDB (Consommables)"
            description="Décider quels typeId DofusDB sont autorisés pour l’import de consommables."
            mode="decision"
            field-label="Valider (utiliser / ne pas utiliser)"
            list-url="/api/scrapping/consumable-types"
            bulk-url="/api/scrapping/consumable-types/bulk"
            delete-url-base="/api/scrapping/consumable-types"
        />
    </Container>
</template>

