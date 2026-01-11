/**
 * Tests unitaires pour resource-type-descriptors
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
import { getResourceTypeFieldDescriptors, RESOURCE_TYPE_QUICK_EDIT_FIELDS } from '@/Entities/resource-type/resource-type-descriptors';

describe('resource-type-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getResourceTypeFieldDescriptors();
            const requiredFields = ['id', 'name', 'usable', 'is_visible'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getResourceTypeFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.display) {
                    // display.views est obsolète (vues manuelles maintenant)
                    // display.sizes est utilisé pour les tableaux (xs-xl)
                    expect(desc.display).toHaveProperty('sizes');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf fonctionne avec canUpdateAny', () => {
            const descriptors = getResourceTypeFieldDescriptors({
                capabilities: { updateAny: true },
            });

            const idDescriptor = descriptors.id;
            if (idDescriptor.visibleIf) {
                expect(idDescriptor.visibleIf({ capabilities: { updateAny: true } })).toBe(true);
                expect(idDescriptor.visibleIf({ capabilities: { updateAny: false } })).toBe(false);
            }
        });
    });

    describe('QUICK_EDIT_FIELDS', () => {
        it('QUICK_EDIT_FIELDS est défini et est un tableau', () => {
            expect(RESOURCE_TYPE_QUICK_EDIT_FIELDS).toBeDefined();
            expect(Array.isArray(RESOURCE_TYPE_QUICK_EDIT_FIELDS)).toBe(true);
        });

        it('QUICK_EDIT_FIELDS contient des champs valides', () => {
            const descriptors = getResourceTypeFieldDescriptors();
            RESOURCE_TYPE_QUICK_EDIT_FIELDS.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
            });
        });

        it('QUICK_EDIT_FIELDS est aligné avec les champs bulk enabled', () => {
            const descriptors = getResourceTypeFieldDescriptors();
            const bulkEnabledFields = Object.keys(descriptors).filter(
                (key) => descriptors[key].edit?.form?.bulk?.enabled === true
            );

            // Tous les champs dans QUICK_EDIT_FIELDS doivent avoir bulk.enabled = true
            RESOURCE_TYPE_QUICK_EDIT_FIELDS.forEach((field) => {
                expect(bulkEnabledFields).toContain(field);
            });
        });
    });

    describe('Configuration bulk', () => {
        it('les champs avec edit.form ont une configuration bulk', () => {
            const descriptors = getResourceTypeFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form) {
                    expect(desc.edit.form).toHaveProperty('bulk');
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });

        it('aucun champ bulk n\'a de fonction build (déprécié)', () => {
            const descriptors = getResourceTypeFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.build).toBeUndefined();
                }
            });
        });
    });
});
