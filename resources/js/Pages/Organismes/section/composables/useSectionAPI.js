/**
 * Composable pour centraliser les appels backend des sections
 * 
 * @description
 * Centralise tous les appels API liés aux sections en utilisant les modèles Page et Section.
 * Fournit des méthodes pour CRUD, réorganisation, et autres opérations.
 * 
 * @example
 * const { createSection, updateSection, deleteSection, reorderSections } = useSectionAPI();
 * await createSection({ page_id: 1, template: 'text' });
 */
import { router } from '@inertiajs/vue3';
import { Section } from '@/Models';
import { Page } from '@/Models';

/**
 * Composable pour gérer les appels API des sections
 * 
 * @returns {Object} Méthodes pour interagir avec l'API des sections
 */
export function useSectionAPI() {
  /**
   * Crée une nouvelle section
   * 
   * @param {Object} sectionData - Données de la section { page_id, template, title, order, settings, data }
   * @param {Object} options - Options Inertia (preserveScroll, only, etc.)
   * @returns {Promise<void>}
   */
  const createSection = (sectionData, options = {}) => {
    return new Promise((resolve, reject) => {
      console.log('useSectionAPI - createSection called with:', sectionData);
      console.log('useSectionAPI - Route:', route('sections.store'));
      
      router.post(route('sections.store'), sectionData, {
        preserveScroll: true,
        only: ['page'],
        onSuccess: (page) => {
          console.log('useSectionAPI - onSuccess called with:', page);
          // Passer la réponse complète pour que le callback puisse accéder aux données
          resolve(page);
        },
        onError: (errors) => {
          console.error('useSectionAPI - onError called with:', errors);
          reject(errors);
        },
        onFinish: () => {
          console.log('useSectionAPI - onFinish called');
        },
        ...options
      });
    });
  };

  /**
   * Met à jour une section
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {Object} updates - Données à mettre à jour { title, settings, data, etc. }
   * @param {Object} options - Options Inertia
   * @returns {Promise<void>}
   */
  const updateSection = (sectionId, updates, options = {}) => {
    return new Promise((resolve, reject) => {
      // Vérifier que sectionId est valide
      if (!sectionId || (typeof sectionId !== 'number' && typeof sectionId !== 'string')) {
        console.error('useSectionAPI: updateSection appelé avec un sectionId invalide', { sectionId, updates });
        reject(new Error('Section ID invalide'));
        return;
      }
      
      // Ziggy attend un objet avec la clé correspondant au paramètre de la route
      const routeParams = { section: sectionId };
      router.patch(route('sections.update', routeParams), updates, {
        preserveScroll: true,
        only: ['page'],
        onSuccess: (page) => {
          resolve(page);
        },
        onError: (errors) => {
          console.error('Erreur lors de la mise à jour de la section:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Supprime une section (soft delete)
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {Object} options - Options Inertia
   * @returns {Promise<void>}
   */
  const deleteSection = (sectionId, options = {}) => {
    return new Promise((resolve, reject) => {
      router.delete(route('sections.delete', { section: sectionId }), {
        preserveScroll: true,
        only: ['page'],
        onSuccess: (page) => {
          resolve(page);
        },
        onError: (errors) => {
          console.error('Erreur lors de la suppression de la section:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Réorganise l'ordre des sections
   * 
   * @param {Array<Object>} sections - Tableau de sections avec { id, order }
   * @param {Object} options - Options Inertia
   * @returns {Promise<void>}
   */
  const reorderSections = (sections, options = {}) => {
    return new Promise((resolve, reject) => {
      router.patch(route('sections.reorder'), { sections }, {
        preserveScroll: true,
        only: ['page'],
        onSuccess: (page) => {
          resolve(page);
        },
        onError: (errors) => {
          console.error('Erreur lors de la réorganisation des sections:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Récupère une section par son ID
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {Object} options - Options Inertia
   * @returns {Promise<Section>}
   */
  const getSection = (sectionId, options = {}) => {
    return new Promise((resolve, reject) => {
      router.get(route('sections.show', { section: sectionId }), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
          // Extraire la section depuis la réponse
          const sectionData = page.section || page.data?.section;
          if (sectionData) {
            resolve(new Section(sectionData));
          } else {
            reject(new Error('Section non trouvée dans la réponse'));
          }
        },
        onError: (errors) => {
          console.error('Erreur lors de la récupération de la section:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Restaure une section supprimée
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {Object} options - Options Inertia
   * @returns {Promise<void>}
   */
  const restoreSection = (sectionId, options = {}) => {
    return new Promise((resolve, reject) => {
      router.post(route('sections.restore', { section: sectionId }), {}, {
        preserveScroll: true,
        only: ['page'],
        onSuccess: (page) => {
          resolve(page);
        },
        onError: (errors) => {
          console.error('Erreur lors de la restauration de la section:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Supprime définitivement une section (force delete)
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {Object} options - Options Inertia
   * @returns {Promise<void>}
   */
  const forceDeleteSection = (sectionId, options = {}) => {
    return new Promise((resolve, reject) => {
      router.delete(route('sections.forceDelete', { section: sectionId }), {
        preserveScroll: true,
        only: ['page'],
        onSuccess: (page) => {
          resolve(page);
        },
        onError: (errors) => {
          console.error('Erreur lors de la suppression définitive de la section:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Ajoute un fichier à une section
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {File} file - Fichier à uploader
   * @param {Object} metadata - Métadonnées du fichier { title, comment, description, order }
   * @param {Object} options - Options Inertia
   * @returns {Promise<Object>}
   */
  const attachFile = (sectionId, file, metadata = {}, options = {}) => {
    return new Promise((resolve, reject) => {
      const formData = new FormData();
      formData.append('file', file);
      if (metadata.title) formData.append('title', metadata.title);
      if (metadata.comment) formData.append('comment', metadata.comment);
      if (metadata.description) formData.append('description', metadata.description);
      if (metadata.order !== undefined) formData.append('order', metadata.order);

      router.post(route('sections.files.store', sectionId), formData, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
          resolve(response);
        },
        onError: (errors) => {
          console.error('Erreur lors de l\'ajout du fichier:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  /**
   * Supprime un fichier d'une section
   * 
   * @param {Number|String} sectionId - ID de la section
   * @param {Number|String} fileId - ID du fichier
   * @param {Object} options - Options Inertia
   * @returns {Promise<void>}
   */
  const detachFile = (sectionId, fileId, options = {}) => {
    return new Promise((resolve, reject) => {
      router.delete(route('sections.files.delete', [sectionId, fileId]), {
        preserveScroll: true,
        onSuccess: () => {
          resolve();
        },
        onError: (errors) => {
          console.error('Erreur lors de la suppression du fichier:', errors);
          reject(errors);
        },
        ...options
      });
    });
  };

  return {
    createSection,
    updateSection,
    deleteSection,
    reorderSections,
    getSection,
    restoreSection,
    forceDeleteSection,
    attachFile,
    detachFile,
  };
}

