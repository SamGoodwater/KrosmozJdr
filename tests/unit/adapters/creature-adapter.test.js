/**
 * Tests unitaires pour creature-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptCreatureEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Creature
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Creature est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptCreatureEntitiesTableResponse } from '@/Entities/creature/creature-adapter';
import { Creature } from '@/Models/Entity/Creature';

describe('creature-adapter (version simplifiée)', () => {
    describe('adaptCreatureEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Creature', () => {
            const response = {
                meta: {
                    entityType: 'creatures',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Creature 1', level: '10', hostility: 2 },
                    { id: 2, name: 'Creature 2', level: '20', hostility: 3 },
                ],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('creatures');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Creature est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Creature);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('creatures');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Creature', () => {
            const entity = { id: 1, name: 'Test', level: '10', customField: 'custom' };
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            const creature = result.rows[0].rowParams.entity;
            expect(creature).toBeInstanceOf(Creature);
            expect(creature.id).toBe(1);
            expect(creature.name).toBe('Test');
            // Les champs personnalisés sont préservés dans _data
            expect(creature._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', level: null },
                ],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            const creature = result.rows[0].rowParams.entity;
            expect(creature).toBeInstanceOf(Creature);
            expect(creature.id).toBe(1);
            expect(creature.name).toBe('Test');
            expect(creature.level).toBeNull();
        });
    });
});
