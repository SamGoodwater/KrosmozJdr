<script setup>
/**
 * Campaign Edit Page
 * 
 * @description
 * Page d'édition d'une campagne avec gestion des relations
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Campaign } from '@/Models/Entity/Campaign';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    campaign: {
        type: Object,
        required: true
    },
    availableUsers: {
        type: Array,
        default: () => []
    },
    availableScenarios: {
        type: Array,
        default: () => []
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

const viewMode = ref('large');

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
    state: {
        type: 'select',
        label: 'État',
        required: false,
        showInCompact: true,
        options: getEntityStateOptions(),
    },
    read_level: {
        type: 'select',
        label: 'Lecture (min.)',
        required: false,
        showInCompact: false,
        options: getUserRoleOptions(),
    },
    write_level: {
        type: 'select',
        label: 'Écriture (min.)',
        required: false,
        showInCompact: false,
        options: getUserRoleOptions(),
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Créer une instance de modèle Campaign
const campaign = computed(() => {
    const campaignData = props.campaign || page.props.campaign || {};
    return new Campaign(campaignData);
});

setPageTitle(`Modifier la campagne : ${campaign.value.name || 'Nouvelle campagne'}`);
</script>

<template>
    <Head :title="`Modifier la campagne : ${campaign?.name || 'Nouvelle campagne'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="campaign"
            entity-type="campaign"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des utilisateurs de la campagne -->
        <EntityRelationsManager
            :relations="campaign.users || []"
            :available-items="availableUsers"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="users"
            relation-name="Utilisateurs de la campagne"
            :config="{
                displayFields: ['name', 'email'],
                searchFields: ['name', 'email'],
                itemLabel: 'utilisateur',
                itemLabelPlural: 'utilisateurs'
            }"
        />
        
        <!-- Gestion des scénarios de la campagne -->
        <EntityRelationsManager
            :relations="campaign.scenarios || []"
            :available-items="availableScenarios"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="scenarios"
            relation-name="Scénarios de la campagne"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'scénario',
                itemLabelPlural: 'scénarios'
            }"
        />
        
        <!-- Gestion des objets de la campagne -->
        <EntityRelationsManager
            :relations="campaign.items || []"
            :available-items="availableItems"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="items"
            relation-name="Objets de la campagne"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'objet',
                itemLabelPlural: 'objets'
            }"
        />
        
        <!-- Gestion des consommables de la campagne -->
        <EntityRelationsManager
            :relations="campaign.consumables || []"
            :available-items="availableConsumables"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="consumables"
            relation-name="Consommables de la campagne"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'consommable',
                itemLabelPlural: 'consommables'
            }"
        />
        
        <!-- Gestion des ressources de la campagne -->
        <EntityRelationsManager
            :relations="campaign.resources || []"
            :available-items="availableResources"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="resources"
            relation-name="Ressources de la campagne"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'ressource',
                itemLabelPlural: 'ressources'
            }"
        />
        
        <!-- Gestion des sorts de la campagne -->
        <EntityRelationsManager
            :relations="campaign.spells || []"
            :available-items="availableSpells"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="spells"
            relation-name="Sorts de la campagne"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'sort',
                itemLabelPlural: 'sorts'
            }"
        />
        
        <!-- Gestion des panoplies de la campagne -->
        <EntityRelationsManager
            :relations="campaign.panoplies || []"
            :available-items="availablePanoplies"
            :entity-id="campaign.id"
            entity-type="campaigns"
            relation-type="panoplies"
            relation-name="Panoplies de la campagne"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'panoplie',
                itemLabelPlural: 'panoplies'
            }"
        />
    </Container>
</template>

