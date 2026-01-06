/**
 * Tests unitaires pour scenario-adapter
 *
 * @description
 * Vérifie que :
 * - buildScenarioCell génère correctement les cellules
 * - adaptScenarioEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildScenarioCell, adaptScenarioEntitiesTableResponse } from '@/Entities/scenario/scenario-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.scenarios.show') {
            return `/scenarios/${params?.scenario || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('scenario-adapter', () => {
    describe('buildScenarioCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Scenario' };
            const cell = buildScenarioCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Scenario');
            expect(cell.params.href).toContain('/scenarios/1');
            expect(cell.params.searchValue).toBe('Test Scenario');
            expect(cell.params.sortValue).toBe('Test Scenario');
        });

        it('génère une cellule text pour state', () => {
            const entity = { id: 1, state: 1 };
            const cell = buildScenarioCell('state', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBeDefined();
        });

        it('génère une cellule badge pour is_public', () => {
            const entity = { id: 1, is_public: true };
            const cell = buildScenarioCell('is_public', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildScenarioCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildScenarioCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildScenarioCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildScenarioCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptScenarioEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'scenarios',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Scenario 1', state: 1 },
                    { id: 2, name: 'Scenario 2', state: 2 },
                ],
            };

            const result = adaptScenarioEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('scenarios');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Scenario 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'scenarios', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptScenarioEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('scenarios');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'scenarios', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptScenarioEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', state: 1, customField: 'custom' };
            const response = {
                meta: { entityType: 'scenarios', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptScenarioEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});
