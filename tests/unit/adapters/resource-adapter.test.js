/**
 * Tests unitaires pour resource-adapter
 *
 * @description
 * Vérifie que :
 * - buildResourceCell génère correctement les cellules
 * - adaptResourceEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildResourceCell, adaptResourceEntitiesTableResponse } from '@/Entities/resource/resource-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.resources.show') {
            return `/resources/${params?.resource || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('resource-adapter', () => {
    describe('buildResourceCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Resource' };
            const cell = buildResourceCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Resource');
            expect(cell.params.href).toContain('/resources/1');
            expect(cell.params.searchValue).toBe('Test Resource');
            expect(cell.params.sortValue).toBe('Test Resource');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildResourceCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildResourceCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour resource_type', () => {
            const entity = {
                id: 1,
                resourceType: { id: 1, name: 'Wood', slug: 'wood' },
            };
            const cell = buildResourceCell('resource_type', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Wood');
        });

        it('génère "-" pour resource_type null', () => {
            const entity = { id: 1, resourceType: null };
            const cell = buildResourceCell('resource_type', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildResourceCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildResourceCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptResourceEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'resources',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Resource 1' },
                    { id: 2, name: 'Resource 2' },
                ],
            };

            const result = adaptResourceEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('resources');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Resource 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
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

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'resources', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptResourceEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

