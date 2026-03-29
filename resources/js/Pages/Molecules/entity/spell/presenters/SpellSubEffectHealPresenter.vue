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
});
</script>

<template>
    <SpellSubEffectRow :row="row" />
</template>
