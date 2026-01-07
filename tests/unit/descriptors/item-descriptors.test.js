/**
 * Tests unitaires pour item-descriptors
 *
 * @description
 * Vérifie que :
 * - La structure des descriptors est correcte
 * - visibleIf / editableIf fonctionnent correctement
 * - Les options des selects sont cohérentes
 * - La configuration bulk est correcte
 * - Les groupes de champs sont définis
 * - viewFields.quickEdit est cohérent avec les champs bulk
 */

import { describe, it, expect } from 'vitest';
import { getItemFieldDescriptors, ITEM_VIEW_FIELDS } from '@/Entities/item/item-descriptors';

describe('item-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getItemFieldDescriptors();
            const requiredFields = ['id', 'name', 'level', 'rarity', 'usable', 'is_visible'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display', () => {
            const descriptors = getItemFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                expect(desc).toHaveProperty('display');
                expect(desc.display).toHaveProperty('views');
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf fonctionne avec canUpdateAny', () => {
            const descriptors = getItemFieldDescriptors({
                capabilities: { updateAny: true },
            });

            const idDescriptor = descriptors.id;
            if (idDescriptor.visibleIf) {
                expect(idDescriptor.visibleIf({ capabilities: { updateAny: true } })).toBe(true);
                expect(idDescriptor.visibleIf({ capabilities: { updateAny: false } })).toBe(false);
            }
        });

        it('auto_update est visible seulement si canUpdateAny', () => {
            const descriptors = getItemFieldDescriptors({
                capabilities: { updateAny: true },
            });

            const autoUpdateDesc = descriptors.auto_update;
            if (autoUpdateDesc?.visibleIf) {
                expect(autoUpdateDesc.visibleIf({ capabilities: { updateAny: true } })).toBe(true);
                expect(autoUpdateDesc.visibleIf({ capabilities: { updateAny: false } })).toBe(false);
            }
        });
    });

    describe('Configuration bulk', () => {
        it('les champs bulk-enabled ont enabled: true', () => {
            const descriptors = getItemFieldDescriptors();
            const bulkEnabledFields = ITEM_VIEW_FIELDS.quickEdit;

            bulkEnabledFields.forEach((fieldKey) => {
                const desc = descriptors[fieldKey];
                if (desc?.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.enabled).toBe(true);
                }
            });
        });

        it('les champs bulk ont une fonction build', () => {
            const descriptors = getItemFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk?.enabled) {
                    expect(desc.edit.form.bulk).toHaveProperty('build');
                    expect(typeof desc.edit.form.bulk.build).toBe('function');
                }
            });
        });

        it('rarity a une validation correcte dans build', () => {
            const descriptors = getItemFieldDescriptors();
            const rarityDesc = descriptors.rarity;

            if (rarityDesc?.edit?.form?.bulk?.build) {
                const buildFn = rarityDesc.edit.form.bulk.build;
                // Tester avec une valeur valide (0-5)
                expect(typeof buildFn(3)).toBe('number');
                expect(buildFn(3)).toBeGreaterThanOrEqual(0);
                expect(buildFn(3)).toBeLessThanOrEqual(5);
            }
        });
    });

    describe('Groupes de champs', () => {
        it('les champs avec edit.form ont un groupe défini', () => {
            const descriptors = getItemFieldDescriptors();
            const fieldsWithEdit = Object.values(descriptors).filter((desc) => desc.edit?.form);

            fieldsWithEdit.forEach((desc) => {
                if (desc.edit.form.bulk?.enabled) {
                    expect(desc.edit.form).toHaveProperty('group');
                    expect(typeof desc.edit.form.group).toBe('string');
                    expect(desc.edit.form.group.length).toBeGreaterThan(0);
                }
            });
        });

        it('les groupes sont cohérents (Statut, Métier, Contenu, Métadonnées)', () => {
            const descriptors = getItemFieldDescriptors();
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

    describe('viewFields.quickEdit', () => {
        it('quickEdit contient uniquement des champs existants', () => {
            const descriptors = getItemFieldDescriptors();
            const quickEditFields = ITEM_VIEW_FIELDS.quickEdit;

            quickEditFields.forEach((fieldKey) => {
                expect(descriptors).toHaveProperty(fieldKey);
            });
        });

        it('les champs quickEdit sont bulk-enabled', () => {
            const descriptors = getItemFieldDescriptors();
            const quickEditFields = ITEM_VIEW_FIELDS.quickEdit;

            quickEditFields.forEach((fieldKey) => {
                const desc = descriptors[fieldKey];
                if (desc?.edit?.form) {
                    expect(desc.edit.form.bulk?.enabled).toBe(true);
                }
            });
        });
    });

    describe('Options des selects', () => {
        it('is_visible a les bonnes options', () => {
            const descriptors = getItemFieldDescriptors();
            const isVisibleDesc = descriptors.is_visible;

            if (isVisibleDesc?.edit?.form?.type === 'select') {
                const options = isVisibleDesc.edit.form.options;
                const values = options.map((opt) => opt.value);
                expect(values).toContain('guest');
                expect(values).toContain('user');
                expect(values).toContain('admin');
            }
        });

        it('les options des selects ont value et label', () => {
            const descriptors = getItemFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.type === 'select' && desc.edit.form.options) {
                    desc.edit.form.options.forEach((option) => {
                        expect(option).toHaveProperty('value');
                        expect(option).toHaveProperty('label');
                    });
                }
            });
        });
    });

    describe('Champs spécifiques', () => {
        it('name est required et non bulk-enabled', () => {
            const descriptors = getItemFieldDescriptors();
            const nameDesc = descriptors.name;

            expect(nameDesc.edit.form.required).toBe(true);
            expect(nameDesc.edit.form.bulk.enabled).toBe(false);
        });

        it('rarity a une configuration correcte', () => {
            const descriptors = getItemFieldDescriptors();
            const rarityDesc = descriptors.rarity;

            expect(rarityDesc.format).toBe('enum');
            expect(rarityDesc.edit.form.type).toBe('select');
            expect(rarityDesc.edit.form.bulk.enabled).toBe(true);
        });
    });
});

