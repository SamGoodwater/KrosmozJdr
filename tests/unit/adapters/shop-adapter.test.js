/**
 * Tests unitaires pour shop-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptShopEntitiesTableResponse } from '@/Entities/shop/shop-adapter';
import { Shop } from '@/Models/Entity/Shop';

describe('shop-adapter (version simplifiée)', () => {
    describe('adaptShopEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Shop', () => {
            const response = {
                meta: { entityType: 'shops', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Shop 1' }],
            };

            const result = adaptShopEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('shops');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Shop);
        });

        it('gère un tableau vide', () => {
            const result = adaptShopEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptShopEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
