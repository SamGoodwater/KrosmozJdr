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
});
</script>

<template>
    <SpellSubEffectRow :row="row" />
</template>
