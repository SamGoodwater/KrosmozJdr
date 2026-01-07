/**
 * Tests unitaires pour capability-adapter
 *
 * @description
 * Vérifie que :
 * - buildCapabilityCell génère correctement les cellules
 * - adaptCapabilityEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildCapabilityCell, adaptCapabilityEntitiesTableResponse } from '@/Entities/capability/capability-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.capabilities.show') {
            return `/capabilities/${params?.capability || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('capability-adapter', () => {
    describe('buildCapabilityCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Capability' };
            const cell = buildCapabilityCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Capability');
            expect(cell.params.href).toContain('/capabilities/1');
            expect(cell.params.searchValue).toBe('Test Capability');
            expect(cell.params.sortValue).toBe('Test Capability');
        });

        it('génère une cellule text pour level', () => {
            const entity = { id: 1, level: '10' };
            const cell = buildCapabilityCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('10');
        });

        it('génère "-" pour level null', () => {
            const entity = { id: 1, level: null };
            const cell = buildCapabilityCell('level', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour pa', () => {
            const entity = { id: 1, pa: '5' };
            const cell = buildCapabilityCell('pa', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('5');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildCapabilityCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildCapabilityCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildCapabilityCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildCapabilityCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptCapabilityEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'capabilities',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Capability 1' },
                    { id: 2, name: 'Capability 2' },
                ],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('capabilities');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Capability 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('capabilities');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'capabilities', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptCapabilityEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

