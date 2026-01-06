/**
 * Tests unitaires pour shop-adapter
 *
 * @description
 * Vérifie que :
 * - buildShopCell génère correctement les cellules
 * - adaptShopEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildShopCell, adaptShopEntitiesTableResponse } from '@/Entities/shop/shop-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.shops.show') {
            return `/shops/${params?.shop || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('shop-adapter', () => {
    describe('buildShopCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Shop' };
            const cell = buildShopCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Shop');
            expect(cell.params.href).toContain('/shops/1');
            expect(cell.params.searchValue).toBe('Test Shop');
            expect(cell.params.sortValue).toBe('Test Shop');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildShopCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildShopCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildShopCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildShopCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptShopEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'shops',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Shop 1' },
                    { id: 2, name: 'Shop 2' },
                ],
            };

            const result = adaptShopEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('shops');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Shop 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'shops', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptShopEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('shops');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'shops', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptShopEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'shops', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptShopEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

