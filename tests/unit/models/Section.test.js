/**
 * Tests unitaires pour le modèle Section
 */
import { describe, it, expect } from 'vitest';
import { Section } from '@/Models';
import { createMockSection } from '../../setup.js';

describe('Section Model', () => {
  describe('Constructor', () => {
    it('devrait créer une instance Section à partir de données brutes', () => {
      const rawData = createMockSection();
      const section = new Section(rawData);

      expect(section).toBeInstanceOf(Section);
      expect(section.id).toBe(1);
      expect(section.template).toBe('text');
    });

    it('devrait gérer les données dans .data (Resource)', () => {
      const rawData = {
        data: createMockSection(),
      };
      const section = new Section(rawData);

      expect(section.id).toBe(1);
    });
  });

  describe('Propriétés', () => {
    it('devrait exposer toutes les propriétés correctement', () => {
      const rawData = createMockSection({
        id: 123,
        title: 'My Section',
        template: 'image',
        state: 'published',
      });
      const section = new Section(rawData);

      expect(section.id).toBe(123);
      expect(section.title).toBe('My Section');
      expect(section.template).toBe('image');
      expect(section.state).toBe('published');
      expect(section.pageId).toBe(1);
    });

    it('devrait gérer les permissions', () => {
      const rawData = createMockSection({
        can: {
          update: true,
          delete: false,
        },
      });
      const section = new Section(rawData);

      expect(section.canUpdate).toBe(true);
      expect(section.canDelete).toBe(false);
    });
  });

  describe('Méthodes', () => {
    it('devrait convertir en données de formulaire', () => {
      const rawData = createMockSection();
      const section = new Section(rawData);
      const formData = section.toFormData();

      expect(formData).toHaveProperty('page_id');
      expect(formData).toHaveProperty('template');
      expect(formData).toHaveProperty('settings');
      expect(formData).toHaveProperty('data');
    });

    it('devrait vérifier si la section est publiée', () => {
      const publishedSection = new Section(createMockSection({ state: 'published' }));
      const draftSection = new Section(createMockSection({ state: 'draft' }));

      expect(publishedSection.isPublished).toBe(true);
      expect(draftSection.isPublished).toBe(false);
    });

    it('devrait vérifier si la section est visible', () => {
      const visibleSection = new Section(createMockSection({ is_visible: 'guest' }));
      const hiddenSection = new Section(createMockSection({ is_visible: 'admin' }));

      expect(visibleSection.isVisible).toBe('guest');
      expect(hiddenSection.isVisible).toBe('admin');
    });
  });
});

