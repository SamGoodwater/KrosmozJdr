<script setup>
/**
 * Composant assembleur des normes : table + chart + sélecteur de conditions.
 * Layout responsive : côte à côte (lg) ou empilé (sm).
 */
import { ref, toRef } from 'vue';
import NormsTable from '@/Pages/Molecules/data-display/NormsTable.vue';
import NormsChart from '@/Pages/Molecules/data-display/NormsChart.vue';
import NormsConditionSelector from '@/Pages/Molecules/data-input/NormsConditionSelector.vue';
import { useNormsReader } from '@/Composables/characteristic/useNormsReader';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
} from '@/Utils/Characteristic/normsConstants';

const props = defineProps({
    grid: { type: Object, required: true },
    conditions: { type: Array, default: () => [] },
    description: { type: String, default: '' },
    characteristicName: { type: String, default: '' },
    characteristicColor: { type: String, default: '#6366f1' },
    availableCharacteristics: { type: Object, default: () => ({}) },
});

const gridRef = toRef(props, 'grid');
const conditionsRef = toRef(props, 'conditions');

const {
    selectedLevel,
    activeConditionIndices,
    toggleCondition,
    selectLevel,
    effectivePowerIndex,
    effectivePowerLevel,
    effectiveLevelIndex,
    resolvedValue,
} = useNormsReader(gridRef, conditionsRef);

const helpOpen = ref(false);
</script>

<template>
    <div class="space-y-4">
        <!-- Description -->
        <p v-if="description" class="text-sm text-base-content/70">{{ description }}</p>

        <!-- Aide de lecture (collapse) -->
        <div class="collapse collapse-arrow bg-base-200/40 border border-base-300 rounded-lg">
            <input v-model="helpOpen" type="checkbox" />
            <div class="collapse-title text-sm font-medium">
                <i class="fa-solid fa-circle-question mr-1 text-info" /> Aide de lecture
            </div>
            <div class="collapse-content text-sm space-y-2">
                <p>Le tableau indique les valeurs recommandées par <strong>niveau</strong> (colonnes) et par <strong>puissance</strong> (lignes).</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Cliquez sur un <strong>numéro de niveau</strong> (en-tête) pour mettre en évidence la colonne correspondante.</li>
                    <li>La ligne « <strong>Neutre</strong> » est sélectionnée par défaut. Activez des <strong>conditions</strong> (chips ci-dessous) pour décaler la lecture vers une ligne plus forte ou plus faible.</li>
                    <li>Certaines conditions décalent le <strong>niveau</strong> (colonne) au lieu de la puissance.</li>
                    <li>La cellule à l'intersection de la ligne et de la colonne actives est la <strong>valeur de référence</strong>.</li>
                </ul>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span
                        v-for="pl in POWER_LEVELS"
                        :key="pl"
                        class="inline-flex items-center gap-1 text-xs"
                    >
                        <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: POWER_COLORS[pl] }" />
                        {{ POWER_LABELS[pl] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Conditions -->
        <div v-if="conditions.length" class="space-y-1">
            <h5 class="text-xs font-semibold text-base-content/60 uppercase tracking-wide">Conditions</h5>
            <NormsConditionSelector
                :conditions="conditions"
                :active-indices="activeConditionIndices"
                :available-characteristics="availableCharacteristics"
                @toggle="toggleCondition"
            />
        </div>

        <!-- Valeur résolue -->
        <div v-if="selectedLevel !== null" class="flex items-center gap-3 text-sm">
            <span class="text-base-content/60">
                Niveau <strong :style="{ color: characteristicColor }">{{ selectedLevel }}</strong>
                <template v-if="effectiveLevelIndex !== null && effectiveLevelIndex !== selectedLevel - 1">
                    → lecture au niveau <strong>{{ effectiveLevelIndex + 1 }}</strong>
                </template>
                ·
                Puissance <strong :style="{ color: POWER_COLORS[effectivePowerLevel] }">{{ POWER_LABELS[effectivePowerLevel] }}</strong>
            </span>
            <span v-if="resolvedValue !== null" class="badge badge-lg font-bold" :style="{ backgroundColor: characteristicColor, color: 'white' }">
                {{ resolvedValue }}
            </span>
        </div>

        <!-- Table + Chart responsive -->
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 min-w-0">
                <NormsTable
                    :grid="grid"
                    :effective-power-index="effectivePowerIndex"
                    :selected-level="selectedLevel"
                    :effective-level-index="effectiveLevelIndex"
                    @select-level="selectLevel"
                />
            </div>
            <div class="lg:w-[45%] lg:max-w-md shrink-0">
                <NormsChart
                    :grid="grid"
                    :effective-power-index="effectivePowerIndex"
                    :selected-level="selectedLevel"
                    :effective-level-index="effectiveLevelIndex"
                />
            </div>
        </div>
    </div>
</template>
