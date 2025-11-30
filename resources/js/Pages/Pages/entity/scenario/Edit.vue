<script setup>
/**
 * Scenario Edit Page
 * 
 * @description
 * Page d'édition d'un scénario avec gestion des relations (Items, Consumables, Resources, Spells, Panoplies)
 * 
 * @props {Object} scenario - Données du scénario à éditer
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    scenario: {
        type: Object,
        required: true
    },
    availableItems: {
        type: Array,
        default: () => []
    },
    availableConsumables: {
        type: Array,
        default: () => []
    },
    availableResources: {
        type: Array,
        default: () => []
    },
    availableSpells: {
        type: Array,
        default: () => []
    },
    availablePanoplies: {
        type: Array,
        default: () => []
    }
});

// Mode d'affichage par défaut
const viewMode = ref('large');

// Configuration des champs pour les scénarios
const fieldsConfig = {
    name: { 
        type: 'text', 
        label: 'Nom', 
        required: true, 
        showInCompact: true 
    },
    description: { 
        type: 'textarea', 
        label: 'Description', 
        required: false, 
        showInCompact: false 
    },
    slug: { 
        type: 'text', 
        label: 'Slug', 
        required: false, 
        showInCompact: false 
    },
    keyword: { 
        type: 'text', 
        label: 'Mot-clé', 
        required: false, 
        showInCompact: false 
    },
    is_public: { 
        type: 'checkbox', 
        label: 'Public', 
        required: false, 
        showInCompact: true 
    },
    usable: { 
        type: 'checkbox', 
        label: 'Utilisable', 
        required: false, 
        showInCompact: true 
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Extraire les données du scénario (gérer la structure Resource)
const scenario = computed(() => {
    const scenarioData = props.scenario || page.props.scenario || {};
    if (scenarioData.data && typeof scenarioData.data === 'object' && scenarioData.data.id) {
        return scenarioData.data;
    }
    return scenarioData;
});

setPageTitle(`Modifier le scénario : ${scenario.value?.name || 'Nouveau scénario'}`);
</script>

<template>
    <Head :title="`Modifier le scénario : ${scenario?.name || 'Nouveau scénario'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="scenario"
            entity-type="scenario"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des objets du scénario -->
        <EntityRelationsManager
            :relations="scenario?.items || []"
            :available-items="availableItems"
            :entity-id="scenario?.id"
            entity-type="scenarios"
            relation-type="items"
            relation-name="Objets du scénario"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'objet',
                itemLabelPlural: 'objets'
            }"
        />
        
        <!-- Gestion des consommables du scénario -->
        <EntityRelationsManager
            :relations="scenario?.consumables || []"
            :available-items="availableConsumables"
            :entity-id="scenario?.id"
            entity-type="scenarios"
            relation-type="consumables"
            relation-name="Consommables du scénario"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'consommable',
                itemLabelPlural: 'consommables'
            }"
        />
        
        <!-- Gestion des ressources du scénario -->
        <EntityRelationsManager
            :relations="scenario?.resources || []"
            :available-items="availableResources"
            :entity-id="scenario?.id"
            entity-type="scenarios"
            relation-type="resources"
            relation-name="Ressources du scénario"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'ressource',
                itemLabelPlural: 'ressources'
            }"
        />
        
        <!-- Gestion des sorts du scénario -->
        <EntityRelationsManager
            :relations="scenario?.spells || []"
            :available-items="availableSpells"
            :entity-id="scenario?.id"
            entity-type="scenarios"
            relation-type="spells"
            relation-name="Sorts du scénario"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'sort',
                itemLabelPlural: 'sorts'
            }"
        />
        
        <!-- Gestion des panoplies du scénario -->
        <EntityRelationsManager
            :relations="scenario?.panoplies || []"
            :available-items="availablePanoplies"
            :entity-id="scenario?.id"
            entity-type="scenarios"
            relation-type="panoplies"
            relation-name="Panoplies du scénario"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'panoplie',
                itemLabelPlural: 'panoplies'
            }"
        />
    </Container>
</template>

