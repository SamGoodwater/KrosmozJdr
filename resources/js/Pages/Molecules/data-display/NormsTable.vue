<script setup>
/**
 * Tableau interactif de normes : 5 lignes puissance × 20 colonnes niveaux.
 * La ligne active (puissance effective) et la colonne active (level sélectionné) sont mises en avant.
 */
import { computed } from 'vue';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
    POWER_BG_COLORS,
    MAX_LEVEL,
    NEUTRAL_INDEX,
} from '@/Utils/Characteristic/normsConstants';
import { levelColor } from '@/Utils/Characteristic/levelColorScale';

const props = defineProps({
    grid: { type: Object, required: true },
    effectivePowerIndex: { type: Number, default: NEUTRAL_INDEX },
    selectedLevel: { type: Number, default: null },
    effectiveLevelIndex: { type: Number, default: null },
});

const emit = defineEmits(['select-level']);

const levels = Array.from({ length: MAX_LEVEL }, (_, i) => i + 1);

const activePowerLevel = computed(() => POWER_LEVELS[props.effectivePowerIndex]);
const activeColIndex = computed(() => props.effectiveLevelIndex);

function cellValue(powerLevel, levelIndex) {
    const row = props.grid?.[powerLevel];
    if (!row) return null;
    return row[levelIndex];
}

function isResolvedCell(powerLevel, levelIndex) {
    return powerLevel === activePowerLevel.value && levelIndex === activeColIndex.value;
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-xs table-pin-rows table-pin-cols">
            <thead>
                <tr>
                    <th class="sticky left-0 z-10 bg-base-200" />
                    <th
                        v-for="lvl in levels"
                        :key="lvl"
                        class="text-center cursor-pointer select-none transition-colors text-xs min-w-[40px]"
                        :class="{
                            'font-bold': selectedLevel === lvl,
                        }"
                        :style="{
                            color: levelColor(lvl),
                            backgroundColor: activeColIndex === lvl - 1 ? 'rgba(99, 102, 241, 0.12)' : undefined,
                        }"
                        @click="emit('select-level', lvl)"
                    >
                        {{ lvl }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(pl, plIdx) in POWER_LEVELS"
                    :key="pl"
                    class="transition-all duration-200"
                    :style="{
                        backgroundColor: plIdx === effectivePowerIndex ? POWER_BG_COLORS[pl] : undefined,
                    }"
                >
                    <td
                        class="sticky left-0 z-10 bg-base-200 font-medium text-xs whitespace-nowrap pr-2"
                        :style="{ borderLeftColor: POWER_COLORS[pl], borderLeftWidth: plIdx === effectivePowerIndex ? '3px' : '1px' }"
                    >
                        <span
                            class="inline-block w-2 h-2 rounded-full mr-1"
                            :style="{ backgroundColor: POWER_COLORS[pl] }"
                        />
                        {{ POWER_LABELS[pl] }}
                    </td>
                    <td
                        v-for="(lvl, lvlIdx) in levels"
                        :key="lvlIdx"
                        class="text-center text-xs transition-all duration-200"
                        :class="{
                            'font-bold ring-2 ring-primary rounded': isResolvedCell(pl, lvlIdx),
                        }"
                        :style="{
                            backgroundColor: activeColIndex === lvlIdx
                                ? (plIdx === effectivePowerIndex ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.06)')
                                : undefined,
                        }"
                    >
                        <template v-if="cellValue(pl, lvlIdx) !== null && cellValue(pl, lvlIdx) !== undefined">
                            {{ cellValue(pl, lvlIdx) }}
                        </template>
                        <span v-else class="text-base-content/20">—</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
