/**
 * Tests unitaires pour resource-descriptors (nouveau système)
 *
 * @description
 * Vérifie que :
 * - Les descriptors sont conformes aux règles strictes (pas de build, pas de logique métier)
 * - Les options utilisent des constantes (RarityFormatter, VisibilityFormatter)
 * - Les visibleIf sont pures et reçoivent le contexte
 * - Les descriptors sont déterministes
 */

import { describe, it, expect } from 'vitest';
import { getResourceFieldDescriptors, RESOURCE_QUICK_EDIT_FIELDS } from '@/Entities/resource/resource-descriptors.js';
import { RarityFormatter } from '@/Utils/Formatters/RarityFormatter.js';
import { VisibilityFormatter } from '@/Utils/Formatters/VisibilityFormatter.js';

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

      // Vérifier que tous les champs bulk n'ont pas de build
      for (const [key, descriptor] of Object.entries(descriptors)) {
        if (descriptor.edit?.form?.bulk) {
          expect(descriptor.edit.form.bulk.build).toBeUndefined();
        }
      }
    });

    it('les options utilisent des constantes (RarityFormatter, VisibilityFormatter)', () => {
      const descriptors = getResourceFieldDescriptors();

      // Vérifier rarity
      expect(descriptors.rarity.edit.form.options).toBeDefined();
      expect(Array.isArray(descriptors.rarity.edit.form.options)).toBe(true);
      expect(descriptors.rarity.edit.form.options.length).toBe(RarityFormatter.options.length);

      // Vérifier is_visible
      expect(descriptors.is_visible.edit.form.options).toBeDefined();
      expect(Array.isArray(descriptors.is_visible.edit.form.options)).toBe(true);
      expect(descriptors.is_visible.edit.form.options.length).toBe(VisibilityFormatter.options.length);
    });

    it('les visibleIf sont pures et reçoivent le contexte', () => {
      const descriptors = getResourceFieldDescriptors();

      // Vérifier auto_update
      expect(descriptors.auto_update.visibleIf).toBeDefined();
      expect(typeof descriptors.auto_update.visibleIf).toBe('function');

      // Tester avec contexte
      const ctx1 = { capabilities: { updateAny: true } };
      const ctx2 = { capabilities: { updateAny: false } };
      const ctx3 = { meta: { capabilities: { updateAny: true } } };

      expect(descriptors.auto_update.visibleIf(ctx1)).toBe(true);
      expect(descriptors.auto_update.visibleIf(ctx2)).toBe(false);
      expect(descriptors.auto_update.visibleIf(ctx3)).toBe(true);
    });

    it('les descriptors sont déterministes (même contexte = même résultat)', () => {
      const ctx = {
        capabilities: { updateAny: true, createAny: true },
        resourceTypes: [{ id: 1, name: 'Test' }],
      };

      const descriptors1 = getResourceFieldDescriptors(ctx);
      const descriptors2 = getResourceFieldDescriptors(ctx);

      // Vérifier que les résultats sont identiques
      expect(Object.keys(descriptors1)).toEqual(Object.keys(descriptors2));
      expect(descriptors1.name.key).toBe(descriptors2.name.key);
      expect(descriptors1.rarity.key).toBe(descriptors2.rarity.key);
    });
  });

  describe('RESOURCE_QUICK_EDIT_FIELDS', () => {
    it('définit les champs affichés dans quickEdit', () => {
      expect(Array.isArray(RESOURCE_QUICK_EDIT_FIELDS)).toBe(true);
      expect(RESOURCE_QUICK_EDIT_FIELDS.length).toBeGreaterThan(0);
    });

    it('contient les champs bulk-editables', () => {
      expect(RESOURCE_QUICK_EDIT_FIELDS).toContain('rarity');
      expect(RESOURCE_QUICK_EDIT_FIELDS).toContain('level');
      expect(RESOURCE_QUICK_EDIT_FIELDS).toContain('usable');
      expect(RESOURCE_QUICK_EDIT_FIELDS).toContain('is_visible');
    });

    it('ne contient pas le champ name (non bulk-editable)', () => {
      expect(RESOURCE_QUICK_EDIT_FIELDS).not.toContain('name');
    });
  });

  describe('Conformité aux règles strictes', () => {
    it('aucune logique métier dans les descriptors', () => {
      const descriptors = getResourceFieldDescriptors();

      // Vérifier qu'il n'y a pas de fonctions de calcul ou de transformation
      for (const [key, descriptor] of Object.entries(descriptors)) {
        // Pas de build dans bulk
        if (descriptor.edit?.form?.bulk) {
          expect(descriptor.edit.form.bulk.build).toBeUndefined();
        }

        // Pas de fonctions dans options (sauf pour resource_type_id qui est dynamique)
        if (descriptor.edit?.form?.options && key !== 'resource_type_id') {
          expect(typeof descriptor.edit.form.options).not.toBe('function');
        }
      }
    });

    it('aucune description de vue (Large/Compact/Minimal/Text)', () => {
      const descriptors = getResourceFieldDescriptors();

      // Vérifier qu'il n'y a pas de configuration de vue
      for (const descriptor of Object.values(descriptors)) {
        expect(descriptor.view).toBeUndefined();
        expect(descriptor.views).toBeUndefined();
        expect(descriptor.large).toBeUndefined();
        expect(descriptor.compact).toBeUndefined();
        expect(descriptor.minimal).toBeUndefined();
        expect(descriptor.text).toBeUndefined();
      }
    });

    it('parle le langage du moteur (sortable, filterable, editable)', () => {
      const descriptors = getResourceFieldDescriptors();

      // Les descriptors utilisent des propriétés déclaratives
      expect(descriptors.name.edit).toBeDefined();
      expect(descriptors.rarity.edit).toBeDefined();
      expect(descriptors.level.edit).toBeDefined();
    });
  });

  describe('Gestion du contexte', () => {
    it('extrait correctement les capabilities du contexte', () => {
      const ctx1 = { capabilities: { updateAny: true } };
      const ctx2 = { meta: { capabilities: { updateAny: true } } };
      const ctx3 = { capabilities: { updateAny: false } };

      const descriptors1 = getResourceFieldDescriptors(ctx1);
      const descriptors2 = getResourceFieldDescriptors(ctx2);
      const descriptors3 = getResourceFieldDescriptors(ctx3);

      // Vérifier que visibleIf fonctionne correctement
      expect(descriptors1.auto_update.visibleIf(ctx1)).toBe(true);
      expect(descriptors2.auto_update.visibleIf(ctx2)).toBe(true);
      expect(descriptors3.auto_update.visibleIf(ctx3)).toBe(false);
    });

    it('extrait correctement les resourceTypes du contexte', () => {
      const ctx1 = { resourceTypes: [{ id: 1, name: 'Type 1' }] };
      const ctx2 = { meta: { resourceTypes: [{ id: 2, name: 'Type 2' }] } };

      const descriptors1 = getResourceFieldDescriptors(ctx1);
      const descriptors2 = getResourceFieldDescriptors(ctx2);

      // resource_type_id.options est null dans le descriptor (sera construit dans FormConfig)
      expect(descriptors1.resource_type_id.edit.form.options).toBeNull();
      expect(descriptors2.resource_type_id.edit.form.options).toBeNull();
    });
  });
});
