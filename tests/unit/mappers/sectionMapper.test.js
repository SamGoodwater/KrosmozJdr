/**
 * Tests unitaires pour SectionMapper (nouveau)
 * 
 * @description
 * Tests pour le nouveau SectionMapper qui hérite de BaseMapper.
 * 
 * @deprecated Ce fichier teste l'ancien mapper. Les nouveaux tests sont dans tests/unit/services/Mappers/SectionMapper.test.js
 */
import { describe, it, expect } from 'vitest';
import { SectionMapper } from '@/Utils/Services/Mappers/SectionMapper';
import { createMockSection } from '../../setup.js';

describe('SectionMapper (nouveau)', () => {
  describe('mapToModel', () => {
    it('devrait mapper des données brutes en modèle Section', () => {
      const rawData = createMockSection();
      const sectionModel = SectionMapper.mapToModel(rawData);

      expect(sectionModel).toBeDefined();
      expect(sectionModel.id).toBe(1);
      expect(sectionModel.template).toBe('text');
      expect(sectionModel.pageId).toBe(1);
    });

    it('devrait gérer les données dans .data (Resource)', () => {
      const rawData = {
        data: createMockSection(),
        can: { update: true }
      };
      const sectionModel = SectionMapper.mapToModel(rawData);

      expect(sectionModel).toBeDefined();
      expect(sectionModel.id).toBe(1);
    });

    it('devrait retourner null si les données sont null', () => {
      const sectionModel = SectionMapper.mapToModel(null);
      expect(sectionModel).toBeNull();
    });
  });

  describe('mapToModels', () => {
    it('devrait mapper un tableau de sections', () => {
      const rawSections = [
        createMockSection({ id: 1, template: 'text' }),
        createMockSection({ id: 2, template: 'image' }),
      ];

      const sectionModels = SectionMapper.mapToModels(rawSections);

      expect(sectionModels).toHaveLength(2);
      expect(sectionModels[0].id).toBe(1);
      expect(sectionModels[1].id).toBe(2);
    });

    it('devrait retourner un tableau vide si le paramètre n\'est pas un tableau', () => {
      const sectionModels = SectionMapper.mapToModels(null);
      expect(sectionModels).toEqual([]);
    });
  });

  describe('mapToFormData', () => {
    it('devrait mapper une section en données de formulaire', () => {
      const section = createMockSection();
      const formData = SectionMapper.mapToFormData(section);

      expect(formData).toHaveProperty('page_id', 1);
      expect(formData).toHaveProperty('title', 'Test Section');
      expect(formData).toHaveProperty('slug', 'test-section');
      expect(formData).toHaveProperty('template', 'text');
      expect(formData).toHaveProperty('settings');
      expect(formData).toHaveProperty('data');
      expect(formData).toHaveProperty('is_visible', 'guest');
      expect(formData).toHaveProperty('can_edit_role', 'admin');
      expect(formData).toHaveProperty('state', 'published');
    });
  });

  describe('mapFromFormData', () => {
    it('devrait nettoyer les valeurs vides', () => {
      const formData = {
        title: '',
        slug: null,
        order: 0,
        template: 'text',
        settings: {},
        data: {}
      };
      const cleaned = SectionMapper.mapFromFormData(formData);

      expect(cleaned).toHaveProperty('title', null);
      expect(cleaned).toHaveProperty('slug', null);
      expect(cleaned).not.toHaveProperty('settings');
      expect(cleaned).not.toHaveProperty('data');
    });
  });
});

