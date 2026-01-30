/**
 * Composable pour gérer les formulaires de pages (création/édition)
 * 
 * @description
 * Centralise toute la logique commune entre CreatePageModal et EditPageModal :
 * - Création et gestion du formulaire Inertia
 * - Génération automatique du slug depuis le titre
 * - Validation des champs
 * - Gestion des erreurs
 * 
 * @param {Object} initialData - Données initiales du formulaire (optionnel pour création)
 * @param {Object} options - Options { mode: 'create'|'edit' }
 * @returns {Object} Formulaire et utilitaires
 * 
 * @example
 * // Mode création
 * const { form, slugManuallyEdited, handleSlugInput, titleValidation, slugValidation } = usePageForm();
 * 
 * // Mode édition
 * const { form, ... } = usePageForm({ title: 'Ma page', slug: 'ma-page', ... });
 */
import { useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import { TransformService } from '@/Utils/Services';

export function usePageForm(initialData = null, options = {}) {
  const mode = options.mode || (initialData ? 'edit' : 'create');
  
  // Valeurs par défaut pour la création
  const defaultValues = {
    title: '',
    slug: '',
    read_level: 0,
    write_level: 4,
    in_menu: true,
    state: 'draft',
    parent_id: null,
    menu_order: 0
  };
  
  // Fusionner avec les données initiales si fournies
  const formData = initialData ? { ...defaultValues, ...initialData } : defaultValues;
  
  // Formulaire Inertia
  const form = useForm(formData);
  
  // Flag pour savoir si l'utilisateur a modifié manuellement le slug
  const slugManuallyEdited = ref(false);
  
  /**
   * Génère un slug depuis un titre
   * @param {String} title - Titre de la page
   * @returns {String} Slug généré
   */
  const generateSlug = (title) => {
    return TransformService.generateSlugFromTitle(title);
  };
  
  /**
   * Génération automatique du slug depuis le titre
   * Seulement si le slug n'a pas été modifié manuellement
   */
  watch(() => form.title, (newTitle) => {
    if (newTitle && !slugManuallyEdited.value) {
      form.slug = generateSlug(newTitle);
    }
  });
  
  /**
   * Détecter si l'utilisateur modifie manuellement le slug
   */
  const handleSlugInput = () => {
    slugManuallyEdited.value = true;
  };
  
  /**
   * Computed pour la validation du titre
   */
  const titleValidation = computed(() => {
    if (!form.errors.title) return null;
    return {
      state: 'error',
      message: form.errors.title,
      showNotification: false
    };
  });
  
  /**
   * Computed pour la validation du slug
   */
  const slugValidation = computed(() => {
    if (!form.errors.slug) return null;
    return {
      state: 'error',
      message: form.errors.slug,
      showNotification: false
    };
  });
  
  /**
   * Réinitialise le formulaire et le flag de slug manuel
   */
  const resetForm = () => {
    form.reset();
    form.clearErrors();
    slugManuallyEdited.value = false;
  };
  
  /**
   * Soumet le formulaire vers la route appropriée
   * @param {String} routeName - Nom de la route ('pages.store' ou 'pages.update')
   * @param {Object} routeParams - Paramètres de la route (optionnel, pour update)
   * @param {Object} callbacks - Callbacks { onSuccess, onError }
   */
  const submitForm = (routeName, routeParams = {}, callbacks = {}) => {
    const method = mode === 'create' ? 'post' : 'patch';
    const routeUrl = routeParams.id 
      ? route(routeName, routeParams) 
      : route(routeName);
    
    form[method](routeUrl, {
      preserveScroll: true,
      onSuccess: () => {
        if (callbacks.onSuccess) callbacks.onSuccess();
      },
      onError: () => {
        if (callbacks.onError) callbacks.onError();
      }
    });
  };
  
  return {
    form,
    mode,
    slugManuallyEdited,
    handleSlugInput,
    titleValidation,
    slugValidation,
    generateSlug,
    resetForm,
    submitForm
  };
}

