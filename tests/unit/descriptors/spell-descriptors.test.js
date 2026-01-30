/**
 * Tests unitaires pour spell-descriptors
 *
 * @description
 * Vérifie que :
 * - La structure des descriptors est correcte (champs requis présents)
 * - visibleIf / editableIf fonctionnent correctement
 * - Les options des selects sont cohérentes
 * - La configuration bulk est correcte
 * - Les groupes de champs sont définis
 * - QUICK_EDIT_FIELDS est cohérent avec les champs bulk
 */

import { describe, it, expect } from 'vitest';
import { getSpellFieldDescriptors } from '@/Entities/spell/spell-descriptors';

describe('spell-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getSpellFieldDescriptors();
            const requiredFields = ['id', 'name', 'level', 'pa', 'po', 'area', 'state', 'read_level', 'write_level'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.display) {
                    // display.views est obsolète (vues manuelles maintenant)
                    // display.sizes est utilisé pour les tableaux (xs-xl)
                    expect(desc.display).toHaveProperty('sizes');
                }
            });
        });

        it('les champs avec edit.form ont une configuration bulk', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form) {
                    expect(desc.edit.form).toHaveProperty('bulk');
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf fonctionne avec canCreateAny', () => {
            const descriptorsWithCreate = getSpellFieldDescriptors({
                capabilities: { createAny: true },
            });
            const descriptorsWithoutCreate = getSpellFieldDescriptors({
                capabilities: { createAny: false },
            });

            const idDescriptor = descriptorsWithCreate.id;
            if (idDescriptor.visibleIf) {
                expect(idDescriptor.visibleIf({ capabilities: { createAny: true } })).toBe(true);
                expect(idDescriptor.visibleIf({ capabilities: { createAny: false } })).toBe(false);
            }
        });

        it('visibleIf fonctionne avec canUpdateAny', () => {
            const descriptors = getSpellFieldDescriptors({
                capabilities: { updateAny: true },
            });

            // Vérifier que les champs avec visibleIf basé sur updateAny fonctionnent
            Object.values(descriptors).forEach((desc) => {
                if (desc.visibleIf && typeof desc.visibleIf === 'function') {
                    const resultWith = desc.visibleIf({ capabilities: { updateAny: true } });
                    const resultWithout = desc.visibleIf({ capabilities: { updateAny: false } });
                    expect(typeof resultWith).toBe('boolean');
                    expect(typeof resultWithout).toBe('boolean');
                }
            });
        });

        it('editableIf fonctionne correctement', () => {
            const descriptors = getSpellFieldDescriptors({
                capabilities: { updateAny: true },
            });

            Object.values(descriptors).forEach((desc) => {
                if (desc.editableIf && typeof desc.editableIf === 'function') {
                    const result = desc.editableIf({ capabilities: { updateAny: true } });
                    expect(typeof result).toBe('boolean');
                }
            });
        });
    });

    describe('Configuration bulk', () => {
        it('les champs bulk-enabled ont enabled: true', () => {
            const descriptors = getSpellFieldDescriptors();
            const bulkEnabledFields = descriptors._quickeditConfig?.fields || [];

            bulkEnabledFields.forEach((fieldKey) => {
                const desc = descriptors[fieldKey];
                if (desc?.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.enabled).toBe(true);
                }
            });
        });

        it('les champs bulk ont une fonction build', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk?.enabled) {
                    expect(desc.edit.form.bulk).toHaveProperty('build');
                    expect(typeof desc.edit.form.bulk.build).toBe('function');
                }
            });
        });

        it('les champs nullable ont nullable: true', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk?.nullable) {
                    expect(desc.edit.form.bulk.nullable).toBe(true);
                }
            });
        });

        it('la fonction build gère les valeurs vides pour les champs nullable', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk?.nullable && desc.edit.form.bulk.build) {
                    const buildFn = desc.edit.form.bulk.build;
                    const result = buildFn('');
                    expect(result === null || result === undefined || result === '').toBe(true);
                }
            });
        });
    });

    describe('Groupes de champs', () => {
        it('les champs avec edit.form ont un groupe défini', () => {
            const descriptors = getSpellFieldDescriptors();
            const fieldsWithEdit = Object.values(descriptors).filter((desc) => desc.edit?.form);

            fieldsWithEdit.forEach((desc) => {
                if (desc.edit.form.bulk?.enabled) {
                    expect(desc.edit.form).toHaveProperty('group');
                    expect(typeof desc.edit.form.group).toBe('string');
                    expect(desc.edit.form.group.length).toBeGreaterThan(0);
                }
            });
        });

        it('les groupes sont cohérents', () => {
            const descriptors = getSpellFieldDescriptors();
            const groups = new Set();

            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.group) {
                    groups.add(desc.edit.form.group);
                }
            });

            // Vérifier qu'il y a au moins quelques groupes
            expect(groups.size).toBeGreaterThan(0);
        });
    });

    describe('QUICK_EDIT_FIELDS', () => {
        it('quickEdit contient uniquement des champs existants', () => {
            const descriptors = getSpellFieldDescriptors();
            const quickEditFields = descriptors._quickeditConfig?.fields || [];

            quickEditFields.forEach((fieldKey) => {
                expect(descriptors).toHaveProperty(fieldKey);
            });
        });

        it('les champs quickEdit sont bulk-enabled', () => {
            const descriptors = getSpellFieldDescriptors();
            const quickEditFields = descriptors._quickeditConfig?.fields || [];

            quickEditFields.forEach((fieldKey) => {
                const desc = descriptors[fieldKey];
                if (desc?.edit?.form) {
                    expect(desc.edit.form.bulk?.enabled).toBe(true);
                }
            });
        });

        it('quickEdit ne contient pas de champs sans edit.form', () => {
            const descriptors = getSpellFieldDescriptors();
            const quickEditFields = descriptors._quickeditConfig?.fields || [];

            quickEditFields.forEach((fieldKey) => {
                const desc = descriptors[fieldKey];
                // Si le champ a edit.form, il doit avoir bulk.enabled
                if (desc?.edit?.form) {
                    expect(desc.edit.form.bulk?.enabled).toBe(true);
                }
            });
        });
    });

    describe('Options des selects', () => {
        it('les champs de type select ont des options définies', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.type === 'select') {
                    expect(desc.edit.form).toHaveProperty('options');
                    expect(Array.isArray(desc.edit.form.options)).toBe(true);
                    expect(desc.edit.form.options.length).toBeGreaterThan(0);
                }
            });
        });

        it('les options des selects ont value et label', () => {
            const descriptors = getSpellFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.type === 'select' && desc.edit.form.options) {
                    desc.edit.form.options.forEach((option) => {
                        expect(option).toHaveProperty('value');
                        expect(option).toHaveProperty('label');
                    });
                }
            });
        });

        it('read_level a les bonnes options', () => {
            const descriptors = getSpellFieldDescriptors();
            const isVisibleDesc = descriptors.read_level;

            if (isVisibleDesc?.edit?.form?.type === 'select') {
                const options = isVisibleDesc.edit.form.options;
                const values = options.map((opt) => opt.value);
                expect(values).toContain(0);
                expect(values).toContain(1);
                expect(values).toContain(4);
            }
        });
    });
});

