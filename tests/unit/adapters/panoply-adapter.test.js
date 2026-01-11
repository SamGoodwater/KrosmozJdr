/**
 * Tests unitaires pour panoply-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptPanoplyEntitiesTableResponse } from '@/Entities/panoply/panoply-adapter';
import { Panoply } from '@/Models/Entity/Panoply';

describe('panoply-adapter (version simplifiée)', () => {
    describe('adaptPanoplyEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Panoply', () => {
            const response = {
                meta: { entityType: 'panoplies', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Panoply 1' }],
            };

            const result = adaptPanoplyEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('panoplies');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Panoply);
        });

        it('gère un tableau vide', () => {
            const result = adaptPanoplyEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptPanoplyEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
