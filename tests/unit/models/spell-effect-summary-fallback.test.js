/**
 * Fallback colonne Effets lorsque effect_usages_chips est vide.
 */
import { describe, it, expect } from 'vitest';
import { Spell } from '@/Models/Entity/Spell';

describe('Spell._toEffectSummaryCell fallback', () => {
    it('affiche effect_usages_summary quand les chips sont absents', () => {
        const spell = new Spell({
            id: 1,
            name: 'Test',
            effect_usages_chips: [],
            effect_usages_summary: 'Dégâts feu D2',
        });

        const cell = spell.toCell('effect_summary', { context: 'table', size: 'md' });

        expect(cell.type).toBe('text');
        expect(cell.value).toBe('Dégâts feu D2');
    });

    it('utilise _toEffectCell quand seul effect HTML est présent', () => {
        const spell = new Spell({
            id: 2,
            name: 'Test',
            effect_usages_chips: [],
            effect_usages_summary: '',
            effect: '<p>Brûlure</p>',
        });

        const cell = spell.toCell('effect_summary', { context: 'table', size: 'md' });

        expect(cell.value).not.toBe('—');
        expect(String(cell.params?.searchValue || cell.value || '')).not.toBe('');
    });
});
