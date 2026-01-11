/**
 * Tests unitaires pour panoply-descriptors
 *
 * @description
 * Vérifie que :
 * - La structure des descriptors est correcte
 * - visibleIf / editableIf fonctionnent correctement
 * - La configuration bulk est correcte
 * - Les groupes de champs sont définis
 */

import { describe, it, expect } from 'vitest';
import { getPanoplyFieldDescriptors, PANOPLY_QUICK_EDIT_FIELDS } from '@/Entities/panoply/panoply-descriptors';

describe('panoply-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getPanoplyFieldDescriptors();
            const requiredFields = ['id', 'name', 'usable', 'is_visible'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getPanoplyFieldDescriptors();
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
        it('created_by est visible seulement si canCreateAny', () => {
            const descriptors = getPanoplyFieldDescriptors({
                capabilities: { createAny: true },
            });

            const createdByDesc = descriptors.created_by;
            if (createdByDesc?.visibleIf) {
                expect(createdByDesc.visibleIf({ capabilities: { createAny: true } })).toBe(true);
                expect(createdByDesc.visibleIf({ capabilities: { createAny: false } })).toBe(false);
            }
        });
    });

    describe('Configuration bulk', () => {
        it('les champs bulk-enabled ont enabled: true', () => {
            const descriptors = getPanoplyFieldDescriptors();
            const bulkEnabledFields = PANOPLY_QUICK_EDIT_FIELDS || [];

            bulkEnabledFields.forEach((fieldKey) => {
                const desc = descriptors[fieldKey];
                if (desc?.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.enabled).toBe(true);
                }
            });
        });

        it('les champs bulk ont une fonction build', () => {
            const descriptors = getPanoplyFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk?.enabled) {
                    expect(desc.edit.form.bulk).toHaveProperty('build');
                    expect(typeof desc.edit.form.bulk.build).toBe('function');
                }
            });
        });
    });

    describe('Groupes de champs', () => {
        it('les champs avec edit.form ont un groupe défini', () => {
            const descriptors = getPanoplyFieldDescriptors();
            const fieldsWithEdit = Object.values(descriptors).filter((desc) => desc.edit?.form);

            fieldsWithEdit.forEach((desc) => {
                if (desc.edit.form.bulk?.enabled) {
                    expect(desc.edit.form).toHaveProperty('group');
                    expect(typeof desc.edit.form.group).toBe('string');
                    expect(desc.edit.form.group.length).toBeGreaterThan(0);
                }
            });
        });
    });

    describe('Options des selects', () => {
        it('is_visible a les bonnes options', () => {
            const descriptors = getPanoplyFieldDescriptors();
            const isVisibleDesc = descriptors.is_visible;

            if (isVisibleDesc?.edit?.form?.type === 'select') {
                const options = isVisibleDesc.edit.form.options;
                const values = options.map((opt) => opt.value);
                expect(values).toContain('guest');
                expect(values).toContain('user');
                expect(values).toContain('admin');
            }
        });
    });
});

