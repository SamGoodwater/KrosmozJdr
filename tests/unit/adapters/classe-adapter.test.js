/**
 * Tests unitaires pour classe-adapter
 *
 * @description
 * Vérifie que :
 * - buildClasseCell génère correctement les cellules
 * - adaptClasseEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildClasseCell, adaptClasseEntitiesTableResponse } from '@/Entities/classe/classe-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.classes.show') {
            return `/classes/${params?.classe || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('classe-adapter', () => {
    describe('buildClasseCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Classe' };
            const cell = buildClasseCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Classe');
            expect(cell.params.href).toContain('/classes/1');
            expect(cell.params.searchValue).toBe('Test Classe');
            expect(cell.params.sortValue).toBe('Test Classe');
        });

        it('génère une cellule text pour description', () => {
            const entity = { id: 1, description: 'Test Description' };
            const cell = buildClasseCell('description', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('Test Description');
        });

        it('génère "-" pour description null', () => {
            const entity = { id: 1, description: null };
            const cell = buildClasseCell('description', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });

        it('génère une cellule text pour life', () => {
            const entity = { id: 1, life: '100' };
            const cell = buildClasseCell('life', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('100');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildClasseCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildClasseCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildClasseCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildClasseCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptClasseEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'classes',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Classe 1' },
                    { id: 2, name: 'Classe 2' },
                ],
            };

            const result = adaptClasseEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('classes');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Classe 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'classes', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptClasseEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('classes');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'classes', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptClasseEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', customField: 'custom' };
            const response = {
                meta: { entityType: 'classes', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptClasseEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});

