/**
 * Tests unitaires pour npc-adapter
 *
 * @description
 * Vérifie que :
 * - buildNpcCell génère correctement les cellules
 * - adaptNpcEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildNpcCell, adaptNpcEntitiesTableResponse } from '@/Entities/npc/npc-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.npcs.show') {
            return `/npcs/${params?.npc || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('npc-adapter', () => {
    describe('buildNpcCell', () => {
        it('génère une cellule route pour creature_name', () => {
            const entity = {
                id: 1,
                creature: { id: 1, name: 'Test NPC' },
            };
            const cell = buildNpcCell('creature_name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test NPC');
            expect(cell.params.href).toContain('/npcs/1');
        });

        it('génère "-" pour creature_name si creature est null', () => {
            const entity = { id: 1, creature: null };
            const cell = buildNpcCell('creature_name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour age', () => {
            const entity = { id: 1, age: '25 ans' };
            const cell = buildNpcCell('age', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('25 ans');
        });

        it('génère une cellule text pour size', () => {
            const entity = { id: 1, size: '1m75' };
            const cell = buildNpcCell('size', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('1m75');
        });

        it('génère une cellule text pour classe', () => {
            const entity = {
                id: 1,
                classe: { id: 1, name: 'Iop' },
            };
            const cell = buildNpcCell('classe', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Iop');
        });

        it('génère "-" pour classe null', () => {
            const entity = { id: 1, classe: null };
            const cell = buildNpcCell('classe', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour specialization', () => {
            const entity = {
                id: 1,
                specialization: { id: 1, name: 'Guerrier' },
            };
            const cell = buildNpcCell('specialization', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Guerrier');
        });

        it('génère "-" pour specialization null', () => {
            const entity = { id: 1, specialization: null };
            const cell = buildNpcCell('specialization', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptNpcEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'npcs',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, age: '25', size: 'Moyen' },
                    { id: 2, age: '30', size: 'Grand' },
                ],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('npcs');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('npcs');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, age: '25', customField: 'custom' };
            const response = {
                meta: { entityType: 'npcs', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptNpcEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

