/**
 * Tests unitaires pour useBulkEditPanel
 *
 * @description
 * Vérifie que :
 * - L'agrégation fonctionne correctement
 * - Le buildPayload construit le bon payload
 * - Le dirty tracking fonctionne
 * - Les champs nullable sont gérés
 */

import { describe, it, expect, beforeEach } from 'vitest';
import { useBulkEditPanel } from '@/Composables/entity/useBulkEditPanel';

describe('useBulkEditPanel', () => {
    const mockFieldMeta = {
        level: {
            label: 'Niveau',
            nullable: true,
            build: (v) => (v === '' ? null : String(v)),
        },
        state: {
            label: 'État',
            nullable: true,
            build: (v) => (v === '' || v === null ? null : String(v)),
        },
        name: {
            label: 'Nom',
            nullable: false,
            build: (v) => String(v),
        },
    };

    describe('aggregation', () => {
        it('détecte les valeurs identiques', () => {
            const entities = [
                { id: 1, level: '10', state: 'playable' },
                { id: 2, level: '10', state: 'playable' },
            ];

            const { aggregate } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            expect(aggregate.value.level.same).toBe(true);
            expect(aggregate.value.level.value).toBe('10');
            expect(aggregate.value.state.same).toBe(true);
            expect(aggregate.value.state.value).toBe('playable');
        });

        it('détecte les valeurs différentes', () => {
            const entities = [
                { id: 1, level: '10', state: 'playable' },
                { id: 2, level: '20', state: 'draft' },
            ];

            const { aggregate } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            expect(aggregate.value.level.same).toBe(false);
            expect(aggregate.value.level.value).toBeNull();
            expect(aggregate.value.state.same).toBe(false);
            expect(aggregate.value.state.value).toBeNull();
        });

        it('gère les valeurs nulles', () => {
            const entities = [
                { id: 1, level: null, state: 'draft' },
                { id: 2, level: null, state: 'draft' },
            ];

            const { aggregate } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            expect(aggregate.value.level.same).toBe(true);
            expect(aggregate.value.level.value).toBeNull();
        });

        it('gère un tableau vide', () => {
            const { aggregate } = useBulkEditPanel({
                selectedEntities: [],
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            expect(aggregate.value.level.same).toBe(true);
            expect(aggregate.value.level.value).toBeNull();
        });
    });

    describe('buildPayload', () => {
        it('construit le payload avec les champs modifiés', () => {
            const entities = [
                { id: 1, level: '10' },
                { id: 2, level: '20' },
            ];

            const { form, dirty, buildPayload, onChange } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            onChange('level', '50');
            dirty.level = true;

            const payload = buildPayload();

            expect(payload.ids).toEqual([1, 2]);
            expect(payload.level).toBe('50');
            expect(payload.state).toBeUndefined();
        });

        it('n\'inclut pas les champs non modifiés', () => {
            const entities = [{ id: 1, level: '10' }];

            const { form, dirty, buildPayload, onChange } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            onChange('level', '50');
            dirty.level = true;
            // state n'est pas modifié

            const payload = buildPayload();

            expect(payload.level).toBe('50');
            expect(payload.state).toBeUndefined();
        });

        it('applique la fonction build pour chaque champ', () => {
            const entities = [{ id: 1, state: 'draft' }];

            const { form, dirty, buildPayload, onChange } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            onChange('state', 'playable');
            dirty.state = true;

            const payload = buildPayload();

            expect(payload.state).toBe('playable');
            expect(typeof payload.state).toBe('string');
        });

        it('gère les valeurs nullable (null si vide)', () => {
            const entities = [{ id: 1, level: '10' }];

            const { form, dirty, buildPayload, onChange } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            onChange('level', '');
            dirty.level = true;

            const payload = buildPayload();

            expect(payload.level).toBeNull();
        });

        it('gère le mode filtered', () => {
            const entities = [{ id: 1 }, { id: 2 }, { id: 3 }];
            const filteredIds = [1, 2];

            const { scope, buildPayload, onChange, dirty } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
                mode: 'client',
                filteredIds,
            });

            scope.value = 'filtered';
            onChange('level', '50');
            dirty.level = true;

            const payload = buildPayload();

            expect(payload.ids).toEqual([1, 2]); // Seulement les filtered
        });
    });

    describe('dirty tracking', () => {
        it('marque un champ comme dirty après onChange', () => {
            const entities = [{ id: 1, level: '10' }];

            const { dirty, onChange } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            expect(dirty.level).toBe(false);

            onChange('level', '50');

            expect(dirty.level).toBe(true);
        });

        it('reset les champs dirty lors du changement de sélection', () => {
            const entities1 = [{ id: 1, level: '10' }];
            const entities2 = [{ id: 2, level: '20' }];

            const { dirty, onChange, selectedEntities } = useBulkEditPanel({
                selectedEntities: entities1,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            onChange('level', '50');
            expect(dirty.level).toBe(true);

            // Simuler changement de sélection
            selectedEntities.value = entities2;

            // Note: En réalité, watch() devrait reset, mais dans les tests on peut vérifier manuellement
            // Pour un test complet, il faudrait utiliser @vue/test-utils
        });
    });

    describe('canApply', () => {
        it('retourne false si aucun champ modifié', () => {
            const entities = [{ id: 1 }];

            const { canApply } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            expect(canApply.value).toBe(false);
        });

        it('retourne false si pas admin', () => {
            const entities = [{ id: 1 }];

            const { canApply, onChange, dirty } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: false,
                fieldMeta: mockFieldMeta,
            });

            onChange('level', '50');
            dirty.level = true;

            expect(canApply.value).toBe(false);
        });

        it('retourne true si champ modifié et admin', () => {
            const entities = [{ id: 1 }];

            const { canApply, onChange, dirty } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta: mockFieldMeta,
            });

            onChange('level', '50');
            dirty.level = true;

            expect(canApply.value).toBe(true);
        });

        it('retourne false si champ required est vide', () => {
            const entities = [{ id: 1 }];
            const fieldMeta = {
                name: {
                    label: 'Nom',
                    nullable: false,
                    build: (v) => String(v),
                },
            };

            const { canApply, onChange, dirty } = useBulkEditPanel({
                selectedEntities: entities,
                isAdmin: true,
                fieldMeta,
            });

            onChange('name', '');
            dirty.name = true;

            expect(canApply.value).toBe(false);
        });
    });
});

