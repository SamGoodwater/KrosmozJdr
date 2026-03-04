/**
 * Template Registry - Système centralisé de gestion des templates
 * 
 * @description
 * Registry optimisé avec :
 * - Cache des composants chargés (évite les recharges)
 * - Validation des configurations au démarrage
 * - Gestion d'erreurs robuste
 * - Performance optimisée (computed cached)
 * 
 * @example
 * const registry = useTemplateRegistry();
 * const component = await registry.loadComponent('text', 'read');
 * const isValid = registry.validateTemplate('text');
 */
import { ref, computed, shallowRef } from 'vue';
import { availableTemplates, getTemplateByValue } from '../templates';
import { logDev, warnDev } from '@/Utils/dev-logger';
const isDev = import.meta.env.DEV;

// ============================================
// CACHE GLOBAL (partagé entre toutes les instances)
// ============================================
const componentCache = new Map(); // Cache: 'templateName:mode' => Component
const validationCache = new Map(); // Cache: 'templateName' => boolean
let registryInitialized = false;

// ============================================
// VALIDATION DES CONFIGURATIONS
// ============================================

/**
 * Valide la structure d'une configuration de template
 * @param {Object} config - Configuration du template
 * @returns {Object} { valid: boolean, errors: Array<string> }
 */
function validateTemplateConfig(config) {
  const errors = [];
  
  // Champs obligatoires
  const requiredFields = ['value', 'name', 'icon', 'description'];
  for (const field of requiredFields) {
    if (!config[field]) {
      errors.push(`Champ obligatoire manquant: ${field}`);
    }
  }
  
  // Validation du type de valeur
  if (config.value && typeof config.value !== 'string') {
    errors.push(`Le champ "value" doit être une chaîne de caractères`);
  }
  
  // Validation des fonctions de chargement
  if (!config.readComponent || typeof config.readComponent !== 'function') {
    errors.push(`Le champ "readComponent" doit être une fonction`);
  }
  
  if (!config.editComponent || typeof config.editComponent !== 'function') {
    errors.push(`Le champ "editComponent" doit être une fonction`);
  }
  
  // Validation des données par défaut (optionnelles mais doivent être des objets)
  if (config.defaultSettings && typeof config.defaultSettings !== 'object') {
    errors.push(`Le champ "defaultSettings" doit être un objet`);
  }
  
  if (config.defaultData && typeof config.defaultData !== 'object') {
    errors.push(`Le champ "defaultData" doit être un objet`);
  }
  
  return {
    valid: errors.length === 0,
    errors
  };
}

/**
 * Initialise le registry et valide toutes les configurations
 */
function initializeRegistry() {
  if (registryInitialized) return;
  
  if (isDev) console.group('🎨 Template Registry - Initialisation');
  
  let validCount = 0;
  let invalidCount = 0;
  
  for (const template of availableTemplates.value) {
    const validation = validateTemplateConfig(template);
    validationCache.set(template.value, validation.valid);
    
    if (validation.valid) {
      validCount++;
      logDev(`✅ Template "${template.name}" (${template.value})`);
    } else {
      invalidCount++;
      if (isDev) {
        console.error(`❌ Template "${template.name}" (${template.value}):`);
        validation.errors.forEach(error => console.error(`   - ${error}`));
      }
    }
  }
  
  if (isDev) {
    logDev(`\n📊 Résumé: ${validCount} valides, ${invalidCount} invalides`);
    console.groupEnd();
  }
  
  registryInitialized = true;
}

// ============================================
// COMPOSABLE PUBLIC
// ============================================

/**
 * Hook pour accéder au registry de templates
 * 
 * @returns {Object} API du registry
 */
export function useTemplateRegistry() {
  // Initialiser le registry au premier appel
  if (!registryInitialized) {
    initializeRegistry();
  }
  
  // État de chargement
  const isLoading = ref(false);
  const lastError = shallowRef(null);
  
  /**
   * Liste des templates valides uniquement
   */
  const validTemplates = computed(() => {
    return availableTemplates.value.filter(t => validationCache.get(t.value) === true);
  });
  
  /**
   * Statistiques du registry
   */
  const stats = computed(() => ({
    total: availableTemplates.value.length,
    valid: validTemplates.value.length,
    invalid: availableTemplates.value.length - validTemplates.value.length,
    cached: componentCache.size,
  }));
  
  /**
   * Charge un composant avec cache
   * 
   * @param {String} templateValue - Valeur du template
   * @param {String} mode - 'read' ou 'edit'
   * @returns {Promise<Component|null>} Composant Vue ou null
   */
  async function loadComponent(templateValue, mode = 'read') {
    const cacheKey = `${templateValue}:${mode}`;
    
    // Vérifier le cache d'abord
    if (componentCache.has(cacheKey)) {
      return componentCache.get(cacheKey);
    }
    
    // Vérifier que le template est valide
    if (validationCache.get(templateValue) !== true) {
      const error = new Error(`Template "${templateValue}" invalide ou non trouvé`);
      lastError.value = error;
      console.error(error.message);
      return null;
    }
    
    const config = getTemplateByValue(templateValue);
    if (!config) {
      const error = new Error(`Configuration du template "${templateValue}" non trouvée`);
      lastError.value = error;
      return null;
    }
    
    isLoading.value = true;
    
    try {
      const loaderFn = mode === 'read' ? config.readComponent : config.editComponent;
      const module = await loaderFn();
      const component = module.default || module;
      
      // Mettre en cache
      componentCache.set(cacheKey, component);
      
      lastError.value = null;
      return component;
    } catch (error) {
      lastError.value = error;
      console.error(`❌ Erreur chargement template "${templateValue}" (${mode}):`, error);
      return null;
    } finally {
      isLoading.value = false;
    }
  }
  
  /**
   * Vérifie si un template existe et est valide
   * 
   * @param {String} templateValue - Valeur du template
   * @returns {Boolean} true si valide
   */
  function isValidTemplate(templateValue) {
    return validationCache.get(templateValue) === true;
  }
  
  /**
   * Récupère la configuration d'un template
   * 
   * @param {String} templateValue - Valeur du template
   * @returns {Object|null} Configuration ou null
   */
  function getConfig(templateValue) {
    if (!isValidTemplate(templateValue)) return null;
    return getTemplateByValue(templateValue);
  }
  
  /**
   * Récupère les options pour les selects (templates valides uniquement)
   * 
   * @returns {Array} Liste des options
   */
  function getOptions() {
    return validTemplates.value.map(template => ({
      value: template.value,
      label: template.name,
      icon: template.icon,
      description: template.description,
      supportsAutoSave: template.supportsAutoSave || false,
    }));
  }
  
  /**
   * Récupère les valeurs par défaut d'un template
   * 
   * @param {String} templateValue - Valeur du template
   * @returns {Object} { settings: Object, data: Object }
   */
  function getDefaults(templateValue) {
    const config = getConfig(templateValue);
    if (!config) {
      return { settings: {}, data: {} };
    }
    
    return {
      settings: config.defaultSettings || {},
      data: config.defaultData || {},
    };
  }
  
  /**
   * Précharge un template (utile pour optimiser le chargement)
   * 
   * @param {String} templateValue - Valeur du template
   * @param {String} mode - 'read', 'edit', ou 'both'
   * @returns {Promise<void>}
   */
  async function preload(templateValue, mode = 'both') {
    if (mode === 'both') {
      await Promise.all([
        loadComponent(templateValue, 'read'),
        loadComponent(templateValue, 'edit'),
      ]);
    } else {
      await loadComponent(templateValue, mode);
    }
  }
  
  /**
   * Vide le cache des composants (utile pour le hot-reload en dev)
   */
  function clearCache() {
    componentCache.clear();
    logDev('🧹 Cache du registry vidé');
  }
  
  /**
   * Réinitialise le registry (force la revalidation)
   */
  function reset() {
    clearCache();
    validationCache.clear();
    registryInitialized = false;
    initializeRegistry();
  }
  
  return {
    // État
    isLoading,
    lastError,
    stats,
    
    // Listes
    templates: validTemplates,
    
    // Méthodes principales
    loadComponent,
    isValidTemplate,
    getConfig,
    getOptions,
    getDefaults,
    
    // Optimisation
    preload,
    clearCache,
    reset,
  };
}

/**
 * Fonction helper pour précharger les templates les plus courants
 * À appeler au démarrage de l'application pour optimiser le premier rendu
 */
export async function preloadCommonTemplates() {
  const registry = useTemplateRegistry();
  
  // CORRECTION : Ne précharger que les templates qui existent réellement
  // NOTE : `entity_table` est un template legacy (caché côté UI).
  const commonTemplates = ['text', 'image', 'gallery'];
  
  logDev('🚀 Préchargement des templates courants...');
  
  // Filtrer uniquement les templates valides avant de précharger
  const validTemplates = commonTemplates.filter(t => registry.isValidTemplate(t));
  
  if (validTemplates.length > 0) {
    await Promise.all(
      validTemplates.map(template => registry.preload(template, 'both'))
    );
    logDev(`✅ ${validTemplates.length} templates préchargés:`, validTemplates);
  } else {
    warnDev('⚠️ Aucun template valide à précharger');
  }
}

