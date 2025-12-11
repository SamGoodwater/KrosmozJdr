/**
 * Tests unitaires pour useSectionSave
 */
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { useSectionSave } from '@/Pages/Organismes/section/composables/useSectionSave';

// Mock useSectionAPI
vi.mock('@/Pages/Organismes/section/composables/useSectionAPI', () => ({
  useSectionAPI: () => ({
    updateSection: vi.fn((id, updates, options) => {
      if (options?.onSuccess) {
        options.onSuccess({ props: { page: { sections: [] } } });
      }
      return Promise.resolve();
    }),
  }),
}));

describe('useSectionSave', () => {
  beforeEach(() => {
    vi.useFakeTimers();
    vi.clearAllMocks();
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

      const { useSectionAPI } = await import('@/Pages/Organismes/section/composables/useSectionAPI');
      const { updateSection } = useSectionAPI();

      expect(updateSection).toHaveBeenCalledWith(1, updates, expect.any(Object));
    });

    it('devrait utiliser un debounce personnalisé', async () => {
      const { saveSection } = useSectionSave();
      const updates = { data: { content: 'Test content' } };

      saveSection(1, updates, 1000);

      // Avancer le temps de 500ms (pas assez)
      vi.advanceTimersByTime(500);

      const { useSectionAPI } = await import('@/Pages/Organismes/section/composables/useSectionAPI');
      const { updateSection } = useSectionAPI();

      expect(updateSection).not.toHaveBeenCalled();

      // Avancer le temps de 500ms supplémentaires
      vi.advanceTimersByTime(500);
      await vi.runAllTimersAsync();

      expect(updateSection).toHaveBeenCalled();
    });

    it('devrait annuler la sauvegarde précédente si une nouvelle est déclenchée', async () => {
      const { saveSection } = useSectionSave();

      saveSection(1, { data: { content: 'First' } });
      saveSection(1, { data: { content: 'Second' } });

      vi.advanceTimersByTime(500);
      await vi.runAllTimersAsync();

      const { useSectionAPI } = await import('@/Pages/Organismes/section/composables/useSectionAPI');
      const { updateSection } = useSectionAPI();

      // Seule la dernière sauvegarde devrait être appelée
      expect(updateSection).toHaveBeenCalledTimes(1);
      expect(updateSection).toHaveBeenCalledWith(1, { data: { content: 'Second' } }, expect.any(Object));
    });
  });

  describe('saveSectionImmediate', () => {
    it('devrait sauvegarder immédiatement sans debounce', async () => {
      const { saveSectionImmediate } = useSectionSave();
      const updates = { data: { content: 'Test content' } };

      await saveSectionImmediate(1, updates);

      const { useSectionAPI } = await import('@/Pages/Organismes/section/composables/useSectionAPI');
      const { updateSection } = useSectionAPI();

      expect(updateSection).toHaveBeenCalledWith(1, updates, expect.any(Object));
    });
  });
});

