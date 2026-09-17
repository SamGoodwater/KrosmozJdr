/**
 * Tests unitaires pour SectionMapper
 * 
 * @description
 * Tests pour le mapper de sections : normalisation, relations, form data, etc.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { SectionMapper } from '@/Utils/Services/Mappers/SectionMapper';
import { Section } from '@/Models';
import { createMockSection } from '../../../setup.js';

describe('SectionMapper', () => {
  describe('mapToModel', () => {
    it('devrait mapper des données brutes en modèle Section', () => {
      const rawData = createMockSection();
      const sectionModel = SectionMapper.mapToModel(rawData);

      expect(sectionModel).toBeInstanceOf(Section);
      expect(sectionModel.id).toBe(1);
      expect(sectionModel.template).toBe('text');
      expect(sectionModel.pageId).toBe(1);
    });

    it('devrait retourner null si rawData est null', () => {
      const sectionModel = SectionMapper.mapToModel(null);
      expect(sectionModel).toBeNull();
    });

    it('devrait retourner la section telle quelle si déjà une instance Section', () => {
      const section = new Section(createMockSection());
      const result = SectionMapper.mapToModel(section);
      expect(result).toBe(section);
    });

    it('devrait gérer les données dans .data (Resource)', () => {
      const rawData = {
        data: createMockSection(),
        can: { update: true }
      };
      const sectionModel = SectionMapper.mapToModel(rawData);

      expect(sectionModel).toBeInstanceOf(Section);
      expect(sectionModel.id).toBe(1);
    });

    it('devrait normaliser les permissions', () => {
      const rawData = createMockSection({
        can: {
          update: 1,
          delete: 0,
          forceDelete: 1,
          restore: 0
        }
      });
      const sectionModel = SectionMapper.mapToModel(rawData);

      expect(sectionModel.canUpdate).toBe(true);
      expect(sectionModel.canDelete).toBe(false);
      expect(sectionModel.canForceDelete).toBe(true);
      expect(sectionModel.canRestore).toBe(false);
    });

    it('devrait gérer la compatibilité avec type et template', () => {
      const rawData = createMockSection({ type: 'image' });
      const sectionModel = SectionMapper.mapToModel(rawData);
      expect(sectionModel.template).toBe('image');
    });

    it('devrait normaliser les relations pivots (users, files)', () => {
      const rawData = createMockSection({
        users: [
          { id: 1, pivot: { role: 'admin' } },
          { id: 2, pivot: { role: 'user' } }
        ],
        files: [
          { id: 1, pivot: { order: 1 } }
        ]
      });
      const sectionModel = SectionMapper.mapToModel(rawData);

      expect(sectionModel._data.users).toHaveLength(2);
      expect(sectionModel._data.users[0]).toHaveProperty('id', 1);
      expect(sectionModel._data.users[0]).toHaveProperty('role', 'admin');
    });
  });

  describe('mapToFormData', () => {
    it('devrait mapper une section en données de formulaire', () => {
      const section = new Section(createMockSection());
      const formData = SectionMapper.mapToFormData(section);

      expect(formData).toHaveProperty('page_id');
      expect(formData).toHaveProperty('title');
      expect(formData).toHaveProperty('slug');
      expect(formData).toHaveProperty('template');
      expect(formData).toHaveProperty('settings');
      expect(formData).toHaveProperty('data');
      expect(formData).toHaveProperty('read_level');
      expect(formData).toHaveProperty('write_level');
      expect(formData).toHaveProperty('state');
    });

    it('devrait mapper des données brutes en form data', () => {
      const rawData = createMockSection();
      const formData = SectionMapper.mapToFormData(rawData);

      expect(formData).toHaveProperty('page_id', 1);
      expect(formData).toHaveProperty('template', 'text');
    });

    it('devrait retourner null si section est null', () => {
      const formData = SectionMapper.mapToFormData(null);
      expect(formData).toBeNull();
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
        data: {},
        read_level: 0,
        write_level: 4,
      };
      const cleaned = SectionMapper.mapFromFormData(formData);

      expect(cleaned).toHaveProperty('title', null);
      expect(cleaned).toHaveProperty('slug', null);
      expect(cleaned).toHaveProperty('order', 0);
      expect(cleaned).toHaveProperty('template', 'text');
      expect(cleaned).not.toHaveProperty('settings');
      expect(cleaned).not.toHaveProperty('data');
    });

    it('devrait garder les settings et data s\'ils ne sont pas vides', () => {
      const formData = {
        title: 'Test',
        settings: { align: 'left' },
        data: { content: 'Test' }
      };
      const cleaned = SectionMapper.mapFromFormData(formData);

      expect(cleaned.settings).toEqual({ align: 'left' });
      expect(cleaned.data).toEqual({ content: 'Test' });
    });

    it('devrait retourner un objet vide si formData est null', () => {
      const cleaned = SectionMapper.mapFromFormData(null);
      expect(cleaned).toEqual({});
    });
  });

  describe('normalizeSectionData', () => {
    it('devrait normaliser toutes les propriétés de base', () => {
      const rawData = createMockSection({
        id: 123,
        page_id: 456,
        title: 'My Section',
        slug: 'my-section',
        order: 5,
        template: 'image',
        settings: { align: 'center' },
        data: { src: 'test.jpg' },
        read_level: 1,
        write_level: 3,
        state: 'draft'
      });
      const normalized = SectionMapper.normalizeSectionData(rawData);

      expect(normalized.id).toBe(123);
      expect(normalized.page_id).toBe(456);
      expect(normalized.title).toBe('My Section');
      expect(normalized.slug).toBe('my-section');
      expect(normalized.order).toBe(5);
      expect(normalized.template).toBe('image');
      expect(normalized.settings).toEqual({ align: 'center' });
      expect(normalized.data).toEqual({ src: 'test.jpg' });
      expect(normalized.read_level).toBe(1);
      expect(normalized.write_level).toBe(3);
      expect(normalized.state).toBe('draft');
    });

    it('devrait utiliser des valeurs par défaut si absentes', () => {
      const rawData = { id: 1, page_id: 1 };
      const normalized = SectionMapper.normalizeSectionData(rawData);

      expect(normalized.template).toBe('text');
      expect(normalized.order).toBe(0);
      expect(normalized.settings).toEqual({});
      expect(normalized.data).toEqual({});
      expect(normalized.read_level).toBe(0);
      expect(normalized.write_level).toBe(4);
      expect(normalized.state).toBe('draft');
    });
  });
});

