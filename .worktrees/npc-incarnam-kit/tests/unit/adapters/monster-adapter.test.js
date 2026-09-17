/**
 * Tests unitaires pour monster-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptMonsterEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Monster
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Monster est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptMonsterEntitiesTableResponse } from '@/Entities/monster/monster-adapter';
import { Monster } from '@/Models/Entity/Monster';

describe('monster-adapter (version simplifiée)', () => {
    describe('adaptMonsterEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Monster', () => {
            const response = {
                meta: {
                    entityType: 'monsters',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Monster 1', level: '10', hostility: 2, life: '30' },
                    { id: 2, name: 'Monster 2', level: '20', hostility: 3, life: '50' },
                ],
            };

            const result = adaptMonsterEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('monsters');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Monster est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Monster);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'monsters', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptMonsterEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('monsters');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'monsters', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptMonsterEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Monster', () => {
            const entity = { id: 1, name: 'Test', level: '10', customField: 'custom' };
            const response = {
                meta: { entityType: 'monsters', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptMonsterEntitiesTableResponse(response);

            const monster = result.rows[0].rowParams.entity;
            expect(monster).toBeInstanceOf(Monster);
            expect(monster.id).toBe(1);
            expect(monster.name).toBe('Test');
            // Les champs personnalisés sont préservés dans _data
            expect(monster._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'monsters', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', level: null },
                ],
            };

            const result = adaptMonsterEntitiesTableResponse(response);

            const monster = result.rows[0].rowParams.entity;
            expect(monster).toBeInstanceOf(Monster);
            expect(monster.id).toBe(1);
            expect(monster.name).toBe('Test');
            expect(monster.level).toBeNull();
        });
    });
});
