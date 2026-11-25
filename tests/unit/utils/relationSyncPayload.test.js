import { describe, expect, it } from 'vitest';
import { buildRelationSyncPayload } from '@/Utils/entity/relationSyncPayload';

describe('buildRelationSyncPayload', () => {
    it('envoie [{ id, level }] pour les aptitudes / sorts / capacités nivelés', () => {
        expect(
            buildRelationSyncPayload([11, 12], ['level'], {
                11: { level: '3' },
                12: { level: '9' },
            }),
        ).toEqual([
            { id: 11, level: 3 },
            { id: 12, level: 9 },
        ]);
    });

    it('envoie [{ id, level, quantity }] pour les objets nivelés', () => {
        expect(
            buildRelationSyncPayload([8], ['level', 'quantity'], {
                8: { level: '6', quantity: '2' },
            }),
        ).toEqual([{ id: 8, level: 6, quantity: 2 }]);
    });

    it('garde { id: { quantity } } pour les recettes / boutiques', () => {
        expect(
            buildRelationSyncPayload([4], ['quantity'], {
                4: { quantity: '3' },
            }),
        ).toEqual({ 4: { quantity: 3 } });
    });

    it('garde { id: { character_level } } pour les emplacements de sorts', () => {
        expect(
            buildRelationSyncPayload([9], ['character_level', 'slot_index', 'choice_order'], {
                9: { character_level: '1', slot_index: '2', choice_order: '0' },
            }),
        ).toEqual({
            9: { character_level: 1, slot_index: 2, choice_order: 0 },
        });
    });

    it('envoie une liste d’ids sans pivot', () => {
        expect(buildRelationSyncPayload([1, 2], null)).toEqual([1, 2]);
        expect(buildRelationSyncPayload([], ['level'])).toEqual([]);
    });
});
