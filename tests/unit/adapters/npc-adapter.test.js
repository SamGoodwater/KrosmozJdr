/**
 * Tests unitaires pour npc-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptNpcEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Npc
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Npc est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptNpcEntitiesTableResponse } from '@/Entities/npc/npc-adapter';
import { Npc } from '@/Models/Entity/Npc';

describe('npc-adapter (version simplifiée)', () => {
    describe('adaptNpcEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Npc', () => {
            const response = {
                meta: {
                    entityType: 'npcs',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'NPC 1' },
                    { id: 2, name: 'NPC 2' },
                ],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('npcs');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Npc est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Npc);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('npcs');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Npc', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            const npc = result.rows[0].rowParams.entity;
            expect(npc).toBeInstanceOf(Npc);
            expect(npc.id).toBe(1);
            expect(npc.name).toBe('Test');
            // Les champs personnalisés sont préservés dans _data
            expect(npc._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', classe_id: null },
                ],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            const npc = result.rows[0].rowParams.entity;
            expect(npc).toBeInstanceOf(Npc);
            expect(npc.id).toBe(1);
            expect(npc.name).toBe('Test');
            expect(npc.classe_id).toBeNull();
        });
    });
});
