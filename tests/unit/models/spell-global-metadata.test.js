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
});
