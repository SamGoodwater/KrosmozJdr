/**
 * Tests unitaires pour PageMapper
 * 
 * @description
 * Tests pour le mapper de pages : normalisation, relations, form data, etc.
 */
import { describe, it, expect } from 'vitest';
import { PageMapper } from '@/Utils/Services/Mappers/PageMapper';
import { Page } from '@/Models';
import { createMockPage } from '../../../setup.js';

describe('PageMapper', () => {
  describe('mapToModel', () => {
    it('devrait mapper des données brutes en modèle Page', () => {
      const rawData = createMockPage();
      const pageModel = PageMapper.mapToModel(rawData);

      expect(pageModel).toBeInstanceOf(Page);
      expect(pageModel.id).toBe(1);
      expect(pageModel.title).toBe('Test Page');
      expect(pageModel.slug).toBe('test-page');
    });

    it('devrait retourner null si rawData est null', () => {
      const pageModel = PageMapper.mapToModel(null);
      expect(pageModel).toBeNull();
    });

    it('devrait retourner la page telle quelle si déjà une instance Page', () => {
      const page = new Page(createMockPage());
      const result = PageMapper.mapToModel(page);
      expect(result).toBe(page);
    });

    it('devrait gérer les données dans .data (Resource)', () => {
      const rawData = {
        data: createMockPage(),
        can: { update: true }
      };
      const pageModel = PageMapper.mapToModel(rawData);

      expect(pageModel).toBeInstanceOf(Page);
      expect(pageModel.id).toBe(1);
    });

    it('devrait normaliser les permissions', () => {
      const rawData = createMockPage({
        can: {
          update: 1,
          delete: 0
        }
      });
      const pageModel = PageMapper.mapToModel(rawData);

      expect(pageModel.canUpdate).toBe(true);
      expect(pageModel.canDelete).toBe(false);
    });
  });

  describe('mapToFormData', () => {
    it('devrait mapper une page en données de formulaire', () => {
      const page = new Page(createMockPage());
      const formData = PageMapper.mapToFormData(page);

      expect(formData).toHaveProperty('title');
      expect(formData).toHaveProperty('slug');
      expect(formData).toHaveProperty('is_visible');
      expect(formData).toHaveProperty('can_edit_role');
      expect(formData).toHaveProperty('in_menu');
      expect(formData).toHaveProperty('state');
      expect(formData).toHaveProperty('parent_id');
      expect(formData).toHaveProperty('menu_order');
    });

    it('devrait mapper des données brutes en form data', () => {
      const rawData = createMockPage();
      const formData = PageMapper.mapToFormData(rawData);

      expect(formData).toHaveProperty('title', 'Test Page');
      expect(formData).toHaveProperty('slug', 'test-page');
    });

    it('devrait retourner null si page est null', () => {
      const formData = PageMapper.mapToFormData(null);
      expect(formData).toBeNull();
    });
  });

  describe('mapFromFormData', () => {
    it('devrait nettoyer les valeurs vides', () => {
      const formData = {
        title: '',
        slug: null,
        is_visible: 'guest',
        can_edit_role: 'admin',
        in_menu: true,
        state: 'draft',
        parent_id: null,
        menu_order: 0
      };
      const cleaned = PageMapper.mapFromFormData(formData);

      expect(cleaned).toHaveProperty('title', null);
      expect(cleaned).toHaveProperty('slug', null);
      expect(cleaned).toHaveProperty('is_visible', 'guest');
    });

    it('devrait retourner un objet vide si formData est null', () => {
      const cleaned = PageMapper.mapFromFormData(null);
      expect(cleaned).toEqual({});
    });
  });
});

