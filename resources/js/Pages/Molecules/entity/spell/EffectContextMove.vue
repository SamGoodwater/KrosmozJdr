<script setup>
/**
 * Contexte déplacement (cases, type, téléport, option « jusqu’à »).
 */
import { computed } from 'vue';
import FormulaExpressionInput from '@/Pages/Molecules/data-input/FormulaExpressionInput.vue';
import { METERS_PER_CASE, previewMetersFromCellsFormula } from '@/Utils/Entity/displacementFormat.js';

const props = defineProps({
    cellsFormula: { type: String, default: '' },
    movementKind: { type: String, default: 'movement' },
    teleport: { type: Boolean, default: false },
    valueUpTo: { type: Boolean, default: false },
    /** Suggestions formules (optionnel). */
    suggestions: { type: Array, default: () => [] },
});

const emit = defineEmits([
    'update:cellsFormula',
    'update:movementKind',
    'update:teleport',
    'update:valueUpTo',
    'dirty',
]);

const MOVEMENT_KIND_OPTIONS = Object.freeze([
    { value: 'movement', label: 'Déplacement' },
    { value: 'teleport', label: 'Téléportation' },
    { value: 'jump', label: 'Saut' },
    { value: 'pull', label: 'Attirance' },
    { value: 'push', label: 'Repousse' },
]);

const metersPreview = computed(() => previewMetersFromCellsFormula(props.cellsFormula));

function onCells(value) {
    emit('update:cellsFormula', value);
    emit('dirty');
}

function onKind(value) {
    emit('update:movementKind', value);
    emit('update:teleport', value === 'teleport');
    emit('dirty');
}

function onTeleport(checked) {
    emit('update:teleport', checked);
    emit('update:movementKind', checked ? 'teleport' : 'movement');
    emit('dirty');
}

function onUpTo(checked) {
    emit('update:valueUpTo', checked);
    emit('dirty');
}
</script>

<template>
    <div class="space-y-2 max-w-xl">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <label class="text-xs font-medium text-base-content/80">Nombre de cases (formule)</label>
                <label class="flex items-center gap-1.5 cursor-pointer text-xs text-base-content/75">
                    <input
                        type="checkbox"
                        class="checkbox checkbox-xs"
                        :checked="valueUpTo"
                        @change="onUpTo($event.target.checked)"
                    />
                    <span title="Le lanceur choisit un nombre de cases ≤ cette formule">
                        Jusqu’à
                    </span>
                </label>
            </div>
            <FormulaExpressionInput
                :model-value="cellsFormula"
                :suggestions="suggestions"
                :min-query-length="0"
                :open-on-focus="true"
                placeholder="ex: 3, [strong], min(3+[agi], 6)"
                @update:model-value="onCells"
            />
            <p class="text-xs text-base-content/70">
                Distance en cases (1 case = {{ METERS_PER_CASE }} m).
                <template v-if="valueUpTo">
                    Coché « Jusqu’à » : le lanceur choisit un nombre ≤ cette formule.
                </template>
            </p>
            <p v-if="metersPreview" class="text-xs font-medium tabular-nums text-base-content/80">
                {{ metersPreview }}
            </p>
        </div>
        <div class="space-y-1">
            <label class="text-xs font-medium text-base-content/80">Type de mouvement</label>
            <select
                class="select select-bordered select-sm w-full"
                :value="movementKind"
                @change="onKind($event.target.value)"
            >
                <option v-for="opt in MOVEMENT_KIND_OPTIONS" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input
                type="checkbox"
                class="checkbox checkbox-sm"
                :checked="teleport"
                @change="onTeleport($event.target.checked)"
            />
            <span class="text-xs">Téléportation (ignore les obstacles)</span>
        </label>
    </div>
</template>
