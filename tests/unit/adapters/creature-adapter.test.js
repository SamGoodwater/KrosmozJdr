/**
 * Tests unitaires pour creature-adapter
 *
 * @description
 * Vérifie que :
 * - buildCreatureCell génère correctement les cellules
 * - adaptCreatureEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildCreatureCell, adaptCreatureEntitiesTableResponse } from '@/Entities/creature/creature-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.creatures.show') {
            return `/creatures/${params?.creature || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('creature-adapter', () => {
    describe('buildCreatureCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Creature' };
            const cell = buildCreatureCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Creature');
            expect(cell.params.href).toContain('/creatures/1');
            expect(cell.params.searchValue).toBe('Test Creature');
            expect(cell.params.sortValue).toBe('Test Creature');
        });

        it('génère une cellule text pour level', () => {
            const entity = { id: 1, level: '10' };
            const cell = buildCreatureCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('10');
            expect(cell.params.sortValue).toBe(10);
        });

        it('génère "-" pour level null', () => {
            const entity = { id: 1, level: null };
            const cell = buildCreatureCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour life', () => {
            const entity = { id: 1, life: '30' };
            const cell = buildCreatureCell('life', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('30');
        });

        it('génère une cellule text pour pa', () => {
            const entity = { id: 1, pa: '6' };
            const cell = buildCreatureCell('pa', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('6');
        });

        it('génère une cellule badge pour usable (true)', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildCreatureCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
            expect(cell.params.sortValue).toBe(1);
        });

        it('génère une cellule badge pour usable (false)', () => {
            const entity = { id: 1, usable: 0 };
            const cell = buildCreatureCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Non');
            expect(cell.params.color).toBe('neutral');
            expect(cell.params.sortValue).toBe(0);
        });

        it('génère une cellule badge pour hostility', () => {
            const entity = { id: 1, hostility: 1 };
            const cell = buildCreatureCell('hostility', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Neutre');
            expect(cell.params.color).toBe('info');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildCreatureCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildCreatureCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptCreatureEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'creatures',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Creature 1', level: '10', life: '30', pa: '6' },
                    { id: 2, name: 'Creature 2', level: '20', life: '50', pa: '8' },
                ],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('creatures');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Creature 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('creatures');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', level: '10', customField: 'custom' };
            const response = {
                meta: { entityType: 'creatures', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptCreatureEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

