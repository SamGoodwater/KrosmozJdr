/**
 * Tests unitaires pour specialization-adapter
 *
 * @description
 * Vérifie que :
 * - buildSpecializationCell génère correctement les cellules
 * - adaptSpecializationEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildSpecializationCell, adaptSpecializationEntitiesTableResponse } from '@/Entities/specialization/specialization-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.specializations.show') {
            return `/specializations/${params?.specialization || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('specialization-adapter', () => {
    describe('buildSpecializationCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Specialization' };
            const cell = buildSpecializationCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Specialization');
            expect(cell.params.href).toContain('/specializations/1');
            expect(cell.params.searchValue).toBe('Test Specialization');
            expect(cell.params.sortValue).toBe('Test Specialization');
        });

        it('génère une cellule text pour description', () => {
            const entity = { id: 1, description: 'Test Description' };
            const cell = buildSpecializationCell('description', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Test Description');
        });

        it('génère "-" pour description null', () => {
            const entity = { id: 1, description: null };
            const cell = buildSpecializationCell('description', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildSpecializationCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildSpecializationCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour classe', () => {
            const entity = {
                id: 1,
                classe: { id: 1, name: 'Féca', slug: 'feca' },
            };
            const cell = buildSpecializationCell('classe', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Féca');
        });

        it('génère "-" pour classe null', () => {
            const entity = { id: 1, classe: null };
            const cell = buildSpecializationCell('classe', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildSpecializationCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildSpecializationCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptSpecializationEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'specializations',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Specialization 1' },
                    { id: 2, name: 'Specialization 2' },
                ],
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('specializations');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Specialization 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
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

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'specializations', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptSpecializationEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

