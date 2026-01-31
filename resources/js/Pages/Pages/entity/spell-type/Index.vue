<script setup>
/**
 * SpellType Index Page
 *
 * @description
 * Gestion/validation des types de sorts via `state`.
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
onMounted(() => setPageTitle("Types de sorts"));
</script>

<template>
    <Head title="Types de sorts" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Types de sorts"
            description="Valider ou archiver des types de sorts (champ state)."
            mode="state"
            field-label="État (bulk)"
            list-url="/api/types/spell-types"
            bulk-url="/api/types/spell-types/bulk"
            delete-url-base="/api/types/spell-types"
        />
    </Container>
</template>

