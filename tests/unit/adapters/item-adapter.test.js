/**
 * Tests unitaires pour item-adapter
 *
 * @description
 * Vérifie que :
 * - buildItemCell génère correctement les cellules
 * - adaptItemEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildItemCell, adaptItemEntitiesTableResponse } from '@/Entities/item/item-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.items.show') {
            return `/items/${params?.item || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('item-adapter', () => {
    describe('buildItemCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Item' };
            const cell = buildItemCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Item');
            expect(cell.params.href).toContain('/items/1');
            expect(cell.params.searchValue).toBe('Test Item');
            expect(cell.params.sortValue).toBe('Test Item');
        });

        it('génère une cellule text pour level', () => {
            const entity = { id: 1, level: '10' };
            const cell = buildItemCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('10');
            expect(cell.params.sortValue).toBe(10);
        });

        it('génère "-" pour level null', () => {
            const entity = { id: 1, level: null };
            const cell = buildItemCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule badge pour rarity', () => {
            const entity = { id: 1, rarity: 1 };
            const cell = buildItemCell('rarity', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBeDefined();
            expect(cell.params.color).toBeDefined();
        });

        it('génère une cellule badge pour usable (true)', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildItemCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
            expect(cell.params.sortValue).toBe(1);
        });

        it('génère une cellule badge pour usable (false)', () => {
            const entity = { id: 1, usable: 0 };
            const cell = buildItemCell('usable', entity, {}, { context: 'table' });

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
            const cell = buildItemCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildItemCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptItemEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
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
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Item 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
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

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', level: '10', customField: 'custom' };
            const response = {
                meta: { entityType: 'items', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptItemEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

