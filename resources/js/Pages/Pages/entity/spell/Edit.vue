<script setup>
/**
 * Spell Edit Page
 * 
 * @description
 * Page d'édition d'un sort avec deux modes d'affichage (Grand et Compact)
 * 
 * @props {Object} spell - Données du sort à éditer
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Spell } from '@/Models/Entity/Spell';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import SpellEffectsManager from '@/Pages/Organismes/entity/SpellEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    spell: {
        type: Object,
        required: true
    },
    availableBreeds: {
        type: Array,
        default: () => []
    },
    availableSpellTypes: {
        type: Array,
        default: () => []
    },
    availableSpellEffectTypes: {
        type: Array,
        default: () => []
    }
});

// Mode d'affichage par défaut
const viewMode = ref('large');

// Configuration des champs pour les sorts
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
    pa: { 
        type: 'number', 
        label: 'Coût PA', 
        required: false, 
        showInCompact: true 
    },
    po: { 
        type: 'number', 
        label: 'Portée', 
        required: false, 
        showInCompact: false 
    },
    area: { 
        type: 'text', 
        label: 'Zone', 
        required: false, 
        showInCompact: false 
    },
    element: { 
        type: 'select', 
        label: 'Élément', 
        required: false, 
        showInCompact: false,
        options: [
            { value: 'neutral', label: 'Neutre' },
            { value: 'fire', label: 'Feu' },
            { value: 'water', label: 'Eau' },
            { value: 'earth', label: 'Terre' },
            { value: 'air', label: 'Air' }
        ]
    },
    is_magic: { 
        type: 'checkbox', 
        label: 'Magique', 
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

// Créer une instance de modèle Spell
const spell = computed(() => {
    const spellData = props.spell || page.props.spell || {};
    return new Spell(spellData);
});

setPageTitle(`Modifier le sort : ${spell.value.name || 'Nouveau sort'}`);
</script>

<template>
    <Head :title="`Modifier le sort : ${spell?.name || 'Nouveau sort'}`" />
    
    <Container class="space-y-6">
        <EntityEditForm
            :entity="spell"
            entity-type="spell"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des classes du sort (breeds) -->
        <EntityRelationsManager
            :relations="spell.breeds || []"
            :available-items="availableBreeds"
            :entity-id="spell.id"
            entity-type="spells"
            relation-type="breeds"
            relation-name="Classes pouvant utiliser ce sort"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'classe',
                itemLabelPlural: 'classes'
            }"
        />
        
        <!-- Gestion des types de sort -->
        <EntityRelationsManager
            :relations="spell.spellTypes || []"
            :available-items="availableSpellTypes"
            :entity-id="spell.id"
            entity-type="spells"
            relation-type="spellTypes"
            relation-name="Types de sort"
            :config="{
                displayFields: ['name', 'description', 'color'],
                searchFields: ['name', 'description'],
                routeName: 'entities.spells.updateSpellTypes',
                itemLabel: 'type',
                itemLabelPlural: 'types'
            }"
        />

        <!-- Effets du sort (dégâts, soins, états, etc.) -->
        <SpellEffectsManager
            :spell="spell"
            :available-spell-effect-types="availableSpellEffectTypes"
        />
    </Container>
</template>

