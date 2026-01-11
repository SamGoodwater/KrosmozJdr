/**
 * Tests unitaires pour scenario-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptScenarioEntitiesTableResponse } from '@/Entities/scenario/scenario-adapter';
import { Scenario } from '@/Models/Entity/Scenario';

describe('scenario-adapter (version simplifiée)', () => {
    describe('adaptScenarioEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Scenario', () => {
            const response = {
                meta: { entityType: 'scenarios', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Scenario 1' }],
            };

            const result = adaptScenarioEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('scenarios');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Scenario);
        });

        it('gère un tableau vide', () => {
            const result = adaptScenarioEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptScenarioEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
