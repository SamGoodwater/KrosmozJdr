<script setup>
/**
 * Item Edit Page
 * 
 * @description
 * Page d'édition d'un item avec deux modes d'affichage (Grand et Compact)
 * 
 * @props {Object} item - Données de l'item à éditer
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Item } from '@/Models/Entity/Item';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    availableResources: {
        type: Array,
        default: () => []
    }
});

// Mode d'affichage par défaut
const viewMode = ref('large');

// Configuration des champs pour les items
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
    rarity: { 
        type: 'select', 
        label: 'Rareté', 
        required: false, 
        showInCompact: true,
        options: [
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
        ]
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Créer une instance de modèle Item
const item = computed(() => {
    const itemData = props.item || page.props.item || {};
    return new Item(itemData);
});

setPageTitle(`Modifier l'item : ${item.value.name || 'Nouvel item'}`);
</script>

<template>
    <Head :title="`Modifier l'item : ${item?.name || 'Nouvel item'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="item"
            entity-type="item"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des ressources de l'item (recette de craft avec quantités) -->
        <EntityRelationsManager
            :relations="item.resources || []"
            :available-items="availableResources"
            :entity-id="item.id"
            entity-type="items"
            relation-type="resources"
            relation-name="Ressources nécessaires (recette de craft)"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity'],
                itemLabel: 'ressource',
                itemLabelPlural: 'ressources'
            }"
        />
    </Container>
</template>

