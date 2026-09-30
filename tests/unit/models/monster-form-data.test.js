import { describe, expect, it } from 'vitest';
import { Monster } from '@/Models/Entity/Monster';

describe('Monster.toFormData access levels', () => {
    it('exporte state, read_level et write_level pour le formulaire d’édition', () => {
        const monster = new Monster({
            id: 12,
            size: 2,
            is_boss: false,
            boss_pa: null,
            monster_race_id: 4,
            auto_update: true,
            state: 'playable',
            read_level: 3,
            write_level: 4,
        });

        expect(monster.toFormData()).toMatchObject({
            size: 2,
            monster_race_id: 4,
            auto_update: true,
            state: 'playable',
            read_level: 3,
            write_level: 4,
        });
        expect(monster.read_level).toBe(3);
        expect(monster.write_level).toBe(4);
    });
});
