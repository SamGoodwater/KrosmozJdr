/**
 * Tests unitaires pour monster-descriptors
 *
 * @description
 * Vérifie que :
 * - La structure des descriptors est correcte
 * - visibleIf / editableIf fonctionnent correctement
 * - La configuration bulk est correcte
 * - Les groupes de champs sont définis
 * - QUICK_EDIT_FIELDS est cohérent avec les champs bulk
 */

import { describe, it, expect } from 'vitest';
import { getMonsterFieldDescriptors } from '@/Entities/monster/monster-descriptors';

describe('monster-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getMonsterFieldDescriptors();
            const requiredFields = [
                'id',
                'creature_name',
                'creature_level',
                'creature_hostility',
                'creature_life',
                'state',
                'read_level',
                'write_level',
            ];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                const label = descriptors[field].label ?? descriptors[field].general?.label;
                expect(label).toBeDefined();
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('chaque colonne tableau avec cellules définit table.cell.sizes', () => {
            const descriptors = getMonsterFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.table?.cell) {
                    expect(desc.table.cell).toHaveProperty('sizes');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('permissions.visibleIf sur id suit canCreateAny (fermeture sur le factory)', () => {
            const on = getMonsterFieldDescriptors({ capabilities: { createAny: true } });
            const off = getMonsterFieldDescriptors({ capabilities: { createAny: false } });
            expect(on.id.permissions?.visibleIf?.()).toBe(true);
            expect(off.id.permissions?.visibleIf?.()).toBe(false);
        });
    });


    describe('Configuration bulk', () => {
        it('les champs avec edition.form ou edit.form ont une configuration bulk', () => {
            const descriptors = getMonsterFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                const form = desc.edition?.form ?? desc.edit?.form;
                if (!form) return;
                const bulk = desc.edition?.bulk ?? desc.edit?.form?.bulk;
                expect(bulk).toBeDefined();
                expect(bulk).toHaveProperty('enabled');
                expect(typeof bulk.enabled).toBe('boolean');
            });
        });

        it('bulk.build est optionnel : si présent, c’est une fonction (déprécié, mappers)', () => {
            const descriptors = getMonsterFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                const bulk = desc.edition?.bulk ?? desc.edit?.form?.bulk;
                if (bulk?.build != null) {
                    expect(typeof bulk.build).toBe('function');
                }
            });
        });
    });

    describe('Filtres numériques', () => {
        it('Niveau en slider principal, stats combat en plages avancées', () => {
            const descriptors = getMonsterFieldDescriptors();
            expect(descriptors.creature_level.table.filterable).toMatchObject({
                type: 'range',
                defaultVisible: true,
            });
            for (const key of ['creature_pa', 'creature_pm', 'creature_po', 'creature_life', 'creature_ini', 'creature_ca']) {
                expect(descriptors[key].table.filterable).toMatchObject({
                    type: 'range',
                    defaultVisible: false,
                });
            }
        });
    });
});
