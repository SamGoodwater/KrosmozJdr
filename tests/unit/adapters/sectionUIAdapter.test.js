/**
 * Tests unitaires pour l'UI de section (useSectionUI)
 */
import { describe, it, expect } from 'vitest';
import { useSectionUI } from '@/Pages/Organismes/section/composables/useSectionUI';
import { createMockSection } from '../../setup.js';

describe('sectionUIAdapter', () => {
  describe('useSectionUI', () => {
    it('devrait adapter une section text avec état playable', () => {
      const section = createMockSection({
        template: 'text',
        state: 'playable',
      });

      const { uiData } = useSectionUI(section);

      expect(uiData.value.color).toBe('success');
      expect(uiData.value.icon).toBe('fa-file-text');
      expect(uiData.value.badge.text).toBe('Jouable');
      expect(uiData.value.badge.color).toBe('success');
      expect(uiData.value.containerClass).toContain('section-state-playable');
      expect(uiData.value.containerClass).toContain('section-template-text');
    });

    it('devrait adapter une section avec état draft', () => {
      const section = createMockSection({
        state: 'draft',
      });

      const { uiData } = useSectionUI(section);

      expect(uiData.value.color).toBe('warning');
      expect(uiData.value.badge.text).toBe('Brouillon');
      expect(uiData.value.badge.color).toBe('warning');
    });

    it('devrait adapter une section image', () => {
      const section = createMockSection({
        template: 'image',
      });

      const { uiData } = useSectionUI(section);

      expect(uiData.value.icon).toBe('fa-image');
      expect(uiData.value.containerClass).toContain('section-template-image');
    });

    it('devrait générer une URL pour la section', () => {
      const section = createMockSection({
        id: 123,
        page: { slug: 'test-page' },
        slug: 'test-section',
      });

      const { uiData } = useSectionUI(section);

      expect(uiData.value.url).toContain('test-page');
      expect(uiData.value.url).toContain('section-123');
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

      const uiDataWithContent = useSectionUI(sectionWithContent).uiData.value;
      const uiDataEmpty = useSectionUI(sectionEmpty).uiData.value;

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

