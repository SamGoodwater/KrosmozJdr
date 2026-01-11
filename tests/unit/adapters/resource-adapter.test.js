/**
 * Tests unitaires pour resource-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptResourceEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Resource
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Resource est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptResourceEntitiesTableResponse } from '@/Entities/resource/resource-adapter';
import { Resource } from '@/Models/Entity/Resource';

describe('resource-adapter (version simplifiée)', () => {
    describe('adaptResourceEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Resource', () => {
            const response = {
                meta: {
                    entityType: 'resources',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Resource 1', level: '10', rarity: 1 },
                    { id: 2, name: 'Resource 2', level: '20', rarity: 2 },
                ],
            };

            const result = adaptResourceEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('resources');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Resource est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Resource);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Resource 1');
            expect(result.rows[0].rowParams.entity.level).toBe(10);
            expect(result.rows[0].rowParams.entity.rarity).toBe(1);
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'resources', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptResourceEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('resources');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'resources', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptResourceEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Resource', () => {
            const entity = { id: 1, name: 'Test', level: '15', rarity: 3, customField: 'custom' };
            const response = {
                meta: { entityType: 'resources', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptResourceEntitiesTableResponse(response);

            const resource = result.rows[0].rowParams.entity;
            expect(resource).toBeInstanceOf(Resource);
            expect(resource.id).toBe(1);
            expect(resource.name).toBe('Test');
            expect(resource.level).toBe(15);
            expect(resource.rarity).toBe(3);
            // Les champs personnalisés sont préservés dans _data
            expect(resource._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'resources', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', level: null, rarity: null },
                ],
            };

            const result = adaptResourceEntitiesTableResponse(response);

            const resource = result.rows[0].rowParams.entity;
            expect(resource).toBeInstanceOf(Resource);
            expect(resource.id).toBe(1);
            expect(resource.name).toBe('Test');
            expect(resource.level).toBeNull();
            expect(resource.rarity).toBeNull();
        });
    });
});

