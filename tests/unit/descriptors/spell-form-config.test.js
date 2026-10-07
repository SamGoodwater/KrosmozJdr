import { describe, expect, it } from 'vitest';
import {
    SPELL_FORM_FIELD_SECTIONS_CREATE,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/spell/spell-form-config';

describe('sections du formulaire de sort', () => {
    it('sépare les propriétés globales des propriétés communes avec les degrés', () => {
        const global = SPELL_FORM_FIELD_SECTIONS_EDIT.find((section) => section.id === 'global_properties');
        const baseCast = SPELL_FORM_FIELD_SECTIONS_EDIT.find(
            (section) => section.id === 'base_cast_properties',
        );
        const metadata = SPELL_FORM_FIELD_SECTIONS_EDIT.find((section) => section.id === 'metadata');

        expect(global.fieldKeys).toEqual(
            expect.arrayContaining([
                'category',
                'state',
                'spellTypes',
                'is_magic',
                'element',
                'target_type',
                'ritual_available',
                'allows_reaction',
                'resolution_mode',
            ]),
        );
        expect(global.fieldKeys).not.toContain('pa');

        expect(baseCast.collapsedByDefault).toBe(true);
        expect(baseCast.fieldKeys).toEqual(
            expect.arrayContaining(['pa', 'po_min', 'cast_per_turn', 'area']),
        );
        expect(baseCast.fieldKeys).not.toContain('element');
        expect(baseCast.fieldKeys).not.toContain('ritual_available');
        expect(baseCast.fieldKeys).not.toContain('target_type');
        expect(baseCast.fieldKeys).not.toContain('resolution_mode');
        expect(baseCast.fieldKeys).not.toContain('allows_reaction');

        expect(metadata.collapsedByDefault).toBe(true);
    });

    it('conserve la séparation lors de la création', () => {
        expect(SPELL_FORM_FIELD_SECTIONS_CREATE.map((section) => section.id)).toEqual([
            'general',
            'global_properties',
            'base_cast_properties',
            'admin',
        ]);
    });
});
