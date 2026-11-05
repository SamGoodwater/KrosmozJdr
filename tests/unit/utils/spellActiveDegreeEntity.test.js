import { describe, expect, it } from 'vitest';
import { Spell } from '@/Models/Entity/Spell';
import { buildSpellActiveDegreeEntity } from '@/Utils/Entity/spellActiveDegreeEntity';

const degrees = [
    { id: 1, required_level: 1, properties: { cast_per_turn: '2', cast_per_target: '1', po_max: '5' }, rows: [] },
    { id: 2, required_level: 5, properties: { cast_per_turn: '2', cast_per_target: '2', po_max: '5' }, rows: [] },
    { id: 3, required_level: 15, properties: { cast_per_turn: '3', cast_per_target: '2', po_max: '6' }, rows: [{ id: 9 }] },
];

const spell = new Spell({
    id: 651,
    cast_per_turn: '2',
    cast_per_target: '1',
    po_max: '5',
    spell_degrees: { default_degree_id: 1, degrees },
});

describe('buildSpellActiveDegreeEntity', () => {
    it('lit les lancers et la portée du degré actif, pas du premier degré', () => {
        const at15 = buildSpellActiveDegreeEntity(spell, degrees[2]);

        expect(at15).toBeInstanceOf(Spell);
        expect(at15.castPerTurn).toBe('3');
        expect(at15.castPerTarget).toBe('2');
        expect(at15.poMax).toBe('6');
        expect(at15._data._active_degree_rows).toEqual([{ id: 9 }]);
    });

    it('change de valeur à chaque onglet', () => {
        const perTarget = degrees.map((degree) => buildSpellActiveDegreeEntity(spell, degree).castPerTarget);

        expect(perTarget).toEqual(['1', '2', '2']);
    });

    it('rend le sort tel quel sans degré actif', () => {
        expect(buildSpellActiveDegreeEntity(spell, null)).toBe(spell);
    });
});
