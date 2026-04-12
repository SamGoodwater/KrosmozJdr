<script setup>
/**
 * Sélecteur interactif de conditions de normes.
 * Chaque condition = chip toggleable affichant un résumé lisible.
 */
import { POWER_COLORS } from '@/Utils/Characteristic/normsConstants';

const props = defineProps({
    conditions: { type: Array, required: true },
    activeIndices: { type: Set, default: () => new Set() },
    availableCharacteristics: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['toggle']);

function conditionLabel(cond) {
    const charName = props.availableCharacteristics?.[cond.characteristic_key]?.name
        || cond.characteristic_key;
    const sign = cond.modifier > 0 ? '+' : '';
    const target = cond.target === 'power' ? 'puissance' : 'niveau';
    return `${charName} ${cond.operator} ${cond.value} → ${target} ${sign}${cond.modifier}`;
}

function chipColor(cond) {
    if (cond.target === 'power') {
        return cond.modifier > 0 ? POWER_COLORS.strong : POWER_COLORS.weak;
    }
    return 'rgb(99, 102, 241)';
}
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <button
            v-for="(cond, idx) in conditions"
            :key="idx"
            type="button"
            class="badge badge-lg cursor-pointer select-none transition-all duration-200 text-xs"
            :class="activeIndices.has(idx) ? 'badge-primary text-primary-content' : 'badge-ghost'"
            :style="activeIndices.has(idx) ? { backgroundColor: chipColor(cond), borderColor: chipColor(cond) } : {}"
            :title="cond.comment || conditionLabel(cond)"
            @click="emit('toggle', idx)"
        >
            {{ conditionLabel(cond) }}
        </button>
        <span v-if="!conditions.length" class="text-xs text-base-content/50 italic">
            Aucune condition définie.
        </span>
    </div>
</template>
