/**
 * Tests unitaires pour l'adapter breed (réponse table API → rows avec instances Breed)
 *
 * @description
 * Vérifie que getEntityResponseAdapter('breeds') transforme correctement
 * une réponse backend en TableResponse avec instances Breed.
 */

import { describe, it, expect } from 'vitest';
import { getEntityResponseAdapter } from '@/Entities/entity-registry';
import { Breed } from '@/Models/Entity/Breed';

describe('breed-adapter', () => {
    describe('getEntityResponseAdapter(breeds)', () => {
        it('transforme entities en TableResponse avec instances Breed', () => {
            const adapter = getEntityResponseAdapter('breeds');
            expect(adapter).toBeDefined();

            const response = {
                meta: { entityType: 'breeds', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Classe 1' }],
            };

            const result = adapter(response);

            expect(result.meta.entityType).toBe('breeds');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Breed);
        });

        it('gère un tableau vide', () => {
            const adapter = getEntityResponseAdapter('breeds');
            const result = adapter({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const adapter = getEntityResponseAdapter('breeds');
            const result = adapter({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
