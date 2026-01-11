/**
 * Tests unitaires pour capability-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptCapabilityEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Capability
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Capability est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptCapabilityEntitiesTableResponse } from '@/Entities/capability/capability-adapter';
import { Capability } from '@/Models/Entity/Capability';

describe('capability-adapter (version simplifiée)', () => {
    describe('adaptCapabilityEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Capability', () => {
            const response = {
                meta: {
                    entityType: 'capabilities',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Capability 1', description: 'Desc 1' },
                    { id: 2, name: 'Capability 2', description: 'Desc 2' },
                ],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('capabilities');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Capability est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Capability);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Capability 1');
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('capabilities');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Capability', () => {
            const entity = { id: 1, name: 'Test', description: 'Test desc', customField: 'custom' };
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            const capability = result.rows[0].rowParams.entity;
            expect(capability).toBeInstanceOf(Capability);
            expect(capability.id).toBe(1);
            expect(capability.name).toBe('Test');
            // Les champs personnalisés sont préservés dans _data
            expect(capability._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', description: null },
                ],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            const capability = result.rows[0].rowParams.entity;
            expect(capability).toBeInstanceOf(Capability);
            expect(capability.id).toBe(1);
            expect(capability.name).toBe('Test');
            expect(capability.description).toBeNull();
        });
    });
});
