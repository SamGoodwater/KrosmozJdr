/**
 * Tests unitaires pour campaign-adapter
 *
 * @description
 * Vérifie que :
 * - buildCampaignCell génère correctement les cellules
 * - adaptCampaignEntitiesTableResponse transforme correctement les données
 * - Les valeurs nulles sont gérées
 * - Les relations sont gérées
 */

import { describe, it, expect, vi } from 'vitest';
import { buildCampaignCell, adaptCampaignEntitiesTableResponse } from '@/Entities/campaign/campaign-adapter';

// Mock de route() pour les tests
vi.mock('@inertiajs/vue3', () => ({
    route: (name, params) => {
        if (name === 'entities.campaigns.show') {
            return `/campaigns/${params?.campaign || params || ''}`;
        }
        return `#${name}`;
    },
}));

describe('campaign-adapter', () => {
    describe('buildCampaignCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Campaign' };
            const cell = buildCampaignCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Campaign');
            expect(cell.params.href).toContain('/campaigns/1');
            expect(cell.params.searchValue).toBe('Test Campaign');
            expect(cell.params.sortValue).toBe('Test Campaign');
        });

        it('génère une cellule text pour state', () => {
            const entity = { id: 1, state: 1 };
            const cell = buildCampaignCell('state', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBeDefined();
        });

        it('génère une cellule badge pour is_public', () => {
            const entity = { id: 1, is_public: 1 };
            const cell = buildCampaignCell('is_public', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour usable', () => {
            const entity = { id: 1, usable: 1 };
            const cell = buildCampaignCell('usable', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Oui');
            expect(cell.params.color).toBe('success');
        });

        it('génère une cellule badge pour is_visible', () => {
            const entity = { id: 1, is_visible: 'admin' };
            const cell = buildCampaignCell('is_visible', entity, {}, { context: 'table' });

            expect(cell.type).toBe('badge');
            expect(cell.value).toBe('Administrateur');
            expect(cell.params.color).toBe('error');
        });

        it('génère une cellule text pour created_by', () => {
            const entity = {
                id: 1,
                createdBy: { id: 1, name: 'John Doe', email: 'john@example.com' },
            };
            const cell = buildCampaignCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('John Doe');
        });

        it('génère "-" pour created_by null', () => {
            const entity = { id: 1, createdBy: null };
            const cell = buildCampaignCell('created_by', entity, {}, { context: 'table' });

            expect(cell.type).toBe('text');
            expect(cell.value).toBe('-');
        });
    });

    describe('adaptCampaignEntitiesTableResponse', () => {
        it('transforme entities en TableResponse', () => {
            const response = {
                meta: {
                    entityType: 'campaigns',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Campaign 1', state: 1 },
                    { id: 2, name: 'Campaign 2', state: 2 },
                ],
            };

            const result = adaptCampaignEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('campaigns');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells.name.type).toBe('route');
            expect(result.rows[0].cells.name.value).toBe('Campaign 1');
            expect(result.rows[0].rowParams.entity).toBeDefined();
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'campaigns', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptCampaignEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('campaigns');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'campaigns', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptCampaignEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve les rowParams.entity', () => {
            const entity = { id: 1, name: 'Test', state: 1, customField: 'custom' };
            const response = {
                meta: { entityType: 'campaigns', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptCampaignEntitiesTableResponse(response);

            expect(result.rows[0].rowParams.entity).toEqual(entity);
            expect(result.rows[0].rowParams.entity.customField).toBe('custom');
        });
    });
});
