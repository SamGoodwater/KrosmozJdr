/**
 * Tests unitaires pour resource-type-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptResourceTypeEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de ResourceType
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance ResourceType est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptResourceTypeEntitiesTableResponse } from '@/Entities/resource-type/resource-type-adapter';
import { ResourceType } from '@/Models/Entity/ResourceType';

describe('resource-type-adapter (version simplifiée)', () => {
    describe('adaptResourceTypeEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances ResourceType', () => {
            const response = {
                meta: {
                    entityType: 'resource-types',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Resource Type 1', state: 'playable' },
                    { id: 2, name: 'Resource Type 2', state: 'draft' },
                ],
            };

            const result = adaptResourceTypeEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('resource-types');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance ResourceType est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(ResourceType);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Resource Type 1');
            expect(result.rows[0].rowParams.entity.state).toBe('playable');
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'resource-types', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptResourceTypeEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('resource-types');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'resource-types', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptResourceTypeEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance ResourceType', () => {
            const entity = { id: 1, name: 'Test', state: 'playable', customField: 'custom' };
            const response = {
                meta: { entityType: 'resource-types', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptResourceTypeEntitiesTableResponse(response);

            const resourceType = result.rows[0].rowParams.entity;
            expect(resourceType).toBeInstanceOf(ResourceType);
            expect(resourceType.id).toBe(1);
            expect(resourceType.name).toBe('Test');
            expect(resourceType.state).toBe('playable');
        });
    });
});
