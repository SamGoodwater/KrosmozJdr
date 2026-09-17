/**
 * Tests unitaires pour item-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptItemEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Item
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Item est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptItemEntitiesTableResponse } from '@/Entities/item/item-adapter';
import { Item } from '@/Models/Entity/Item';

describe('item-adapter (version simplifiée)', () => {
    describe('adaptItemEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Item', () => {
            const response = {
                meta: {
                    entityType: 'items',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Item 1', level: '10', rarity: 1 },
                    { id: 2, name: 'Item 2', level: '20', rarity: 2 },
                ],
            };

            const result = adaptItemEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('items');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Item est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Item);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Item 1');
            expect(result.rows[0].rowParams.entity.level).toBe(10);
            expect(result.rows[0].rowParams.entity.rarity).toBe(1);
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'items', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptItemEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('items');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'items', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptItemEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Item', () => {
            const entity = { id: 1, name: 'Test', level: '15', rarity: 3, customField: 'custom' };
            const response = {
                meta: { entityType: 'items', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptItemEntitiesTableResponse(response);

            const item = result.rows[0].rowParams.entity;
            expect(item).toBeInstanceOf(Item);
            expect(item.id).toBe(1);
            expect(item.name).toBe('Test');
            expect(item.level).toBe(15);
            expect(item.rarity).toBe(3);
            // Les champs personnalisés sont préservés dans _data
            expect(item._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'items', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', level: null, rarity: null },
                ],
            };

            const result = adaptItemEntitiesTableResponse(response);

            const item = result.rows[0].rowParams.entity;
            expect(item).toBeInstanceOf(Item);
            expect(item.id).toBe(1);
            expect(item.name).toBe('Test');
            expect(item.level).toBeNull();
            expect(item.rarity).toBeNull();
        });
    });
});

