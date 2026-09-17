/**
 * Tests unitaires pour useSectionAPI
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useSectionAPI } from '@/Pages/Organismes/section/composables/useSectionAPI';

// Mock Inertia router
vi.mock('@inertiajs/vue3', () => ({
  router: {
    post: vi.fn((url, data, options) => {
      if (options?.onSuccess) {
        options.onSuccess({ props: { page: { sections: [] } } });
      }
    }),
    patch: vi.fn((url, data, options) => {
      if (options?.onSuccess) {
        options.onSuccess({ props: { page: { sections: [] } } });
      }
    }),
    delete: vi.fn((url, options) => {
      if (options?.onSuccess) {
        options.onSuccess({ props: { page: { sections: [] } } });
      }
    }),
    get: vi.fn((url, options) => {
      if (options?.onSuccess) {
        options.onSuccess({ props: { page: { sections: [] } } });
      }
    }),
    reload: vi.fn((options) => {
      if (options?.onSuccess) {
        options.onSuccess();
      }
    }),
  },
}));

// Mock route() (Ziggy)
global.route = vi.fn((name, params) => {
  if (typeof params === 'string' || typeof params === 'number') {
    return `/route/${name}/${params}`;
  }
  if (params && typeof params === 'object') {
    const query = new URLSearchParams(params).toString();
    return `/route/${name}${query ? '?' + query : ''}`;
  }
  return `/route/${name}`;
});

describe('useSectionAPI', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('createSection', () => {
    it('devrait créer une section avec succès', async () => {
      const { createSection } = useSectionAPI();
      const sectionData = {
        page_id: 1,
        template: 'text',
        title: 'Test Section',
      };

      const result = await createSection(sectionData);

      expect(result).toBeDefined();
    });

    it('devrait gérer les erreurs lors de la création', async () => {
      const { createSection } = useSectionAPI();
      const sectionData = {
        page_id: 1,
        template: 'text',
      };

      // Mock d'erreur
      const { router } = await import('@inertiajs/vue3');
      router.post.mockImplementationOnce((url, data, options) => {
        if (options?.onError) {
          options.onError({ message: 'Erreur de validation' });
        }
      });

      await expect(createSection(sectionData)).rejects.toBeDefined();
    });
  });

  describe('updateSection', () => {
    it('devrait mettre à jour une section', async () => {
      const { updateSection } = useSectionAPI();
      const updates = {
        title: 'Updated Title',
        data: { content: 'Updated content' },
      };

      const result = await updateSection(1, updates);

      expect(result).toBeDefined();
    });
  });

  describe('deleteSection', () => {
    it('devrait supprimer une section', async () => {
      const { deleteSection } = useSectionAPI();

      const result = await deleteSection(1);

      expect(result).toBeDefined();
    });
  });

  describe('reorderSections', () => {
    it('devrait réorganiser les sections', async () => {
      const { reorderSections } = useSectionAPI();
      const sections = [
        { id: 1, order: 0 },
        { id: 2, order: 1 },
        { id: 3, order: 2 },
      ];

      const result = await reorderSections(sections);

      expect(result).toBeDefined();
    });
  });
});

