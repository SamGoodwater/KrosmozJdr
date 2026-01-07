/**
 * Tests unitaires pour consumable-adapter
 *
 * @description
 * Vérifie que :
 * - buildConsumableCell génère correctement les cellules
 * - adaptConsumableEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildConsumableCell, adaptConsumableEntitiesTableResponse } from '@/Entities/consumable/consumable-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.consumables.show') {
            return `/consumables/${params?.consumable || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('consumable-adapter', () => {
    describe('buildConsumableCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Consumable' };
            const cell = buildConsumableCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Consumable');
            expect(cell.params.href).toContain('/consumables/1');
            expect(cell.params.searchValue).toBe('Test Consumable');
            expect(cell.params.sortValue).toBe('Test Consumable');
        });

        it('génère une cellule badge pour rarity', () => {
            const entity = { id: 1, rarity: 3 };
            const cell = buildConsumableCell('rarity', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Très rare');
            expect(cell.params.color).toBe('warning');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildConsumableCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildConsumableCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour consumable_type', () => {
            const entity = {
                id: 1,
                consumableType: { id: 1, name: 'Potion', slug: 'potion' },
            };
            const cell = buildConsumableCell('consumable_type', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Potion');
        });

        it('génère "-" pour consumable_type null', () => {
            const entity = { id: 1, consumableType: null };
            const cell = buildConsumableCell('consumable_type', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildConsumableCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildConsumableCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptConsumableEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'consumables',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Consumable 1' },
                    { id: 2, name: 'Consumable 2' },
                ],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('consumables');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Consumable 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('consumables');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'consumables', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptConsumableEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

