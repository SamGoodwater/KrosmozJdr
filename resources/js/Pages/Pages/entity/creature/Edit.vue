<script setup>
/**
 * Creature Edit Page
 * 
 * @description
 * Page d'édition d'une créature avec gestion des relations (Items, Resources, Consumables, Spells avec quantités)
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Creature } from '@/Models/Entity/Creature';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    creature: {
        type: Object,
        required: true
    },
    availableItems: {
        type: Array,
        default: () => []
    },
    availableResources: {
        type: Array,
        default: () => []
    },
    availableConsumables: {
        type: Array,
        default: () => []
    },
    availableSpells: {
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
    level: { 
        type: 'number', 
        label: 'Niveau', 
        required: false, 
        showInCompact: true 
    },
    life: { 
        type: 'number', 
        label: 'Vie', 
        required: false, 
        showInCompact: true 
    },
    hostility: { 
        type: 'number', 
        label: 'Hostilité', 
        required: false, 
        showInCompact: true 
    },
    location: { 
        type: 'text', 
        label: 'Localisation', 
        required: false, 
        showInCompact: false 
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Créer une instance de modèle Creature
const creature = computed(() => {
    const creatureData = props.creature || page.props.creature || {};
    return new Creature(creatureData);
});

setPageTitle(`Modifier la créature : ${creature.value.name || 'Nouvelle créature'}`);
</script>

<template>
    <Head :title="`Modifier la créature : ${creature?.name || 'Nouvelle créature'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="creature"
            entity-type="creature"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des objets de la créature (avec quantités) -->
        <EntityRelationsManager
            :relations="creature.items || []"
            :available-items="availableItems"
            :entity-id="creature.id"
            entity-type="creatures"
            relation-type="items"
            relation-name="Objets de la créature"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'objet',
                itemLabelPlural: 'objets'
            }"
        />
        
        <!-- Gestion des ressources de la créature (avec quantités) -->
        <EntityRelationsManager
            :relations="creature.resources || []"
            :available-items="availableResources"
            :entity-id="creature.id"
            entity-type="creatures"
            relation-type="resources"
            relation-name="Ressources de la créature"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'ressource',
                itemLabelPlural: 'ressources'
            }"
        />
        
        <!-- Gestion des consommables de la créature (avec quantités) -->
        <EntityRelationsManager
            :relations="creature.consumables || []"
            :available-items="availableConsumables"
            :entity-id="creature.id"
            entity-type="creatures"
            relation-type="consumables"
            relation-name="Consommables de la créature"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'consommable',
                itemLabelPlural: 'consommables'
            }"
        />
        
        <!-- Gestion des sorts de la créature (sans quantité) -->
        <EntityRelationsManager
            :relations="creature.spells || []"
            :available-items="availableSpells"
            :entity-id="creature.id"
            entity-type="creatures"
            relation-type="spells"
            relation-name="Sorts de la créature"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'sort',
                itemLabelPlural: 'sorts'
            }"
        />
    </Container>
</template>

