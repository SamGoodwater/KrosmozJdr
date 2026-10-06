import { describe, expect, it } from 'vitest';
import { Spell } from '@/Models/Entity/Spell';

describe('Spell global metadata', () => {
    it('normalise et exporte les cinq champs globaux', () => {
        const spell = new Spell({
            cast_in_line: 1,
            cast_in_diagonal: true,
            target_type: 'trap',
            max_stack: '5',
            global_cooldown: '2',
        });

        expect(spell.castInLine).toBe(true);
        expect(spell.castInDiagonal).toBe(true);
        expect(spell.targetType).toBe('trap');
        expect(spell.maxStack).toBe(5);
        expect(spell.globalCooldown).toBe(2);
        expect(spell.toFormData()).toMatchObject({
            cast_in_line: true,
            cast_in_diagonal: true,
            target_type: 'trap',
            max_stack: 5,
            global_cooldown: 2,
        });
    });

    it('applique les valeurs par défaut frontend', () => {
        const spell = new Spell({});

        expect(spell.castInLine).toBe(false);
        expect(spell.castInDiagonal).toBe(false);
        expect(spell.targetType).toBeNull();
        expect(spell.maxStack).toBe(0);
        expect(spell.globalCooldown).toBe(0);
    });

    it('utilise les propriétés du degré par défaut dans les vues compactes', () => {
        const spell = new Spell({
            pa: '3',
            po_min: '1',
            po_max: '4',
            sight_line: false,
            spell_degrees: {
                default_degree_id: 12,
                degrees: [
                    {
                        id: 12,
                        properties: {
                            pa: '5',
                            po_min: '2',
                            po_max: '8',
                            sight_line: true,
                            global_cooldown: 3,
                        },
                    },
                ],
            },
        });

        expect(spell.pa).toBe(5);
        expect(spell.poMin).toBe('2');
        expect(spell.poMax).toBe('8');
        expect(spell.sightLine).toBe(true);
        expect(spell.globalCooldown).toBe(3);
    });
});
