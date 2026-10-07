<script setup>
/**
 * Contexte déplacement (cases, type, téléport).
 */
import { computed } from 'vue';
import { METERS_PER_CASE, previewMetersFromCellsFormula } from '@/Utils/Entity/displacementFormat.js';

const props = defineProps({
    cellsFormula: { type: String, default: '' },
    movementKind: { type: String, default: 'movement' },
    teleport: { type: Boolean, default: false },
});

const emit = defineEmits(['update:cellsFormula', 'update:movementKind', 'update:teleport', 'dirty']);

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
</script>

<template>
    <div class="space-y-2 max-w-xl">
        <div class="space-y-1">
            <label class="text-xs font-medium text-base-content/80">Nombre de cases (formule)</label>
            <input
                type="text"
                inputmode="decimal"
                class="input input-bordered input-sm w-full"
                :value="cellsFormula"
                placeholder="ex: 3, 0,33, [level]…"
                @input="onCells($event.target.value)"
            />
            <p class="text-xs text-base-content/70">
                Distance en cases (1 case = {{ METERS_PER_CASE }} m).
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
            <span class="text-xs">Téléportation</span>
        </label>
    </div>
</template>
