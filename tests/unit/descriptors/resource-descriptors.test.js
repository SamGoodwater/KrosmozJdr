/**
 * Tests unitaires pour resource-descriptors (schéma `edition` / `permissions`)
 *
 * @description
 * Vérifie structure, bulk sans build, options, et visibilités alignées sur le code réel.
 */

import { describe, it, expect } from 'vitest';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors.js';
import { RarityFormatter } from '@/Utils/Formatters/RarityFormatter.js';
import {
    getDescriptorForm,
    getDescriptorFormOptions,
    resolveDescriptorOptions,
} from './descriptor-test-helpers.js';

describe('resource-descriptors (nouveau système)', () => {
    describe('Structure et conformité', () => {
        it('retourne les descriptors pour tous les champs', () => {
            const descriptors = getResourceFieldDescriptors();

            expect(descriptors).toBeDefined();
            expect(typeof descriptors).toBe('object');
            expect(descriptors.name).toBeDefined();
            expect(descriptors.name.key).toBe('name');
            expect(descriptors.rarity).toBeDefined();
            expect(descriptors.rarity.key).toBe('rarity');
        });

        it('les descriptors n\'ont pas de fonctions build dans bulk', () => {
            const descriptors = getResourceFieldDescriptors();

            for (const [, descriptor] of Object.entries(descriptors)) {
                const bulk = descriptor.edition?.bulk;
                if (bulk) {
                    expect(bulk.build).toBeUndefined();
                }
            }
        });

        it('pré-coche les types métier sur le filtre Type', () => {
            const descriptors = getResourceFieldDescriptors();
            const filterable = descriptors.resource_type.table.filterable;
            expect(filterable.defaultByLabel).toContain('Bois');
            expect(filterable.defaultByLabel).not.toContain('Souvenir');
            expect(filterable.defaultByDofusTypeId).toContain(38);
        });

        it('les options utilisent des constantes (RarityFormatter, rôles lecture/écriture)', () => {
            const descriptors = getResourceFieldDescriptors();

            const rarityOpts = getDescriptorFormOptions(descriptors.rarity);
            expect(Array.isArray(rarityOpts)).toBe(true);
            expect(rarityOpts.length).toBe(RarityFormatter.options.length);

            const readOpts = getDescriptorFormOptions(descriptors.read_level);
            expect(Array.isArray(readOpts)).toBe(true);
            const writeOpts = getDescriptorFormOptions(descriptors.write_level);
            expect(Array.isArray(writeOpts)).toBe(true);
        });

        it('permissions.visibleIf sur auto_update lit le contexte', () => {
            const descriptors = getResourceFieldDescriptors();
            expect(descriptors.auto_update.permissions?.visibleIf).toBeDefined();
            const fn = descriptors.auto_update.permissions.visibleIf;
            expect(fn({ capabilities: { updateAny: true } })).toBe(true);
            expect(fn({ capabilities: { updateAny: false } })).toBe(false);
            expect(fn({ meta: { capabilities: { updateAny: true } } })).toBe(true);
        });

        it('les descriptors sont déterministes (même contexte = même résultat)', () => {
            const ctx = {
                capabilities: { updateAny: true, createAny: true },
                resourceTypes: [{ id: 1, name: 'Test' }],
            };

            const descriptors1 = getResourceFieldDescriptors(ctx);
            const descriptors2 = getResourceFieldDescriptors(ctx);

            expect(Object.keys(descriptors1)).toEqual(Object.keys(descriptors2));
            expect(descriptors1.name.key).toBe(descriptors2.name.key);
            expect(descriptors1.rarity.key).toBe(descriptors2.rarity.key);
        });
    });


    describe('Conformité aux règles strictes', () => {
        it('aucune logique métier dans les descriptors (pas de build bulk)', () => {
            const descriptors = getResourceFieldDescriptors();

            for (const [, descriptor] of Object.entries(descriptors)) {
                if (descriptor.edition?.bulk) {
                    expect(descriptor.edition.bulk.build).toBeUndefined();
                }
            }
        });

        it('aucune description de vue (Large/Compact/Minimal/Text)', () => {
            const descriptors = getResourceFieldDescriptors();

            for (const descriptor of Object.values(descriptors)) {
                expect(descriptor.view).toBeUndefined();
                expect(descriptor.views).toBeUndefined();
                expect(descriptor.large).toBeUndefined();
                expect(descriptor.compact).toBeUndefined();
                expect(descriptor.minimal).toBeUndefined();
                expect(descriptor.text).toBeUndefined();
            }
        });

        it('parle le langage du moteur (edition sur les champs éditables clés)', () => {
            const descriptors = getResourceFieldDescriptors();
            expect(descriptors.name.edition).toBeDefined();
            expect(descriptors.rarity.edition).toBeDefined();
            expect(descriptors.level.edition).toBeDefined();
        });
    });

    describe('Gestion du contexte', () => {
        it('extrait correctement les capabilities du contexte (auto_update)', () => {
            const ctx1 = { capabilities: { updateAny: true } };
            const ctx2 = { meta: { capabilities: { updateAny: true } } };
            const ctx3 = { capabilities: { updateAny: false } };

            const descriptors1 = getResourceFieldDescriptors(ctx1);
            const descriptors2 = getResourceFieldDescriptors(ctx2);
            const descriptors3 = getResourceFieldDescriptors(ctx3);

            const v1 = descriptors1.auto_update.permissions.visibleIf;
            const v2 = descriptors2.auto_update.permissions.visibleIf;
            const v3 = descriptors3.auto_update.permissions.visibleIf;
            expect(v1(ctx1)).toBe(true);
            expect(v2(ctx2)).toBe(true);
            expect(v3(ctx3)).toBe(false);
        });

        it('resource_type_id.options reflète resourceTypes du contexte', () => {
            const ctx1 = { resourceTypes: [{ id: 1, name: 'Type 1' }] };
            const ctx2 = { meta: { resourceTypes: [{ id: 2, name: 'Type 2' }] } };

            const descriptors1 = getResourceFieldDescriptors(ctx1);
            const descriptors2 = getResourceFieldDescriptors(ctx2);

            const opts1 = resolveDescriptorOptions(getDescriptorFormOptions(descriptors1.resource_type_id), {});
            const opts2 = resolveDescriptorOptions(getDescriptorFormOptions(descriptors2.resource_type_id), {});

            expect(opts1.some((o) => o.value === 1 && o.label === 'Type 1')).toBe(true);
            expect(opts2.some((o) => o.value === 2 && o.label === 'Type 2')).toBe(true);
        });
    });
});
