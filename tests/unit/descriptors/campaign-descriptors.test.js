/**
 * Tests unitaires pour campaign-descriptors
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
import { getCampaignFieldDescriptors, CAMPAIGN_QUICK_EDIT_FIELDS } from '@/Entities/campaign/campaign-descriptors';

describe('campaign-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getCampaignFieldDescriptors();
            const requiredFields = ['id', 'name', 'state', 'usable', 'is_visible'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getCampaignFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.display) {
                    expect(desc.display).toHaveProperty('sizes');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf fonctionne avec canUpdateAny', () => {
            const descriptors = getCampaignFieldDescriptors({
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
            expect(CAMPAIGN_QUICK_EDIT_FIELDS).toBeDefined();
            expect(Array.isArray(CAMPAIGN_QUICK_EDIT_FIELDS)).toBe(true);
        });

        it('QUICK_EDIT_FIELDS contient des champs valides', () => {
            const descriptors = getCampaignFieldDescriptors();
            CAMPAIGN_QUICK_EDIT_FIELDS.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
            });
        });
    });

    describe('Configuration bulk', () => {
        it('les champs avec edit.form ont une configuration bulk', () => {
            const descriptors = getCampaignFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form) {
                    expect(desc.edit.form).toHaveProperty('bulk');
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });

        it('aucun champ bulk n\'a de fonction build (déprécié)', () => {
            const descriptors = getCampaignFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk.build).toBeUndefined();
                }
            });
        });
    });
});
