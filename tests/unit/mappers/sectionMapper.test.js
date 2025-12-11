/**
 * Tests unitaires pour sectionMapper
 */
import { describe, it, expect } from 'vitest';
import { mapToSectionModel, mapToSectionModels, mapToFormData } from '@/Pages/Organismes/section/mappers/sectionMapper';
import { createMockSection } from '../../setup.js';

describe('sectionMapper', () => {
  describe('mapToSectionModel', () => {
    it('devrait mapper des données brutes en modèle Section', () => {
      const rawData = createMockSection();
      const sectionModel = mapToSectionModel(rawData);

      expect(sectionModel).toBeDefined();
      expect(sectionModel.id).toBe(1);
      expect(sectionModel.template).toBe('text');
      expect(sectionModel.pageId).toBe(1);
    });

    it('devrait gérer les données dans .data (Resource)', () => {
      const rawData = {
        data: createMockSection(),
      };
      const sectionModel = mapToSectionModel(rawData);

      expect(sectionModel).toBeDefined();
      expect(sectionModel.id).toBe(1);
    });

    it('devrait retourner null si les données sont null', () => {
      const sectionModel = mapToSectionModel(null);
      expect(sectionModel).toBeNull();
    });
  });

  describe('mapToSectionModels', () => {
    it('devrait mapper un tableau de sections', () => {
      const rawSections = [
        createMockSection({ id: 1, template: 'text' }),
        createMockSection({ id: 2, template: 'image' }),
      ];

      const sectionModels = mapToSectionModels(rawSections);

      expect(sectionModels).toHaveLength(2);
      expect(sectionModels[0].id).toBe(1);
      expect(sectionModels[1].id).toBe(2);
    });

    it('devrait retourner un tableau vide si le paramètre n\'est pas un tableau', () => {
      const sectionModels = mapToSectionModels(null);
      expect(sectionModels).toEqual([]);
    });
  });

  describe('mapToFormData', () => {
    it('devrait mapper une section en données de formulaire', () => {
      const section = createMockSection();
      const formData = mapToFormData(section);

      expect(formData).toEqual({
        page_id: 1,
        title: 'Test Section',
        slug: 'test-section',
        order: 0,
        template: 'text',
        settings: { align: 'left', size: 'md' },
        data: { content: 'Test content' },
        is_visible: 'guest',
        can_edit_role: 'admin',
        state: 'published',
      });
    });
  });
});

