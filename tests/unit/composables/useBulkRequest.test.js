/**
 * Tests unitaires pour useBulkRequest
 *
 * @description
 * Vérifie que :
 * - Les requêtes bulk réussissent correctement
 * - Les erreurs sont gérées
 * - Les notifications sont affichées
 */

import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useBulkRequest } from '@/Composables/entity/useBulkRequest';
import { useNotificationStore } from '@/Stores/notification';

// Mock axios
const mockAxios = {
    patch: vi.fn(),
};

vi.mock('axios', () => ({
    default: mockAxios,
}));

// Mock notification store
const mockNotificationStore = {
    addSuccess: vi.fn(),
    addError: vi.fn(),
};

vi.mock('@/Stores/notification', () => ({
    useNotificationStore: () => mockNotificationStore,
}));

describe('useBulkRequest', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe('bulkPatchJson', () => {
        it('fait une requête PATCH avec le bon payload', async () => {
            mockAxios.patch.mockResolvedValue({
                data: { success: true, summary: { updated: 2 } },
            });

            const { bulkPatchJson } = useBulkRequest();
            const payload = { ids: [1, 2], level: '50' };

            await bulkPatchJson('/api/entities/spells/bulk', payload);

            expect(mockAxios.patch).toHaveBeenCalledWith(
                '/api/entities/spells/bulk',
                payload,
                expect.objectContaining({
                    headers: expect.objectContaining({
                        'Content-Type': 'application/json',
                    }),
                })
            );
        });

        it('affiche une notification de succès', async () => {
            mockAxios.patch.mockResolvedValue({
                data: { success: true, summary: { updated: 2 } },
            });

            const { bulkPatchJson } = useBulkRequest();

            await bulkPatchJson('/api/entities/spells/bulk', { ids: [1, 2] });

            expect(mockNotificationStore.addSuccess).toHaveBeenCalled();
            expect(mockNotificationStore.addError).not.toHaveBeenCalled();
        });

        it('affiche une notification d\'erreur en cas d\'échec', async () => {
            mockAxios.patch.mockRejectedValue({
                response: {
                    data: { message: 'Erreur de validation' },
                    status: 422,
                },
            });

            const { bulkPatchJson } = useBulkRequest();

            try {
                await bulkPatchJson('/api/entities/spells/bulk', { ids: [1, 2] });
            } catch (e) {
                // Expected
            }

            expect(mockNotificationStore.addError).toHaveBeenCalled();
            expect(mockNotificationStore.addSuccess).not.toHaveBeenCalled();
        });

        it('gère les erreurs réseau', async () => {
            mockAxios.patch.mockRejectedValue({
                message: 'Network Error',
            });

            const { bulkPatchJson } = useBulkRequest();

            try {
                await bulkPatchJson('/api/entities/spells/bulk', { ids: [1, 2] });
            } catch (e) {
                // Expected
            }

            expect(mockNotificationStore.addError).toHaveBeenCalled();
        });

        it('retourne true en cas de succès', async () => {
            mockAxios.patch.mockResolvedValue({
                data: { success: true, summary: { updated: 2 } },
            });

            const { bulkPatchJson } = useBulkRequest();

            const result = await bulkPatchJson('/api/entities/spells/bulk', { ids: [1, 2] });

            expect(result).toBe(true);
        });

        it('retourne false en cas d\'échec', async () => {
            mockAxios.patch.mockResolvedValue({
                data: { success: false, errors: [{ id: 1, error: 'Not found' }] },
            });

            const { bulkPatchJson } = useBulkRequest();

            const result = await bulkPatchJson('/api/entities/spells/bulk', { ids: [1, 2] });

            expect(result).toBe(false);
        });

        it('gère les erreurs partielles', async () => {
            mockAxios.patch.mockResolvedValue({
                data: {
                    success: false,
                    summary: { requested: 2, updated: 1, errors: 1 },
                    errors: [{ id: 2, error: 'Not found' }],
                },
            });

            const { bulkPatchJson } = useBulkRequest();

            const result = await bulkPatchJson('/api/entities/spells/bulk', { ids: [1, 2] });

            expect(result).toBe(false);
            expect(mockNotificationStore.addError).toHaveBeenCalled();
        });
    });
});

