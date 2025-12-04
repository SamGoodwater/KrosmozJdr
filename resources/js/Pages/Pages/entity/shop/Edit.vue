<script setup>
/**
 * Shop Edit Page
 * 
 * @description
 * Page d'édition d'une boutique avec gestion des relations (Items, Consumables, Resources avec prix/quantité/commentaire)
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Shop } from '@/Models/Entity/Shop';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    shop: {
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
    location: { 
        type: 'text', 
        label: 'Localisation', 
        required: false, 
        showInCompact: false 
    },
    price: { 
        type: 'number', 
        label: 'Prix', 
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

// Créer une instance de modèle Shop
const shop = computed(() => {
    const shopData = props.shop || page.props.shop || {};
    return new Shop(shopData);
});

setPageTitle(`Modifier la boutique : ${shop.value.name || 'Nouvelle boutique'}`);
</script>

<template>
    <Head :title="`Modifier la boutique : ${shop?.name || 'Nouvelle boutique'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="shop"
            entity-type="shop"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des objets de la boutique (avec prix/quantité/commentaire) -->
        <EntityRelationsManager
            :relations="shop.items || []"
            :available-items="availableItems"
            :entity-id="shop.id"
            entity-type="shops"
            relation-type="items"
            relation-name="Objets vendus dans la boutique"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity', 'price', 'comment'],
                itemLabel: 'objet',
                itemLabelPlural: 'objets'
            }"
        />
        
        <!-- Gestion des consommables de la boutique (avec prix/quantité/commentaire) -->
        <EntityRelationsManager
            :relations="shop.consumables || []"
            :available-items="availableConsumables"
            :entity-id="shop.id"
            entity-type="shops"
            relation-type="consumables"
            relation-name="Consommables vendus dans la boutique"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity', 'price', 'comment'],
                itemLabel: 'consommable',
                itemLabelPlural: 'consommables'
            }"
        />
        
        <!-- Gestion des ressources de la boutique (avec prix/quantité/commentaire) -->
        <EntityRelationsManager
            :relations="shop.resources || []"
            :available-items="availableResources"
            :entity-id="shop.id"
            entity-type="shops"
            relation-type="resources"
            relation-name="Ressources vendues dans la boutique"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                pivotFields: ['quantity', 'price', 'comment'],
                itemLabel: 'ressource',
                itemLabelPlural: 'ressources'
            }"
        />
    </Container>
</template>

