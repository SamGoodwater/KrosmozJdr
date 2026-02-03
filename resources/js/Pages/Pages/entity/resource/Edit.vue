<script setup>
/**
 * Resource Edit Page
 *
 * @description
 * Page d'édition d'une ressource + gestion minimale des pivots (niveau 1).
 *
 * @props {Object|null} resource - Données de la ressource (nullable en création via page /create)
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Resource } from '@/Models/Entity/Resource';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    resource: {
        type: Object,
        default: null,
    },
    resourceTypes: {
        type: Array,
        default: () => [],
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    availableConsumables: {
        type: Array,
        default: () => [],
    },
    availableCreatures: {
        type: Array,
        default: () => [],
    },
    availableShops: {
        type: Array,
        default: () => [],
    },
    availableScenarios: {
        type: Array,
        default: () => [],
    },
    availableCampaigns: {
        type: Array,
        default: () => [],
    },
    availableResourcesForRecipe: {
        type: Array,
        default: () => [],
    },
});

const viewMode = ref('large');

// Contexte pour les descriptors (options dynamiques, permissions, etc.)
const ctx = computed(() => ({
    resourceTypes: props.resourceTypes,
    capabilities: page.props.auth?.user?.can || {},
    meta: {
        resourceTypes: props.resourceTypes,
        capabilities: page.props.auth?.user?.can || {},
    },
}));

// Générer fieldsConfig depuis les descriptors
const fieldsConfig = computed(() => {
    const descriptors = getResourceFieldDescriptors(ctx.value);
    return createFieldsConfigFromDescriptors(descriptors, ctx.value);
});

const resource = computed(() => {
    const resourceData = props.resource || page.props.resource || {};
    return new Resource(resourceData || {});
});

setPageTitle(`Modifier la ressource : ${resource.value.name || 'Nouvelle ressource'}`);
</script>

<template>
    <Head :title="`Modifier la ressource : ${resource?.name || 'Nouvelle ressource'}`" />

    <Container class="space-y-6">
        <EntityEditForm
            :entity="resource"
            entity-type="resource"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="!!resource?.id"
            @update:view-mode="viewMode = $event"
        />

        <!-- Recette : ingrédients (autres ressources) avec quantités -->
        <EntityRelationsManager
            v-if="resource?.id"
            :relations="resource.recipeIngredients || []"
            :available-items="availableResourcesForRecipe"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="recipe"
            relation-name="Recette (ingrédients pour fabriquer cette ressource)"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'ressource',
                itemLabelPlural: 'ressources'
            }"
        />

        <!-- Relations / pivots niveau 1 -->
        <EntityRelationsManager
            :relations="resource.items || []"
            :available-items="availableItems"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="items"
            relation-name="Objets utilisant cette ressource"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'objet',
                itemLabelPlural: 'objets'
            }"
        />

        <EntityRelationsManager
            :relations="resource.consumables || []"
            :available-items="availableConsumables"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="consumables"
            relation-name="Consommables utilisant cette ressource"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'consommable',
                itemLabelPlural: 'consommables'
            }"
        />

        <EntityRelationsManager
            :relations="resource.creatures || []"
            :available-items="availableCreatures"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="creatures"
            relation-name="Créatures liées (quantité)"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'créature',
                itemLabelPlural: 'créatures'
            }"
        />

        <EntityRelationsManager
            :relations="resource.shops || []"
            :available-items="availableShops"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="shops"
            relation-name="Boutiques associées"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity', 'price', 'comment'],
                itemLabel: 'boutique',
                itemLabelPlural: 'boutiques'
            }"
        />

        <EntityRelationsManager
            :relations="resource.scenarios || []"
            :available-items="availableScenarios"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="scenarios"
            relation-name="Scénarios associés"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'scénario',
                itemLabelPlural: 'scénarios'
            }"
        />

        <EntityRelationsManager
            :relations="resource.campaigns || []"
            :available-items="availableCampaigns"
            :entity-id="resource.id"
            entity-type="resources"
            relation-type="campaigns"
            relation-name="Campagnes associées"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'campagne',
                itemLabelPlural: 'campagnes'
            }"
        />
    </Container>
</template>


