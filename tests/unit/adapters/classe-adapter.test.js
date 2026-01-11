/**
 * Tests unitaires pour classe-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptClasseEntitiesTableResponse } from '@/Entities/classe/classe-adapter';
import { Classe } from '@/Models/Entity/Classe';

describe('classe-adapter (version simplifiée)', () => {
    describe('adaptClasseEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Classe', () => {
            const response = {
                meta: { entityType: 'classes', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Classe 1' }],
            };

            const result = adaptClasseEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('classes');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Classe);
        });

        it('gère un tableau vide', () => {
            const result = adaptClasseEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptClasseEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
