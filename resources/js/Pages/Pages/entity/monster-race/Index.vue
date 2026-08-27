<script setup>
/**
 * MonsterRace Index Page
 *
 * @description
 * Validation des races via `state` (playable = visible au catalogue, sans flag séparé).
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
onMounted(() => setPageTitle("Races de monstres"));
</script>

<template>
    <Head title="Races de monstres" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Races de monstres"
            description="Valider (`playable`) rend la race disponible au catalogue. Archiver la masque. Pas de flag catalogue séparé."
            mode="state"
            field-label="État (bulk)"
            list-url="/api/types/monster-races"
            bulk-url="/api/types/monster-races/bulk"
            delete-url-base="/api/types/monster-races"
        />
    </Container>
</template>
