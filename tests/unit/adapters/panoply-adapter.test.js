/**
 * Tests unitaires pour panoply-adapter
 *
 * @description
 * Vérifie que :
 * - buildPanoplyCell génère correctement les cellules
 * - adaptPanoplyEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildPanoplyCell, adaptPanoplyEntitiesTableResponse } from '@/Entities/panoply/panoply-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.panoplies.show') {
            return `/panoplies/${params?.panoply || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('panoply-adapter', () => {
    describe('buildPanoplyCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Panoply' };
            const cell = buildPanoplyCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Panoply');
            expect(cell.params.href).toContain('/panoplies/1');
            expect(cell.params.searchValue).toBe('Test Panoply');
            expect(cell.params.sortValue).toBe('Test Panoply');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildPanoplyCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildPanoplyCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildPanoplyCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildPanoplyCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptPanoplyEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'panoplies',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Panoply 1' },
                    { id: 2, name: 'Panoply 2' },
                ],
            };

            const result = adaptPanoplyEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('panoplies');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Panoply 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'panoplies', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptPanoplyEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('panoplies');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'panoplies', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptPanoplyEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'panoplies', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptPanoplyEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

