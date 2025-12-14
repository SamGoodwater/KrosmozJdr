<script setup>
/**
 * ResourceType Show Page
 *
 * @description
 * Page simple pour consulter un type de ressource (utile pour les liens).
 */
import { Head, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

import Container from "@/Pages/Atoms/data-display/Container.vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const props = defineProps({
    resourceType: { type: Object, required: true },
});

const { setPageTitle } = usePageTitle();
setPageTitle(`Type: ${props.resourceType?.name ?? ""}`);

const decisionLabel = computed(() => {
    const d = props.resourceType?.decision;
    return d === "allowed" ? "Utilisé" : d === "blocked" ? "Non utilisé" : "En attente";
});
</script>

<template>
    <Head :title="`Type de ressource - ${resourceType?.name || ''}`" />

    <Container class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">{{ resourceType.name }}</h1>
                <p class="text-primary-200 mt-2">
                    <Badge color="primary" size="sm">{{ decisionLabel }}</Badge>
                </p>
            </div>
            <div class="flex gap-2">
                <Btn variant="ghost" @click="router.visit(route('entities.resource-types.index'))">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Retour à la liste
                </Btn>
                <Btn variant="ghost" @click="router.visit(route('scrapping.index'))">
                    <i class="fa-solid fa-screwdriver-wrench mr-2"></i>
                    Scrapping
                </Btn>
            </div>
        </div>

        <Card class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-primary-300 uppercase">DofusDB typeId</div>
                    <div class="text-primary-100 font-mono">{{ resourceType.dofusdb_type_id ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-primary-300 uppercase">Détections</div>
                    <div class="text-primary-100">{{ resourceType.seen_count ?? 0 }}</div>
                </div>
                <div>
                    <div class="text-xs text-primary-300 uppercase">Dernière détection</div>
                    <div class="text-primary-100">
                        {{ resourceType.last_seen_at ? new Date(resourceType.last_seen_at).toLocaleString('fr-FR') : '—' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-primary-300 uppercase">Ressources liées</div>
                    <div class="text-primary-100">{{ resourceType.resources_count ?? 0 }}</div>
                </div>
            </div>
        </Card>
    </Container>
</template>


