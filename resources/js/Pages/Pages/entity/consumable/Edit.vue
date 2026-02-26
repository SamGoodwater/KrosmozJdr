<script setup>
/**
 * Consumable Edit Page
 *
 * @description
 * Page d'édition d'un consommable avec formulaire principal et section Effets (usages).
 *
 * @props {Object} consumable - Données du consommable à éditer
 * @props {Array} availableConsumableTypes - Types de consommables pour le select
 * @props {Array} effectUsages - Usages d'effets liés
 * @props {Array} availableEffects - Effets disponibles
 * @props {String} effectEntityType - Type entité pour l'API effets ('consumable')
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Consumable } from '@/Models/Entity/Consumable';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EffectUsagesManager from '@/Pages/Organismes/entity/EffectUsagesManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    consumable: {
        type: Object,
        required: true
    },
    availableConsumableTypes: {
        type: Array,
        default: () => []
    },
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'consumable' },
});

const viewMode = ref('large');

const consumableTypeOptions = computed(() =>
    (props.availableConsumableTypes || []).map((t) => ({
        value: t.id,
        label: t.name || t.description || `Type #${t.id}`
    }))
);

const fieldsConfig = computed(() => ({
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
    effect: {
        type: 'textarea',
        label: 'Effet (texte libre)',
        required: false,
        showInCompact: false
    },
    recipe: {
        type: 'textarea',
        label: 'Recette',
        required: false,
        showInCompact: false
    },
    price: {
        type: 'text',
        label: 'Prix',
        required: false,
        showInCompact: false
    },
    rarity: {
        type: 'select',
        label: 'Rareté',
        required: false,
        showInCompact: true,
        options: [
            { value: 0, label: 'Commun' },
            { value: 1, label: 'Peu commun' },
            { value: 2, label: 'Rare' },
            { value: 3, label: 'Épique' },
            { value: 4, label: 'Légendaire' }
        ]
    },
    state: {
        type: 'select',
        label: 'État',
        required: false,
        showInCompact: false,
        options: [
            { value: 'raw', label: 'Brut' },
            { value: 'draft', label: 'Brouillon' },
            { value: 'playable', label: 'Jouable' },
            { value: 'archived', label: 'Archivé' }
        ]
    },
    consumable_type_id: {
        type: 'select',
        label: 'Type de consommable',
        required: false,
        showInCompact: true,
        options: consumableTypeOptions.value
    },
    image: {
        type: 'file',
        label: 'Image',
        required: false,
        showInCompact: false
    }
}));

const consumable = computed(() => {
    const data = props.consumable || page.props.consumable || {};
    return new Consumable(data);
});

setPageTitle(`Modifier le consommable : ${consumable.value.name || 'Sans nom'}`);
</script>

<template>
    <Head :title="`Modifier le consommable : ${consumable?.name || 'Sans nom'}`" />

    <Container class="space-y-6">
        <EntityEditForm
            :entity="consumable"
            entity-type="consumable"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />

        <!-- Gestion des usages d'effets (système unifié) -->
        <EffectUsagesManager
            :effect-usages="effectUsages"
            :available-effects="availableEffects"
            :entity-type="effectEntityType"
            :entity-id="consumable.id"
        />
    </Container>
</template>
