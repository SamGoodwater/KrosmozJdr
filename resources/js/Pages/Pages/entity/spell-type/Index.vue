<script setup>
/**
 * SpellType Index Page
 *
 * @description
 * Validation des types de sorts via `state` (playable = visible au catalogue).
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
onMounted(() => setPageTitle("Types de sorts"));
</script>

<template>
    <Head title="Types de sorts" />

    <Container class="space-y-6 pb-8">
        <TypeManagerTable
            title="Types de sorts"
            description="Valider (`playable`) rend le type disponible au catalogue. Archiver le masque. Pas de flag catalogue séparé."
            mode="state"
            field-label="État (bulk)"
            list-url="/api/types/spell-types"
            bulk-url="/api/types/spell-types/bulk"
            delete-url-base="/api/types/spell-types"
            :spell-type-name-cell="true"
        />
    </Container>
</template>
