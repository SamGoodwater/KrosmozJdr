/**
 * @vitest-environment node
 */
import { describe, it, expect } from 'vitest';
import { ref, nextTick } from 'vue';
import { useNormsReader } from '@/Composables/characteristic/useNormsReader';

function makeGrid() {
    return {
        very_weak:   Array.from({ length: 20 }, (_, i) => i + 1),
        weak:        Array.from({ length: 20 }, (_, i) => (i + 1) * 2),
        neutral:     Array.from({ length: 20 }, (_, i) => (i + 1) * 3),
        strong:      Array.from({ length: 20 }, (_, i) => (i + 1) * 4),
        very_strong: Array.from({ length: 20 }, (_, i) => (i + 1) * 5),
    };
}

describe('useNormsReader', () => {
    it('defaults to neutral power index (2)', () => {
        const grid = ref(makeGrid());
        const conditions = ref([]);
        const reader = useNormsReader(grid, conditions);
        expect(reader.effectivePowerIndex.value).toBe(2);
        expect(reader.effectivePowerLevel.value).toBe('neutral');
    });

    it('resolvedValue is null when no level selected', () => {
        const grid = ref(makeGrid());
        const conditions = ref([]);
        const reader = useNormsReader(grid, conditions);
        expect(reader.resolvedValue.value).toBeNull();
    });

    it('resolvedValue picks neutral row at selected level', () => {
        const grid = ref(makeGrid());
        const conditions = ref([]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(5);
        // neutral row, index 4 (level 5) => 5 * 3 = 15
        expect(reader.resolvedValue.value).toBe(15);
    });

    it('toggleCondition applies power offset', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'pa', operator: '=', value: 2, target: 'power', modifier: -1, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(10);

        // Before toggle: neutral (index 2), level 10 => 10 * 3 = 30
        expect(reader.effectivePowerIndex.value).toBe(2);
        expect(reader.resolvedValue.value).toBe(30);

        // Toggle condition 0: modifier = -1 => power index 2 + (-1) = 1 (weak)
        reader.toggleCondition(0);
        await nextTick();
        expect(reader.effectivePowerIndex.value).toBe(1);
        // weak row, level index 9 => 10 * 2 = 20
        expect(reader.resolvedValue.value).toBe(20);
    });

    it('toggleCondition applies level offset', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'zone', operator: '>=', value: 6, target: 'level', modifier: -2, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(10);

        // Before: neutral, level index 9
        expect(reader.effectiveLevelIndex.value).toBe(9);

        reader.toggleCondition(0);
        await nextTick();
        // level index 9 + (-2) = 7
        expect(reader.effectiveLevelIndex.value).toBe(7);
        // neutral row, index 7 => 8 * 3 = 24
        expect(reader.resolvedValue.value).toBe(24);
    });

    it('multiple conditions stack', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'pa', operator: '=', value: 2, target: 'power', modifier: -1, comment: '' },
            { characteristic_key: 'range', operator: '=', value: 1, target: 'power', modifier: 1, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(5);

        // Both active: -1 + 1 = 0 offset => still neutral
        reader.toggleCondition(0);
        reader.toggleCondition(1);
        await nextTick();
        expect(reader.effectivePowerIndex.value).toBe(2);
        expect(reader.resolvedValue.value).toBe(15);
    });

    it('clamps power index to bounds', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'a', operator: '=', value: 1, target: 'power', modifier: -10, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(1);

        reader.toggleCondition(0);
        await nextTick();
        // 2 + (-10) = -8 => clamped to 0
        expect(reader.effectivePowerIndex.value).toBe(0);
        expect(reader.effectivePowerLevel.value).toBe('very_weak');
    });

    it('clamps level index to bounds', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'a', operator: '=', value: 1, target: 'level', modifier: -50, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(3);

        reader.toggleCondition(0);
        await nextTick();
        // 2 + (-50) = -48 => clamped to 0
        expect(reader.effectiveLevelIndex.value).toBe(0);
    });

    it('toggle deactivates a condition', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'pa', operator: '=', value: 2, target: 'power', modifier: -1, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);

        reader.toggleCondition(0);
        await nextTick();
        expect(reader.activeConditionIndices.value.has(0)).toBe(true);

        reader.toggleCondition(0);
        await nextTick();
        expect(reader.activeConditionIndices.value.has(0)).toBe(false);
        expect(reader.effectivePowerIndex.value).toBe(2);
    });

    it('clearSelection resets state', async () => {
        const grid = ref(makeGrid());
        const conditions = ref([
            { characteristic_key: 'pa', operator: '=', value: 2, target: 'power', modifier: -1, comment: '' },
        ]);
        const reader = useNormsReader(grid, conditions);
        reader.selectLevel(10);
        reader.toggleCondition(0);
        await nextTick();

        reader.clearSelection();
        await nextTick();
        expect(reader.selectedLevel.value).toBeNull();
        expect(reader.activeConditionIndices.value.size).toBe(0);
    });

    it('selectLevel toggles off if same level clicked', () => {
        const grid = ref(makeGrid());
        const conditions = ref([]);
        const reader = useNormsReader(grid, conditions);

        reader.selectLevel(5);
        expect(reader.selectedLevel.value).toBe(5);

        reader.selectLevel(5);
        expect(reader.selectedLevel.value).toBeNull();
    });
});
