<script setup>
/**
 * MonsterRace Index Page
 *
 * @description
 * Gestion/validation des races de monstres via `state`.
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
onMounted(() => setPageTitle("Races de monstres"));
</script>

<template>
    <Head title="Races de monstres" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Races de monstres"
            description="Valider ou archiver des races (champ state)."
            mode="state"
            field-label="État (bulk)"
            list-url="/api/types/monster-races"
            bulk-url="/api/types/monster-races/bulk"
            delete-url-base="/api/types/monster-races"
        />
    </Container>
</template>

