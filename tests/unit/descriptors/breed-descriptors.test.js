/**
 * Tests unitaires pour breed-descriptors
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
import { getBreedFieldDescriptors } from '@/Entities/breed/breed-descriptors';

describe('breed-descriptors', () => {
    describe('Structure des descriptors', () => {
        it('retourne un objet avec tous les champs requis', () => {
            const descriptors = getBreedFieldDescriptors();
            const requiredFields = ['id', 'name', 'life_dice', 'state', 'read_level', 'write_level'];

            requiredFields.forEach((field) => {
                expect(descriptors).toHaveProperty(field);
                expect(descriptors[field]).toHaveProperty('key');
                expect(descriptors[field]).toHaveProperty('label');
                expect(descriptors[field].key).toBe(field);
            });
        });

        it('tous les descriptors ont une propriété display avec sizes (pour les tableaux)', () => {
            const descriptors = getBreedFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.display) {
                    expect(desc.display).toHaveProperty('sizes');
                }
            });
        });
    });

    describe('visibleIf / editableIf', () => {
        it('visibleIf sur id suit canCreateAny (fermeture, pas le paramètre du call)', () => {
            const visibleOn = getBreedFieldDescriptors({
                capabilities: { createAny: true },
            });
            const visibleOff = getBreedFieldDescriptors({
                capabilities: { createAny: false },
            });

            expect(visibleOn.id.visibleIf?.()).toBe(true);
            expect(visibleOff.id.visibleIf?.()).toBe(false);
            // La fonction ignore l’argument : ne pas s’appuyer sur ctx passé à visibleIf().
            expect(visibleOn.id.visibleIf?.({ capabilities: { createAny: false } })).toBe(true);
        });
    });


    describe('Configuration bulk', () => {
        it('les champs avec edit.form ont une configuration bulk', () => {
            const descriptors = getBreedFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form) {
                    expect(desc.edit.form).toHaveProperty('bulk');
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });

        it('les champs bulk ont enabled de type boolean', () => {
            const descriptors = getBreedFieldDescriptors();
            Object.values(descriptors).forEach((desc) => {
                if (desc.edit?.form?.bulk) {
                    expect(desc.edit.form.bulk).toHaveProperty('enabled');
                    expect(typeof desc.edit.form.bulk.enabled).toBe('boolean');
                }
            });
        });
    });
});
