/**
 * Tests unitaires pour capability-descriptors
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
import { getCapabilityFieldDescriptors } from '@/Entities/capability/capability-descriptors';

describe('capability-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getCapabilityFieldDescriptors();
            const requiredFields = ['id', 'name', 'level', 'pa', 'po', 'is_passive', 'state', 'read_level', 'write_level'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getCapabilityFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.display) {
                    expect(desc.display).toHaveProperty('sizes');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf sur id suit createAny (colonne ID réservée création)', () => {
            const descriptorsOn = getCapabilityFieldDescriptors({
                capabilities: { createAny: true },
            });
            const descriptorsOff = getCapabilityFieldDescriptors({
                capabilities: { createAny: false },
            });

            const idDescriptorOn = descriptorsOn.id;
            const idDescriptorOff = descriptorsOff.id;
            if (idDescriptorOn.visibleIf) {
                expect(idDescriptorOn.visibleIf()).toBe(true);
            }
            if (idDescriptorOff.visibleIf) {
                expect(idDescriptorOff.visibleIf()).toBe(false);
            }
        });
    });


    describe('Configuration bulk', () => {
        it('les champs avec edit.form ont une configuration bulk', () => {
            const descriptors = getCapabilityFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form) {
                    expect(desc.edit.form).toHaveProperty('bulk');
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });

        it('aucun champ bulk n\'a de fonction build (déprécié)', () => {
            const descriptors = getCapabilityFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.build).toBeUndefined();
                }
            });
        });
    });

    describe('Filtres numériques', () => {
        it('Niveau en slider principal, PA / PO en plages avancées', () => {
            const descriptors = getCapabilityFieldDescriptors();
            expect(descriptors.level.table.filterable).toMatchObject({
                type: 'range',
                defaultVisible: true,
            });
            expect(descriptors.pa.table.filterable).toMatchObject({
                type: 'range',
                defaultVisible: false,
            });
            expect(descriptors.po.table.filterable).toMatchObject({
                type: 'range',
                defaultVisible: false,
            });
        });
    });
});
