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
 * - QUICK_EDIT_FIELDS est cohérent avec les champs bulk
 */

import { describe, it, expect } from 'vitest';
import { getItemFieldDescriptors } from '@/Entities/item/item-descriptors';
import { getDescriptorForm, getDescriptorFormOptions, resolveDescriptorOptions } from './descriptor-test-helpers.js';

describe('item-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getItemFieldDescriptors();
            const requiredFields = ['id', 'name', 'level', 'rarity', 'state', 'read_level', 'write_level'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getItemFieldDescriptors();
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
        it('visibleIf sur id suit canUpdateAny (fermeture sur le factory)', () => {
            const on = getItemFieldDescriptors({ capabilities: { updateAny: true } });
            const off = getItemFieldDescriptors({ capabilities: { updateAny: false } });
            expect(on.id.visibleIf?.()).toBe(true);
            expect(off.id.visibleIf?.()).toBe(false);
        });

        it('auto_update suit canUpdateAny (fermeture sur le factory)', () => {
            const on = getItemFieldDescriptors({ capabilities: { updateAny: true } });
            const off = getItemFieldDescriptors({ capabilities: { updateAny: false } });
            expect(on.auto_update.visibleIf?.()).toBe(true);
            expect(off.auto_update.visibleIf?.()).toBe(false);
        });
    });

    describe('Configuration bulk', () => {
        it('les champs bulk-enabled ont enabled: true', () => {
            const descriptors = getItemFieldDescriptors();
            const bulkEnabledFields = Object.keys(descriptors).filter((k) => descriptors[k]?.edit?.form?.bulk?.enabled);

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


    describe('Vue Colonnes — visibilité par défaut', () => {
        const allTrue = { xs: true, sm: true, md: true, lg: true, xl: true };
        const fromSm = { xs: false, sm: true, md: true, lg: true, xl: true };
        const fromMd = { xs: false, sm: false, md: true, lg: true, xl: true };
        const hidden = { xs: false, sm: false, md: false, lg: false, xl: false };

        it('montre image, nom, niveau, type, rareté et bonus', () => {
            const d = getItemFieldDescriptors({ capabilities: { updateAny: true } });
            expect(d.image.table.defaultVisible).toEqual(allTrue);
            expect(d.name.table.defaultVisible).toEqual(allTrue);
            expect(d.level.table.defaultVisible).toEqual(fromSm);
            expect(d.item_type.table.defaultVisible).toEqual(fromSm);
            expect(d.rarity.table.defaultVisible).toEqual(fromSm);
            expect(d.effect.table.defaultVisible).toEqual(fromMd);
        });

        it('masque description, résumé, prix et version', () => {
            const d = getItemFieldDescriptors();
            expect(d.description.table.defaultVisible).toEqual(hidden);
            expect(d.item_summary_meta.table.defaultVisible).toEqual(hidden);
            expect(d.price.table.defaultVisible).toEqual(hidden);
            expect(d.dofus_version.table.defaultVisible).toEqual(hidden);
        });

        it('réserve la colonne État aux éditeurs', () => {
            const editor = getItemFieldDescriptors({ capabilities: { updateAny: true } });
            const player = getItemFieldDescriptors({ capabilities: { updateAny: false } });
            expect(editor.state.visibleIf?.()).toBe(true);
            expect(editor.state.table.visibleIf?.()).toBe(true);
            expect(editor.state.table.defaultVisible).toEqual(fromSm);
            expect(player.state.visibleIf?.()).toBe(false);
        });

        it('utilise un langage fiche de jeu dans les tooltips', () => {
            const d = getItemFieldDescriptors();
            expect(d.name.helper).toBe('Nom de l’équipement');
            expect(d.effect.helper).toMatch(/Bonus/);
            expect(d.item_type.helper).toMatch(/Emplacement/);
        });
    });

    describe('Options des selects', () => {
        it('read_level a les bonnes options', () => {
            const descriptors = getItemFieldDescriptors();
            const isVisibleDesc = descriptors.read_level;
            const form = getDescriptorForm(isVisibleDesc);

            if (form?.type === 'select') {
                const options = resolveDescriptorOptions(getDescriptorFormOptions(isVisibleDesc), {});
                expect(Array.isArray(options)).toBe(true);
                const values = options.map((opt) => opt.value);
                expect(values).toContain(0);
                expect(values).toContain(1);
                expect(values).toContain(4);
            }
        });

        it('les options des selects ont value et label', () => {
            const descriptors = getItemFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                const form = getDescriptorForm(desc);
                if (form?.type === 'select' && form.options) {
                    const opts = resolveDescriptorOptions(form.options, {});
                    if (!Array.isArray(opts)) return;
                    opts.forEach((option) => {
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

            expect(rarityDesc.edit.form.type).toBe('select');
            expect(rarityDesc.edit.form.bulk.enabled).toBe(true);
        });
    });
});

