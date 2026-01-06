/**
 * Tests unitaires pour resource-type-adapter
 *
 * @description
 * Vérifie que :
 * - buildResourceTypeCell génère correctement les cellules
 * - adaptResourceTypeEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildResourceTypeCell, adaptResourceTypeEntitiesTableResponse } from '@/Entities/resource-type/resource-type-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.resource-types.show') {
            return `/resource-types/${params?.resourceType || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('resource-type-adapter', () => {
    describe('buildResourceTypeCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Resource Type' };
            const cell = buildResourceTypeCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Resource Type');
            expect(cell.params.href).toContain('/resource-types/1');
            expect(cell.params.searchValue).toBe('Test Resource Type');
            expect(cell.params.sortValue).toBe('Test Resource Type');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildResourceTypeCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildResourceTypeCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildResourceTypeCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildResourceTypeCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptResourceTypeEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'resource-types',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Resource Type 1' },
                    { id: 2, name: 'Resource Type 2' },
                ],
            };

            const result = adaptResourceTypeEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('resource-types');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Resource Type 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
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

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'resource-types', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptResourceTypeEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

