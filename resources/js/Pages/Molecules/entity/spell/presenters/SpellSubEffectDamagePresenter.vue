<script setup>
/**
 * Présentation sous-effet « dégâts » — point d’extension (couche / mise en forme dédiée).
 *
 * @props {Object} row - Ligne pivot sérialisée
 */
import SpellSubEffectRow from '@/Pages/Molecules/entity/spell/SpellSubEffectRow.vue';

defineProps({
    row: {
        type: Object,
        required: true,
    },
    layout: {
        type: String,
        default: "large",
    },
    degreeArea: {
        type: [String, null],
        default: null,
    },
    hideTriggerTurn: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <SpellSubEffectRow
        :row="row"
        :layout="layout"
        :degree-area="degreeArea"
        :hide-trigger-turn="hideTriggerTurn"
    />
</template>
