/**
 * Tests unitaires pour sectionUIAdapter
 */
import { describe, it, expect } from 'vitest';
import { adaptSectionToUI } from '@/Pages/Organismes/section/adapters/sectionUIAdapter';
import { createMockSection } from '../../setup.js';

describe('sectionUIAdapter', () => {
  describe('adaptSectionToUI', () => {
    it('devrait adapter une section text avec état published', () => {
      const section = createMockSection({
        template: 'text',
        state: 'published',
      });

      const uiData = adaptSectionToUI(section);

      expect(uiData.color).toBe('success');
      expect(uiData.icon).toBe('fa-file-text');
      expect(uiData.badge.text).toBe('Publié');
      expect(uiData.badge.color).toBe('success');
      expect(uiData.containerClass).toContain('section-state-published');
      expect(uiData.containerClass).toContain('section-template-text');
    });

    it('devrait adapter une section avec état draft', () => {
      const section = createMockSection({
        state: 'draft',
      });

      const uiData = adaptSectionToUI(section);

      expect(uiData.color).toBe('warning');
      expect(uiData.badge.text).toBe('Brouillon');
      expect(uiData.badge.color).toBe('warning');
    });

    it('devrait adapter une section image', () => {
      const section = createMockSection({
        template: 'image',
      });

      const uiData = adaptSectionToUI(section);

      expect(uiData.icon).toBe('fa-image');
      expect(uiData.containerClass).toContain('section-template-image');
    });

    it('devrait générer une URL pour la section', () => {
      const section = createMockSection({
        id: 123,
        page: { slug: 'test-page' },
        slug: 'test-section',
      });

      const uiData = adaptSectionToUI(section);

      expect(uiData.url).toContain('test-page');
      expect(uiData.url).toContain('section-123');
    });

    it('devrait détecter si une section a du contenu', () => {
      const sectionWithContent = createMockSection({
        template: 'text',
        data: { content: 'Hello world' },
      });

      const sectionEmpty = createMockSection({
        template: 'text',
        data: { content: '' },
      });

      const uiDataWithContent = adaptSectionToUI(sectionWithContent);
      const uiDataEmpty = adaptSectionToUI(sectionEmpty);

      expect(uiDataWithContent.metadata.hasContent).toBe(true);
      expect(uiDataEmpty.metadata.hasContent).toBe(false);
      expect(uiDataEmpty.metadata.isEmpty).toBe(true);
    });

    it('devrait retourner les métadonnées correctes', () => {
      const section = createMockSection({
        order: 5,
        created_at: '2025-01-01T00:00:00.000000Z',
        updated_at: '2025-01-02T00:00:00.000000Z',
      });

      const uiData = adaptSectionToUI(section);

      expect(uiData.metadata.order).toBe(5);
      expect(uiData.metadata.createdAt).toBe('2025-01-01T00:00:00.000000Z');
      expect(uiData.metadata.updatedAt).toBe('2025-01-02T00:00:00.000000Z');
    });
  });
});

