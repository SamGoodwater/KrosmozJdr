import { describe, expect, it } from 'vitest';
import {
    SPELL_FORM_FIELD_SECTIONS_CREATE,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/spell/spell-form-config';

describe('sections du formulaire de sort', () => {
    it('sépare les propriétés globales des propriétés de repli', () => {
        const global = SPELL_FORM_FIELD_SECTIONS_EDIT.find((section) => section.id === 'spell_properties');
        const fallback = SPELL_FORM_FIELD_SECTIONS_EDIT.find(
            (section) => section.id === 'fallback_properties',
        );

        expect(global.fieldKeys).toEqual(
            expect.arrayContaining([
                'category',
                'state',
                'spellTypes',
                'is_magic',
                'target_type',
                'area',
                'allows_reaction',
                'resolution_mode',
            ]),
        );
        expect(global.fieldKeys).not.toContain('pa');

        expect(fallback.collapsedByDefault).toBe(true);
        expect(fallback.collapsedActionLabel).toContain('ni degrés ni effets');
        expect(fallback.fieldKeys).toEqual(
            expect.arrayContaining(['pa', 'po_min', 'element', 'effect', 'cast_per_turn']),
        );
        expect(fallback.fieldKeys).not.toContain('resolution_mode');
        expect(fallback.fieldKeys).not.toContain('allows_reaction');
    });

    it('conserve la séparation lors de la création', () => {
        expect(SPELL_FORM_FIELD_SECTIONS_CREATE.map((section) => section.id)).toEqual([
            'general',
            'spell_properties',
            'fallback_properties',
            'admin',
        ]);
    });
});
