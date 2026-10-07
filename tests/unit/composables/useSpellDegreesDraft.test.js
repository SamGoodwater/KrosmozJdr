import { describe, expect, it } from 'vitest';
import {
    resolveDegreeProperties,
    spellFallbackProperties,
    serializeEffectRowForApi,
} from '@/Composables/entity/useSpellDegreesDraft.js';

describe('useSpellDegreesDraft helpers', () => {
    it('resolveDegreeProperties own utilise le degré puis le sort', () => {
        const spell = spellFallbackProperties({ pa: '2', area: 'point' });
        const degrees = [
            {
                id: 1,
                properties_source: 'own',
                properties: { pa: '4', area: null },
            },
        ];
        const resolved = resolveDegreeProperties(degrees[0], degrees, spell);
        expect(resolved.pa).toBe('4');
        expect(resolved.pa_source).toBe('degree');
        expect(resolved.area).toBe('point');
        expect(resolved.area_source).toBe('spell');
    });

    it('resolveDegreeProperties previous remonte au degré précédent', () => {
        const spell = spellFallbackProperties({ pa: '1' });
        const degrees = [
            { id: 1, properties_source: 'own', properties: { pa: '5' } },
            { id: 2, properties_source: 'previous', properties: { pa: '9' } },
        ];
        const resolved = resolveDegreeProperties(degrees[1], degrees, spell);
        expect(resolved.pa).toBe('5');
        expect(resolved.pa_source).toBe('previous');
    });

    it('serializeEffectRowForApi inclut value_formula_crit', () => {
        const row = serializeEffectRowForApi(
            {
                sub_effect_id: 3,
                scope: 'combat',
                crit_only: true,
                duration_formula: '2',
                params: { value_formula: '1d6', value_formula_crit: '2d6' },
            },
            0,
        );
        expect(row.params.value_formula_crit).toBe('2d6');
        expect(row.crit_only).toBe(true);
    });
});
