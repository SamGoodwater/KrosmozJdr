/**
 * Tests unitaires pour specialization-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptSpecializationEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Specialization
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Specialization est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptSpecializationEntitiesTableResponse } from '@/Entities/specialization/specialization-adapter';
import { Specialization } from '@/Models/Entity/Specialization';

describe('specialization-adapter (version simplifiée)', () => {
    describe('adaptSpecializationEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Specialization', () => {
            const response = {
                meta: {
                    entityType: 'specializations',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Specialization 1', description: 'Desc 1' },
                    { id: 2, name: 'Specialization 2', description: 'Desc 2' },
                ],
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('specializations');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Specialization est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Specialization);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Specialization 1');
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'specializations', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('specializations');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'specializations', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Specialization', () => {
            const entity = { id: 1, name: 'Test', description: 'Test desc', customField: 'custom' };
            const response = {
                meta: { entityType: 'specializations', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            const specialization = result.rows[0].rowParams.entity;
            expect(specialization).toBeInstanceOf(Specialization);
            expect(specialization.id).toBe(1);
            expect(specialization.name).toBe('Test');
            // Les champs personnalisés sont préservés dans _data
            expect(specialization._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'specializations', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', description: null },
                ],
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            const specialization = result.rows[0].rowParams.entity;
            expect(specialization).toBeInstanceOf(Specialization);
            expect(specialization.id).toBe(1);
            expect(specialization.name).toBe('Test');
            expect(specialization.description).toBeNull();
        });
    });
});
