/**
 * Mapper pour transformer les données Entity (backend) en Model (frontend)
 * 
 * @description
 * Transforme les données brutes du backend (Resource/Entity) en modèle frontend normalisé.
 * Gère la normalisation des données, la résolution des relations, et la conversion des types.
 * 
 * @example
 * const sectionModel = sectionMapper.mapToModel(rawSectionData);
 * // Section normalisée avec toutes les propriétés accessibles
 */
import { Section } from '@/Models';
import { computed } from 'vue';

/**
 * Mappe les données brutes en modèle Section
 * 
 * @param {Object} rawData - Données brutes du backend (Resource ou Entity)
 * @returns {Section} Instance Section normalisée
 */
export function mapToSectionModel(rawData) {
  if (!rawData) return null;
  
  // Si c'est déjà une instance Section, la retourner telle quelle
  if (rawData instanceof Section) {
    return rawData;
  }
  
  // Normaliser les données brutes
  const normalized = normalizeSectionData(rawData);
  
  // Créer l'instance Section
  return new Section(normalized);
}

/**
 * Normalise les données brutes d'une section
 * 
 * @param {Object} rawData - Données brutes (peut être un Proxy Vue/Inertia)
 * @returns {Object} Données normalisées
 */
function normalizeSectionData(rawData) {
  if (!rawData) {
    return null;
  }
  
  // Extraire les données si elles sont dans .data (Resource)
  // Les sections peuvent venir directement ou être dans .data
  // Pour les objets Inertia/Vue réactifs (Proxies), les données sont directement sur l'objet
  const data = rawData.data || rawData;
  
  // Fonction helper pour extraire une valeur d'un objet (gère les Proxies Vue/Inertia)
  // Les Proxies Vue/Inertia permettent d'accéder directement aux propriétés
  const extractValue = (obj, key) => {
    if (!obj) return undefined;
    try {
      // Accéder directement à la propriété (fonctionne avec les Proxies)
      return obj[key];
    } catch (e) {
      return undefined;
    }
  };
  
  // Extraire les permissions - les Proxies Vue/Inertia permettent l'accès direct
  // Essayer d'abord depuis rawData, puis depuis data
  let canPermissions = null;
  
  try {
    const rawCan = extractValue(rawData, 'can');
    if (rawCan && typeof rawCan === 'object') {
      // Les Proxies permettent l'accès direct aux propriétés
      canPermissions = {
        update: rawCan.update === true || rawCan.update === 1,
        delete: rawCan.delete === true || rawCan.delete === 1,
        forceDelete: rawCan.forceDelete === true || rawCan.forceDelete === 1,
        restore: rawCan.restore === true || rawCan.restore === 1,
      };
    }
  } catch (e) {
    // Ignorer les erreurs
  }
  
  // Si pas trouvé dans rawData, essayer dans data
  if (!canPermissions) {
    try {
      const dataCan = extractValue(data, 'can');
      if (dataCan && typeof dataCan === 'object') {
        canPermissions = {
          update: dataCan.update === true || dataCan.update === 1,
          delete: dataCan.delete === true || dataCan.delete === 1,
          forceDelete: dataCan.forceDelete === true || dataCan.forceDelete === 1,
          restore: dataCan.restore === true || dataCan.restore === 1,
        };
      }
    } catch (e) {
      // Ignorer les erreurs
    }
  }
  
  // Valeurs par défaut si aucune permission trouvée
  if (!canPermissions) {
    canPermissions = {
      update: false,
      delete: false,
      forceDelete: false,
      restore: false,
    };
  }
  
  // Debug en développement
  if (import.meta.env.DEV) {
    const rawCan = extractValue(rawData, 'can');
    const dataCan = extractValue(data, 'can');
    console.log('sectionMapper - normalizeSectionData', {
      hasRawData: !!rawData,
      hasData: !!rawData.data,
      rawCan: rawCan,
      dataCan: dataCan,
      rawCanUpdate: rawCan?.update,
      dataCanUpdate: dataCan?.update,
      rawDataId: extractValue(rawData, 'id'),
      dataId: extractValue(data, 'id'),
      finalCanPermissions: canPermissions,
    });
  }
  
  return {
    id: extractValue(data, 'id') || extractValue(rawData, 'id'),
    page_id: extractValue(data, 'page_id') || extractValue(rawData, 'page_id'),
    title: extractValue(data, 'title') || extractValue(rawData, 'title') || null,
    slug: extractValue(data, 'slug') || extractValue(rawData, 'slug') || null,
    order: extractValue(data, 'order') || extractValue(rawData, 'order') || 0,
    template: extractValue(data, 'template') || extractValue(data, 'type') || extractValue(rawData, 'template') || extractValue(rawData, 'type') || 'text',
    settings: extractValue(data, 'settings') || extractValue(rawData, 'settings') || {},
    data: extractValue(data, 'data') || extractValue(rawData, 'data') || {},
    is_visible: extractValue(data, 'is_visible') || extractValue(rawData, 'is_visible') || 'guest',
    can_edit_role: extractValue(data, 'can_edit_role') || extractValue(rawData, 'can_edit_role') || 'admin',
    state: extractValue(data, 'state') || extractValue(rawData, 'state') || 'draft',
    created_by: extractValue(data, 'created_by') || extractValue(rawData, 'created_by') || null,
    created_at: extractValue(data, 'created_at') || extractValue(rawData, 'created_at') || null,
    updated_at: extractValue(data, 'updated_at') || extractValue(rawData, 'updated_at') || null,
    deleted_at: extractValue(data, 'deleted_at') || extractValue(rawData, 'deleted_at') || null,
    
    // Relations (si chargées)
    page: (extractValue(data, 'page') || extractValue(rawData, 'page')) ? normalizePageData(extractValue(data, 'page') || extractValue(rawData, 'page')) : null,
    users: extractValue(data, 'users') || extractValue(rawData, 'users') || [],
    files: extractValue(data, 'files') || extractValue(rawData, 'files') || [],
    createdBy: extractValue(data, 'createdBy') || extractValue(data, 'created_by_user') || extractValue(rawData, 'createdBy') || extractValue(rawData, 'created_by_user') || null,
    
    // Permissions - extraites correctement depuis rawData ou data
    can: canPermissions,
  };
}

/**
 * Normalise les données brutes d'une page (relation)
 * 
 * @param {Object} rawData - Données brutes de la page
 * @returns {Object} Données normalisées
 */
function normalizePageData(rawData) {
  const data = rawData.data || rawData;
  
  return {
    id: data.id,
    title: data.title,
    slug: data.slug,
    is_visible: data.is_visible,
    can_edit_role: data.can_edit_role,
    in_menu: data.in_menu,
    state: data.state,
    parent_id: data.parent_id,
    menu_order: data.menu_order,
    created_by: data.created_by,
    created_at: data.created_at,
    updated_at: data.updated_at,
  };
}

/**
 * Mappe un tableau de sections
 * 
 * @param {Array} rawSections - Tableau de données brutes
 * @returns {Array<Section>} Tableau d'instances Section
 */
export function mapToSectionModels(rawSections) {
  if (!Array.isArray(rawSections)) {
    return [];
  }
  
  return rawSections.map(section => mapToSectionModel(section));
}

/**
 * Mappe les données pour un formulaire
 * 
 * @param {Section|Object} section - Section (Model ou données brutes)
 * @returns {Object} Données formatées pour un formulaire
 */
export function mapToFormData(section) {
  const sectionModel = section instanceof Section ? section : mapToSectionModel(section);
  
  return {
    page_id: sectionModel.pageId,
    title: sectionModel.title || '',
    slug: sectionModel.slug || '',
    order: sectionModel.order || 0,
    template: sectionModel.template,
    settings: sectionModel.settings || {},
    data: sectionModel.data || {},
    is_visible: sectionModel.isVisible || 'guest',
    can_edit_role: sectionModel.canEditRole || 'admin',
    state: sectionModel.state || 'draft',
  };
}

/**
 * Composable pour utiliser le mapper dans un composant Vue
 * 
 * @param {Object|ComputedRef} rawSection - Section brute (peut être réactif)
 * @returns {ComputedRef<Section>} Section normalisée
 */
export function useSectionMapper(rawSection) {
  return computed(() => {
    const sectionValue = typeof rawSection === 'object' && 'value' in rawSection 
      ? rawSection.value 
      : rawSection;
    
    return mapToSectionModel(sectionValue);
  });
}

export default {
  mapToModel: mapToSectionModel,
  mapToModels: mapToSectionModels,
  mapToFormData,
  useMapper: useSectionMapper,
};

