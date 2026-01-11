/**
 * Tests unitaires pour campaign-adapter (version simplifiée)
 */

import { describe, it, expect } from 'vitest';
import { adaptCampaignEntitiesTableResponse } from '@/Entities/campaign/campaign-adapter';
import { Campaign } from '@/Models/Entity/Campaign';

describe('campaign-adapter (version simplifiée)', () => {
    describe('adaptCampaignEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Campaign', () => {
            const response = {
                meta: { entityType: 'campaigns', query: {}, capabilities: {} },
                entities: [{ id: 1, name: 'Campaign 1' }],
            };

            const result = adaptCampaignEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('campaigns');
            expect(result.rows).toHaveLength(1);
            expect(result.rows[0].id).toBe(1);
            expect(result.rows[0].cells).toEqual({});
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Campaign);
        });

        it('gère un tableau vide', () => {
            const result = adaptCampaignEntitiesTableResponse({ meta: {}, entities: [] });
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null', () => {
            const result = adaptCampaignEntitiesTableResponse({ meta: {}, entities: null });
            expect(result.rows).toHaveLength(0);
        });
    });
});
