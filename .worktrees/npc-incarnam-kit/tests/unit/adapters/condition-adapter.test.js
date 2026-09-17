/**
 * Tests unitaires pour condition-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptConditionEntitiesTableResponse } from '@/Entities/condition/condition-adapter';
import { Condition } from '@/Models/Entity/Condition';

describe('condition-adapter (version simplifiée)', () => {
    describe('adaptConditionEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Condition', () => {
            const response = {
                meta: { entityType: 'conditions', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Condition 1' }],
            };

            const result = adaptConditionEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('conditions');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Condition);
        });

        it('gère un tableau vide', () => {
            const result = adaptConditionEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptConditionEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
