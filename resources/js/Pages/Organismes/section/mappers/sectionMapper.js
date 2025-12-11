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
import { computed, toRaw } from 'vue';

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
  
  // IMPORTANT : Désenvelopper les Proxies Vue/Inertia avec toRaw()
  // Cela permet d'accéder directement aux propriétés sans passer par le Proxy
  // On perd la réactivité mais on gagne en fiabilité pour la normalisation
  let unwrappedRawData;
  try {
    // toRaw() désenveloppe les Proxies Vue réactifs
    // Si ce n'est pas un Proxy, toRaw() retourne l'objet tel quel
    unwrappedRawData = toRaw(rawData);
  } catch (e) {
    // Si toRaw() échoue, utiliser rawData directement
    unwrappedRawData = rawData;
  }
  
  // Extraire les données si elles sont dans .data (Resource)
  // Les sections peuvent venir directement ou être dans .data
  const unwrappedData = unwrappedRawData.data || unwrappedRawData;
  
  // Fonction helper pour extraire une valeur d'un objet
  // Maintenant que les données sont désenveloppées, l'accès est direct
  const extractValue = (obj, key) => {
    if (!obj) return undefined;
    try {
      return obj[key];
    } catch (e) {
      return undefined;
    }
  };
  
  // Extraire les permissions
  // Les permissions peuvent être au niveau racine (rawData.can) ou dans .data (data.can)
  // Priorité : rawData.can > data.can
  let canPermissions = null;
  
  try {
    // Essayer d'abord depuis rawData (niveau racine)
    const rawCan = extractValue(unwrappedRawData, 'can');
    if (rawCan && typeof rawCan === 'object') {
      // Désenvelopper si c'est encore un Proxy
      let unwrappedCan = rawCan;
      try {
        unwrappedCan = toRaw(rawCan);
      } catch (e) {
        // Ignorer si ce n'est pas un Proxy
      }
      
      canPermissions = {
        update: unwrappedCan.update === true || unwrappedCan.update === 1,
        delete: unwrappedCan.delete === true || unwrappedCan.delete === 1,
        forceDelete: unwrappedCan.forceDelete === true || unwrappedCan.forceDelete === 1,
        restore: unwrappedCan.restore === true || unwrappedCan.restore === 1,
      };
    }
  } catch (e) {
    // Ignorer les erreurs
  }
  
  // Si pas trouvé dans rawData, essayer dans data
  if (!canPermissions) {
    try {
      const dataCan = extractValue(unwrappedData, 'can');
      if (dataCan && typeof dataCan === 'object') {
        // Désenvelopper si c'est encore un Proxy
        let unwrappedDataCan = dataCan;
        try {
          unwrappedDataCan = toRaw(dataCan);
        } catch (e) {
          // Ignorer si ce n'est pas un Proxy
        }
        
        canPermissions = {
          update: unwrappedDataCan.update === true || unwrappedDataCan.update === 1,
          delete: unwrappedDataCan.delete === true || unwrappedDataCan.delete === 1,
          forceDelete: unwrappedDataCan.forceDelete === true || unwrappedDataCan.forceDelete === 1,
          restore: unwrappedDataCan.restore === true || unwrappedDataCan.restore === 1,
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
  
  return {
    id: extractValue(unwrappedData, 'id') || extractValue(unwrappedRawData, 'id'),
    page_id: extractValue(unwrappedData, 'page_id') || extractValue(unwrappedRawData, 'page_id'),
    title: extractValue(unwrappedData, 'title') || extractValue(unwrappedRawData, 'title') || null,
    slug: extractValue(unwrappedData, 'slug') || extractValue(unwrappedRawData, 'slug') || null,
    order: extractValue(unwrappedData, 'order') || extractValue(unwrappedRawData, 'order') || 0,
    template: extractValue(unwrappedData, 'template') || extractValue(unwrappedData, 'type') || extractValue(unwrappedRawData, 'template') || extractValue(unwrappedRawData, 'type') || 'text',
    settings: extractValue(unwrappedData, 'settings') || extractValue(unwrappedRawData, 'settings') || {},
    data: extractValue(unwrappedData, 'data') || extractValue(unwrappedRawData, 'data') || {},
    is_visible: extractValue(unwrappedData, 'is_visible') || extractValue(unwrappedRawData, 'is_visible') || 'guest',
    can_edit_role: extractValue(unwrappedData, 'can_edit_role') || extractValue(unwrappedRawData, 'can_edit_role') || 'admin',
    state: extractValue(unwrappedData, 'state') || extractValue(unwrappedRawData, 'state') || 'draft',
    created_by: extractValue(unwrappedData, 'created_by') || extractValue(unwrappedRawData, 'created_by') || null,
    created_at: extractValue(unwrappedData, 'created_at') || extractValue(unwrappedRawData, 'created_at') || null,
    updated_at: extractValue(unwrappedData, 'updated_at') || extractValue(unwrappedRawData, 'updated_at') || null,
    deleted_at: extractValue(unwrappedData, 'deleted_at') || extractValue(unwrappedRawData, 'deleted_at') || null,
    
    // Relations (si chargées)
    page: (extractValue(unwrappedData, 'page') || extractValue(unwrappedRawData, 'page')) ? normalizePageData(extractValue(unwrappedData, 'page') || extractValue(unwrappedRawData, 'page')) : null,
    users: extractValue(unwrappedData, 'users') || extractValue(unwrappedRawData, 'users') || [],
    files: extractValue(unwrappedData, 'files') || extractValue(unwrappedRawData, 'files') || [],
    createdBy: extractValue(unwrappedData, 'createdBy') || extractValue(unwrappedData, 'created_by_user') || extractValue(unwrappedRawData, 'createdBy') || extractValue(unwrappedRawData, 'created_by_user') || null,
    
    // Permissions - extraites correctement depuis rawData ou data (désenveloppées)
    can: canPermissions,
  };
}

/**
 * Normalise les données brutes d'une page (relation)
 * 
 * @param {Object} rawData - Données brutes de la page (peut être un Proxy)
 * @returns {Object} Données normalisées
 */
function normalizePageData(rawData) {
  if (!rawData) return null;
  
  // Désenvelopper les Proxies Vue/Inertia si nécessaire
  let unwrappedRawData;
  try {
    unwrappedRawData = toRaw(rawData);
  } catch (e) {
    unwrappedRawData = rawData;
  }
  
  const data = unwrappedRawData.data || unwrappedRawData;
  
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

