/**
 * Tests unitaires pour spell-adapter
 *
 * @description
 * Vérifie que :
 * - buildSpellCell génère correctement les cellules
 * - adaptSpellEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 * - Le formatage fonctionne correctement
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import { buildSpellCell, adaptSpellEntitiesTableResponse } from '@/Entities/spell/spell-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.spells.show') {
            return `/spells/${params?.spell || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('spell-adapter', () => {
    describe('buildSpellCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Spell' };
            const cell = buildSpellCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Spell');
            expect(cell.params.href).toContain('/spells/1');
            expect(cell.params.searchValue).toBe('Test Spell');
            expect(cell.params.sortValue).toBe('Test Spell');
        });

        it('génère une cellule text pour level', () => {
            const entity = { id: 1, level: '10' };
            const cell = buildSpellCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('10');
            expect(cell.params.sortValue).toBe(10);
        });

        it('génère "-" pour level null', () => {
            const entity = { id: 1, level: null };
            const cell = buildSpellCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour pa', () => {
            const entity = { id: 1, pa: '3' };
            const cell = buildSpellCell('pa', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('3');
            expect(cell.params.sortValue).toBe(3);
        });

        it('génère une cellule text pour po', () => {
            const entity = { id: 1, po: '2' };
            const cell = buildSpellCell('po', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('2');
        });

        it('génère une cellule badge pour usable (true)', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildSpellCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
            expect(cell.params.sortValue).toBe(1);
        });

        it('génère une cellule badge pour usable (false)', () => {
            const entity = { id: 1, usable: 0 };
            const cell = buildSpellCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Non');
            expect(cell.params.color).toBe('neutral');
            expect(cell.params.sortValue).toBe(0);
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildSpellCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildSpellCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildSpellCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour created_at', () => {
            const entity = { id: 1, created_at: '2025-01-27T10:00:00.000Z' };
            const cell = buildSpellCell('created_at', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toMatch(/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/);
        });

        it('génère "-" pour created_at null', () => {
            const entity = { id: 1, created_at: null };
            const cell = buildSpellCell('created_at', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptSpellEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'spells',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Spell 1', level: '10', pa: '3', po: '2' },
                    { id: 2, name: 'Spell 2', level: '20', pa: '4', po: '3' },
                ],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('spells');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Spell 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('spells');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('génère toutes les colonnes définies', () => {
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Test', level: '10' }],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            const expectedColumns = ['id', 'name', 'level', 'pa', 'po', 'created_by', 'created_at', 'updated_at'];
            const actualColumns = Object.keys(result.rows[0].cells);
            expectedColumns.forEach((col) => {
                expect(actualColumns).toContain(col);
            });
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', level: '10', customField: 'custom' };
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

