<script setup>
/**
 * Présentation sous-effet « soin » — point d’extension.
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
});
</script>

<template>
    <SpellSubEffectRow :row="row" :layout="layout" :degree-area="degreeArea" />
</template>
