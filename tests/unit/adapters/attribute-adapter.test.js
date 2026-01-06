/**
 * Tests unitaires pour attribute-adapter
 *
 * @description
 * Vérifie que :
 * - buildAttributeCell génère correctement les cellules
 * - adaptAttributeEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildAttributeCell, adaptAttributeEntitiesTableResponse } from '@/Entities/attribute/attribute-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.attributes.show') {
            return `/attributes/${params?.attribute || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('attribute-adapter', () => {
    describe('buildAttributeCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Attribute' };
            const cell = buildAttributeCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Attribute');
            expect(cell.params.href).toContain('/attributes/1');
            expect(cell.params.searchValue).toBe('Test Attribute');
            expect(cell.params.sortValue).toBe('Test Attribute');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildAttributeCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule badge pour usable (true)', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildAttributeCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
            expect(cell.params.sortValue).toBe(1);
        });

        it('génère une cellule badge pour usable (false)', () => {
            const entity = { id: 1, usable: 0 };
            const cell = buildAttributeCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Non');
            expect(cell.params.color).toBe('neutral');
            expect(cell.params.sortValue).toBe(0);
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildAttributeCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildAttributeCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptAttributeEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'attributes',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Attribute 1', is_visible: 'guest' },
                    { id: 2, name: 'Attribute 2', is_visible: 'admin' },
                ],
            };

            const result = adaptAttributeEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('attributes');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Attribute 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'attributes', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptAttributeEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('attributes');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'attributes', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptAttributeEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'attributes', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptAttributeEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

