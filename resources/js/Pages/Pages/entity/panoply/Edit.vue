<script setup>
/**
 * Panoply Edit Page
 * 
 * @description
 * Page d'édition d'une panoplie avec deux modes d'affichage (Grand et Compact)
 * 
 * @props {Object} panoply - Données de la panoplie à éditer
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Panoply } from '@/Models/Entity/Panoply';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    panoply: {
        type: Object,
        required: true
    },
    availableItems: {
        type: Array,
        default: () => []
    }
});

// Mode d'affichage par défaut
const viewMode = ref('large');

// Configuration des champs pour les panoplies
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
    bonus: { 
        type: 'textarea', 
        label: 'Bonus', 
        required: false, 
        showInCompact: false 
    },
    usable: { 
        type: 'checkbox', 
        label: 'Utilisable', 
        required: false, 
        showInCompact: true 
    },
    is_visible: { 
        type: 'checkbox', 
        label: 'Visible', 
        required: false, 
        showInCompact: false 
    }
};

// Créer une instance de modèle Panoply
const panoply = computed(() => {
    const panoplyData = props.panoply || page.props.panoply || {};
    return new Panoply(panoplyData);
});

setPageTitle(`Modifier la panoplie : ${panoply.value.name || 'Nouvelle panoplie'}`);
</script>

<template>
    <Head :title="`Modifier la panoplie : ${panoply?.name || 'Nouvelle panoplie'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="panoply"
            entity-type="panoply"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des items de la panoplie -->
        <EntityRelationsManager
            :relations="panoply.items || []"
            :available-items="availableItems"
            :entity-id="panoply.id"
            entity-type="panoplies"
            relation-type="items"
            relation-name="Items de la panoplie"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                itemLabel: 'item',
                itemLabelPlural: 'items'
            }"
        />
    </Container>
</template>

