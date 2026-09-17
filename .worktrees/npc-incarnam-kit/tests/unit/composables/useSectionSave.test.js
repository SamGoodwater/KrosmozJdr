/**
 * Tests unitaires pour useSectionSave
 */
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { useSectionSave } from '@/Pages/Organismes/section/composables/useSectionSave';

const { mockUpdateSection } = vi.hoisted(() => {
  const mockUpdateSection = vi.fn((id, updates, options) => {
    if (options?.onSuccess) {
      options.onSuccess({ props: { page: { sections: [] } } });
    }
    return Promise.resolve();
  });
  return { mockUpdateSection };
});

// Mock useSectionAPI — une seule implémentation partagée (useSectionSave appelle useSectionAPI au chargement du module)
vi.mock('@/Pages/Organismes/section/composables/useSectionAPI', () => ({
  useSectionAPI: () => ({
    updateSection: mockUpdateSection,
  }),
}));

describe('useSectionSave', () => {
  beforeEach(() => {
    vi.useFakeTimers();
    mockUpdateSection.mockClear();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  describe('saveSection', () => {
    it('devrait sauvegarder une section avec debounce', async () => {
      const { saveSection } = useSectionSave();
      const updates = { data: { content: 'Test content' } };

      saveSection(1, updates);

      // Avancer le temps de 500ms (debounce par défaut)
      vi.advanceTimersByTime(500);

      // Attendre que la promesse soit résolue
      await vi.runAllTimersAsync();

      expect(mockUpdateSection).toHaveBeenCalledWith(1, updates, expect.any(Object));
    });

    it('devrait utiliser un debounce personnalisé', async () => {
      const { saveSection } = useSectionSave();
      const updates = { data: { content: 'Test content' } };

      saveSection(1, updates, 1000);

      // Avancer le temps de 500ms (pas assez)
      vi.advanceTimersByTime(500);

      expect(mockUpdateSection).not.toHaveBeenCalled();

      // Avancer le temps de 500ms supplémentaires
      vi.advanceTimersByTime(500);
      await vi.runAllTimersAsync();

      expect(mockUpdateSection).toHaveBeenCalled();
    });

    it('devrait annuler la sauvegarde précédente si une nouvelle est déclenchée', async () => {
      const { saveSection } = useSectionSave();

      saveSection(1, { data: { content: 'First' } });
      saveSection(1, { data: { content: 'Second' } });

      vi.advanceTimersByTime(500);
      await vi.runAllTimersAsync();

      // Seule la dernière sauvegarde devrait être appelée
      expect(mockUpdateSection).toHaveBeenCalledTimes(1);
      expect(mockUpdateSection).toHaveBeenCalledWith(1, { data: { content: 'Second' } }, expect.any(Object));
    });
  });

  describe('saveSectionImmediate', () => {
    it('devrait sauvegarder immédiatement sans debounce', async () => {
      const { saveSectionImmediate } = useSectionSave();
      const updates = { data: { content: 'Test content' } };

      saveSectionImmediate(1, updates);

      expect(mockUpdateSection).toHaveBeenCalledWith(1, updates, expect.any(Object));
    });
  });
});
