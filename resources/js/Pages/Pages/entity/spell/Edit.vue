<script setup>
/**
 * Spell Edit Page
 * 
 * @description
 * Page d'édition d'un sort avec deux modes d'affichage (Grand et Compact)
 * 
 * @props {Object} spell - Données du sort à éditer
 */
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Spell } from '@/Models/Entity/Spell';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EffectUsagesManager from '@/Pages/Organismes/entity/EffectUsagesManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';
import { getElementOptions } from '@/Utils/Entity/Elements';

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
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'spell' },
});

// Mode d'affichage par défaut
// Configuration des champs pour les sorts
// NB : plusieurs champs numériques côté gameplay sont en base des strings → on autorise des formules.
const fieldsConfig = {
    name: {
        type: 'text',
        label: 'Nom',
        required: true,
        showInCompact: true,
    },
    description: {
        type: 'textarea',
        label: 'Description',
        required: false,
        showInCompact: false,
    },
    level: {
        type: 'text',
        label: 'Niveau (formule ou valeur)',
        required: false,
        showInCompact: true,
    },
    pa: {
        type: 'text',
        label: 'Coût PA (formule ou valeur)',
        required: false,
        showInCompact: true,
    },
    po: {
        type: 'text',
        label: 'Portée (formule ou valeur)',
        required: false,
        showInCompact: false,
    },
    area: {
        type: 'text',
        label: 'Zone',
        required: false,
        showInCompact: false,
    },
    cast_per_turn: {
        type: 'text',
        label: 'Lancers par tour (formule ou valeur)',
        required: false,
        showInCompact: false,
    },
    cast_per_target: {
        type: 'text',
        label: 'Lancers par cible (formule ou valeur)',
        required: false,
        showInCompact: false,
    },
    number_between_two_cast: {
        type: 'text',
        label: 'Délai entre deux lancers (tours, formule ou valeur)',
        required: false,
        showInCompact: false,
    },
    po_editable: {
        type: 'checkbox',
        label: 'Portée modifiable',
        required: false,
        showInCompact: false,
    },
    sight_line: {
        type: 'checkbox',
        label: 'Nécessite la ligne de vue',
        required: false,
        showInCompact: false,
    },
    element: {
        type: 'select',
        label: 'Élément',
        required: false,
        showInCompact: false,
        options: getElementOptions(),
    },
    is_magic: {
        type: 'checkbox',
        label: 'Magique',
        required: false,
        showInCompact: true,
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
        showInCompact: false,
    },
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
        <Route route="entities.spells.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <EntityEditForm
            :entity="spell"
            entity-type="spell"
            :fields-config="fieldsConfig"
            :is-updating="true"
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
                itemLabel: 'breed',
                itemLabelPlural: 'breeds'
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

        <!-- Effets (système unifié : effect_usage par tranche de niveau) -->
        <EffectUsagesManager
            :effect-usages="effectUsages"
            :available-effects="availableEffects"
            :entity-type="effectEntityType"
            :entity-id="spell.id"
        />
    </Container>
</template>

