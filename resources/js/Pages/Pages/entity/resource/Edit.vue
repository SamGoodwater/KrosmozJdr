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
});

const viewMode = ref('large');

const rarityOptions = [
    { value: 0, label: 'Commun' },
    { value: 1, label: 'Peu commun' },
    { value: 2, label: 'Rare' },
    { value: 3, label: 'Très rare' },
    { value: 4, label: 'Légendaire' },
    { value: 5, label: 'Unique' },
];

const resourceTypeOptions = computed(() => ([
    { value: '', label: '—' },
    ...props.resourceTypes.map(t => ({ value: t.id, label: t.name }))
]));

const fieldsConfig = computed(() => ({
    name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
    description: { type: 'textarea', label: 'Description', required: false, showInCompact: false },
    level: { type: 'text', label: 'Niveau', required: false, showInCompact: true },
    rarity: { type: 'select', label: 'Rareté', required: false, showInCompact: true, options: rarityOptions },
    resource_type_id: { type: 'select', label: 'Type de ressource', required: false, showInCompact: true, options: resourceTypeOptions.value },
    price: { type: 'text', label: 'Prix', required: false, showInCompact: true },
    weight: { type: 'text', label: 'Poids', required: false, showInCompact: true },
    usable: { type: 'checkbox', label: 'Utilisable', required: false, showInCompact: true },
    auto_update: { type: 'checkbox', label: 'Auto-update', required: false, showInCompact: true },
    image: { type: 'text', label: 'Image (URL)', required: false, showInCompact: false },
    dofusdb_id: { type: 'text', label: 'DofusDB ID', required: false, showInCompact: false },
    official_id: { type: 'number', label: 'Official ID', required: false, showInCompact: false },
    dofus_version: { type: 'text', label: 'Version Dofus', required: false, showInCompact: false },
    is_visible: { type: 'text', label: 'Visibilité', required: false, showInCompact: false },
}));

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


