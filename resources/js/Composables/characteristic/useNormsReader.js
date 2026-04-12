import { ref, computed } from 'vue';
import { POWER_LEVELS, NEUTRAL_INDEX, MAX_LEVEL } from '@/Utils/Characteristic/normsConstants';

/**
 * Logique de lecture interactive d'une grille de normes.
 * Gère la sélection du level, l'activation des conditions et le calcul des indices effectifs.
 *
 * @param {import('vue').Ref<object|null>} grid - Reactive ref vers la grille {power_level: [val1..val20]}
 * @param {import('vue').Ref<Array>} conditions - Reactive ref vers les conditions
 */
export function useNormsReader(grid, conditions) {
    const selectedLevel = ref(null);
    const activeConditionIndices = ref(new Set());

    function toggleCondition(index) {
        const next = new Set(activeConditionIndices.value);
        if (next.has(index)) {
            next.delete(index);
        } else {
            next.add(index);
        }
        activeConditionIndices.value = next;
    }

    function selectLevel(level) {
        selectedLevel.value = selectedLevel.value === level ? null : level;
    }

    function clearSelection() {
        selectedLevel.value = null;
        activeConditionIndices.value = new Set();
    }

    const powerOffset = computed(() => {
        if (!conditions.value) return 0;
        let offset = 0;
        for (const idx of activeConditionIndices.value) {
            const cond = conditions.value[idx];
            if (cond?.target === 'power') {
                offset += cond.modifier ?? 0;
            }
        }
        return offset;
    });

    const levelOffset = computed(() => {
        if (!conditions.value) return 0;
        let offset = 0;
        for (const idx of activeConditionIndices.value) {
            const cond = conditions.value[idx];
            if (cond?.target === 'level') {
                offset += cond.modifier ?? 0;
            }
        }
        return offset;
    });

    const effectivePowerIndex = computed(() => {
        return Math.max(0, Math.min(POWER_LEVELS.length - 1, NEUTRAL_INDEX + powerOffset.value));
    });

    const effectivePowerLevel = computed(() => POWER_LEVELS[effectivePowerIndex.value]);

    const effectiveLevelIndex = computed(() => {
        if (selectedLevel.value === null) return null;
        const base = selectedLevel.value - 1;
        return Math.max(0, Math.min(MAX_LEVEL - 1, base + levelOffset.value));
    });

    const resolvedValue = computed(() => {
        if (!grid.value || effectiveLevelIndex.value === null) return null;
        const row = grid.value[effectivePowerLevel.value];
        if (!row) return null;
        return row[effectiveLevelIndex.value] ?? null;
    });

    return {
        selectedLevel,
        activeConditionIndices,
        toggleCondition,
        selectLevel,
        clearSelection,
        powerOffset,
        levelOffset,
        effectivePowerIndex,
        effectivePowerLevel,
        effectiveLevelIndex,
        resolvedValue,
    };
}
