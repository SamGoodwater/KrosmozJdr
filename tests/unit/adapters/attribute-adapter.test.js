/**
 * Tests unitaires pour attribute-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptAttributeEntitiesTableResponse } from '@/Entities/attribute/attribute-adapter';
import { Attribute } from '@/Models/Entity/Attribute';

describe('attribute-adapter (version simplifiée)', () => {
    describe('adaptAttributeEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Attribute', () => {
            const response = {
                meta: { entityType: 'attributes', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Attribute 1' }],
            };

            const result = adaptAttributeEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('attributes');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Attribute);
        });

        it('gère un tableau vide', () => {
            const result = adaptAttributeEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptAttributeEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
