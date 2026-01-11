/**
 * Tests unitaires pour consumable-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptConsumableEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Consumable
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Consumable est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptConsumableEntitiesTableResponse } from '@/Entities/consumable/consumable-adapter';
import { Consumable } from '@/Models/Entity/Consumable';

describe('consumable-adapter (version simplifiée)', () => {
    describe('adaptConsumableEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Consumable', () => {
            const response = {
                meta: {
                    entityType: 'consumables',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Consumable 1', level: '10', rarity: 1 },
                    { id: 2, name: 'Consumable 2', level: '20', rarity: 2 },
                ],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('consumables');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Consumable est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Consumable);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Consumable 1');
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('consumables');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Consumable', () => {
            const entity = { id: 1, name: 'Test', level: '15', rarity: 3, customField: 'custom' };
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            const consumable = result.rows[0].rowParams.entity;
            expect(consumable).toBeInstanceOf(Consumable);
            expect(consumable.id).toBe(1);
            expect(consumable.name).toBe('Test');
            expect(consumable.level).toBe(15);
            expect(consumable.rarity).toBe(3);
            // Les champs personnalisés sont préservés dans _data
            expect(consumable._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', level: null, rarity: null },
                ],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            const consumable = result.rows[0].rowParams.entity;
            expect(consumable).toBeInstanceOf(Consumable);
            expect(consumable.id).toBe(1);
            expect(consumable.name).toBe('Test');
            expect(consumable.level).toBeNull();
            expect(consumable.rarity).toBeNull();
        });
    });
});
