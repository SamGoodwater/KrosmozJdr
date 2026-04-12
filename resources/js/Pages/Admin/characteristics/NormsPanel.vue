<script setup>
/**
 * Panneau d'édition des normes (chartes) pour une caractéristique.
 * Grille 5 puissances × 20 niveaux + conditions de lecture + description.
 */
import { computed, ref, watch } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
    MAX_LEVEL,
    CONDITION_OPERATORS,
    CONDITION_TARGETS,
} from '@/Utils/Characteristic/normsConstants';

const props = defineProps({
    modelValue: { type: Object, required: true },
    characteristicsList: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const grid = computed({
    get: () => props.modelValue.norms_grid || emptyGrid(),
    set: (v) => emit('update:modelValue', { ...props.modelValue, norms_grid: v }),
});

/**
 * Copie locale des conditions, synchronisée avec les props.
 * On travaille sur cette copie pour éviter les re-renders destructeurs
 * quand on tape dans les inputs (le v-for ne recrée pas les éléments).
 */
const localConditions = ref([]);
watch(
    () => props.modelValue.norms_conditions,
    (val) => {
        const incoming = val || [];
        if (JSON.stringify(incoming) !== JSON.stringify(localConditions.value)) {
            localConditions.value = incoming.map((c) => ({ ...c }));
        }
    },
    { immediate: true, deep: true }
);

function emitConditions() {
    emit('update:modelValue', {
        ...props.modelValue,
        norms_conditions: localConditions.value.map((c) => ({ ...c })),
    });
}

const localDescription = ref('');
watch(
    () => props.modelValue.norms_description,
    (val) => { localDescription.value = val || ''; },
    { immediate: true }
);
function emitDescription() {
    emit('update:modelValue', { ...props.modelValue, norms_description: localDescription.value });
}

function emptyGrid() {
    const g = {};
    POWER_LEVELS.forEach((pl) => { g[pl] = Array(MAX_LEVEL).fill(null); });
    return g;
}

function updateCell(powerLevel, levelIndex, value) {
    const newGrid = { ...grid.value };
    newGrid[powerLevel] = [...(newGrid[powerLevel] || Array(MAX_LEVEL).fill(null))];
    newGrid[powerLevel][levelIndex] = value !== '' && value !== null ? Number(value) : null;
    grid.value = newGrid;
}

function clearGrid() {
    if (!confirm('Vider toute la grille ?')) return;
    grid.value = emptyGrid();
}

function fillLinear(powerLevel) {
    const row = grid.value[powerLevel] || [];
    const first = row[0] ?? 0;
    const last = row[MAX_LEVEL - 1] ?? 0;
    if (first === 0 && last === 0) return;
    const newRow = [];
    for (let i = 0; i < MAX_LEVEL; i++) {
        newRow.push(Math.round(first + (last - first) * i / (MAX_LEVEL - 1)));
    }
    const newGrid = { ...grid.value };
    newGrid[powerLevel] = newRow;
    grid.value = newGrid;
}

function addCondition() {
    localConditions.value.push({
        characteristic_key: '', operator: '=', value: 0, target: 'power', modifier: 0, comment: '',
    });
    emitConditions();
}

function removeCondition(index) {
    localConditions.value.splice(index, 1);
    emitConditions();
}

function updateConditionLocal(index, field, value) {
    if (localConditions.value[index]) {
        localConditions.value[index][field] = value;
    }
}

function updateConditionAndEmit(index, field, value) {
    updateConditionLocal(index, field, value);
    emitConditions();
}

const levels = Array.from({ length: MAX_LEVEL }, (_, i) => i + 1);

const hasGrid = computed(() => {
    if (!props.modelValue.norms_grid) return false;
    return POWER_LEVELS.some((pl) =>
        (props.modelValue.norms_grid[pl] || []).some((v) => v !== null && v !== '')
    );
});

/** Options formatées pour le SelectSearchField des caractéristiques */
const characteristicsOptions = computed(() =>
    (props.characteristicsList || []).map((c) => ({
        value: c.id || c.key,
        label: c.name || c.id || c.key,
    }))
);
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-title">Normes (Charte)</h4>
            <Btn v-if="hasGrid" size="xs" color="error" variant="ghost" @click="clearGrid">
                Vider la grille
            </Btn>
        </div>

        <!-- Description -->
        <div>
            <label class="label text-xs">Description</label>
            <textarea
                v-model="localDescription"
                @change="emitDescription"
                class="textarea textarea-bordered textarea-sm w-full"
                rows="2"
                placeholder="Description de la norme (contexte, usage…)"
            />
        </div>

        <!-- Grille 5×20 -->
        <div class="overflow-x-auto">
            <table class="table table-xs table-pin-rows">
                <thead>
                    <tr>
                        <th class="sticky left-0 z-10 bg-base-200 min-w-[100px]">Puissance</th>
                        <th
                            v-for="lvl in levels"
                            :key="lvl"
                            class="text-center min-w-[48px] text-xs"
                        >{{ lvl }}</th>
                        <th class="min-w-[60px]"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="pl in POWER_LEVELS" :key="pl">
                        <td class="sticky left-0 z-10 bg-base-200 font-medium text-xs whitespace-nowrap">
                            <span
                                class="inline-block w-2 h-2 rounded-full mr-1"
                                :style="{ backgroundColor: POWER_COLORS[pl] }"
                            />
                            {{ POWER_LABELS[pl] }}
                        </td>
                        <td v-for="(lvl, idx) in levels" :key="idx" class="p-0">
                            <input
                                type="number"
                                class="input input-xs input-bordered w-full text-center p-0.5"
                                :value="grid[pl]?.[idx] ?? ''"
                                @input="updateCell(pl, idx, $event.target.value)"
                                step="any"
                            />
                        </td>
                        <td>
                            <Btn size="xs" variant="ghost" @click="fillLinear(pl)" title="Interpoler linéairement entre level 1 et 20">
                                <i class="fa-solid fa-chart-line text-xs" />
                            </Btn>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Conditions -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h5 class="text-xs font-semibold text-title">Conditions de lecture</h5>
                <Btn size="xs" color="primary" variant="ghost" @click="addCondition">
                    <i class="fa-solid fa-plus mr-1" /> Condition
                </Btn>
            </div>

            <div
                v-for="(cond, idx) in localConditions"
                :key="'cond-' + idx"
                class="flex flex-wrap items-center gap-2 p-2 rounded-lg bg-base-200/50 border border-base-300"
            >
                <!-- Sélecteur de caractéristique via SelectSearchField -->
                <div class="flex-1 min-w-[140px]">
                    <SelectSearchField
                        size="xs"
                        placeholder="Caractéristique…"
                        :options="characteristicsOptions"
                        :model-value="cond.characteristic_key"
                        @update:model-value="updateConditionAndEmit(idx, 'characteristic_key', $event)"
                    />
                </div>

                <select
                    class="select select-xs select-bordered w-16"
                    :value="cond.operator"
                    @change="updateConditionAndEmit(idx, 'operator', $event.target.value)"
                >
                    <option v-for="op in CONDITION_OPERATORS" :key="op" :value="op">{{ op }}</option>
                </select>

                <input
                    type="number"
                    class="input input-xs input-bordered w-20"
                    v-model.number="cond.value"
                    @change="emitConditions"
                    placeholder="Valeur"
                />

                <span class="text-xs text-content/60">→</span>

                <select
                    class="select select-xs select-bordered w-32"
                    :value="cond.target"
                    @change="updateConditionAndEmit(idx, 'target', $event.target.value)"
                >
                    <option v-for="t in CONDITION_TARGETS" :key="t.value" :value="t.value">{{ t.label }}</option>
                </select>

                <input
                    type="number"
                    class="input input-xs input-bordered w-16"
                    v-model.number="cond.modifier"
                    @change="emitConditions"
                    placeholder="±N"
                />

                <input
                    type="text"
                    class="input input-xs input-bordered flex-1 min-w-[120px]"
                    v-model="cond.comment"
                    @change="emitConditions"
                    placeholder="Commentaire…"
                />

                <Btn size="xs" color="error" variant="ghost" @click="removeCondition(idx)">
                    <i class="fa-solid fa-trash text-xs" />
                </Btn>
            </div>

            <p v-if="!localConditions.length" class="text-xs text-content/50 italic">
                Aucune condition. Cliquez sur « + Condition » pour ajouter une règle de lecture.
            </p>
        </div>
    </div>
</template>
