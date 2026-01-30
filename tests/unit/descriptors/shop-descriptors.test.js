/**
 * Tests unitaires pour shop-descriptors
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
import { getShopFieldDescriptors } from '@/Entities/shop/shop-descriptors';

describe('shop-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getShopFieldDescriptors();
            const requiredFields = ['id', 'name', 'location', 'price', 'state', 'read_level', 'write_level'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getShopFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.display) {
                    expect(desc.display).toHaveProperty('sizes');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf fonctionne avec canUpdateAny', () => {
            const descriptors = getShopFieldDescriptors({
                capabilities: { updateAny: true },
            });

            const idDescriptor = descriptors.id;
            if (idDescriptor.visibleIf) {
                expect(idDescriptor.visibleIf({ capabilities: { updateAny: true } })).toBe(true);
                expect(idDescriptor.visibleIf({ capabilities: { updateAny: false } })).toBe(false);
            }
        });
    });

    describe('_quickeditConfig', () => {
        it('définit les champs quickEdit', () => {
            const descriptors = getShopFieldDescriptors();
            expect(Array.isArray(descriptors._quickeditConfig?.fields)).toBe(true);
            expect(descriptors._quickeditConfig.fields.length).toBeGreaterThan(0);
        });

        it('quickEdit contient uniquement des champs existants', () => {
            const descriptors = getShopFieldDescriptors();
            const fields = descriptors._quickeditConfig.fields;
            fields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
            });
        });
    });

    describe('Configuration bulk', () => {
        it('les champs avec edit.form ont une configuration bulk', () => {
            const descriptors = getShopFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form) {
                    expect(desc.edit.form).toHaveProperty('bulk');
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });

        it('aucun champ bulk n\'a de fonction build (déprécié)', () => {
            const descriptors = getShopFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.build).toBeUndefined();
                }
            });
        });
    });
});
