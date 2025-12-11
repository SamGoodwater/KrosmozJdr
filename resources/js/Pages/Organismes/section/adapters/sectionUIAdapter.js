/**
 * Adapter UI pour transformer les données Section en format UI
 * 
 * @description
 * Transforme les données de section (Model) en format adapté à l'UI :
 * - Couleurs selon l'état
 * - Icônes selon le template (depuis config.js)
 * - Badges pour les statuts
 * - Labels pour les visibilités
 * 
 * **Source des données :**
 * - Les icônes des templates sont récupérées depuis les fichiers config.js
 * - Aucune référence hardcodée aux templates spécifiques
 * 
 * @example
 * const uiData = sectionUIAdapter.adapt(section);
 * // { color: 'primary', icon: 'fa-file-text', badge: 'Publié', ... }
 */
import { computed } from 'vue';
import { getTemplateConfig } from '../templates';

/**
 * Adapter pour transformer une section en données UI
 * 
 * @param {Object} section - Section (Model ou données brutes)
 * @returns {Object} Données UI adaptées
 */
export function adaptSectionToUI(section) {
  return {
    // Couleur selon l'état
    color: getStateColor(section.state),
    
    // Icône selon le template
    icon: getTemplateIcon(section.template || section.type),
    
    // Badge pour l'état
    badge: getStateBadge(section.state),
    
    // Label pour la visibilité
    visibilityLabel: getVisibilityLabel(section.is_visible),
    
    // Label pour le rôle d'édition
    editRoleLabel: getEditRoleLabel(section.can_edit_role),
    
    // Statut combiné (pour affichage)
    status: getCombinedStatus(section),
    
    // Classe CSS pour le conteneur
    containerClass: getContainerClass(section),
    
    // URL de la section (pour les liens)
    url: getSectionUrl(section),
    
    // Métadonnées pour l'affichage
    metadata: getMetadata(section),
  };
}

/**
 * Retourne la couleur selon l'état
 * 
 * @param {String} state - État de la section
 * @returns {String} Couleur (primary, success, warning, error, etc.)
 */
function getStateColor(state) {
  const colorMap = {
    'draft': 'warning',
    'preview': 'info',
    'published': 'success',
    'archived': 'neutral',
  };
  
  return colorMap[state] || 'neutral';
}

/**
 * Retourne l'icône selon le template
 * 
 * Utilise la configuration du template pour récupérer l'icône.
 * Si le template n'est pas trouvé, retourne une icône par défaut.
 * 
 * @param {String} template - Type de template
 * @returns {String} Nom de l'icône FontAwesome
 */
function getTemplateIcon(template) {
  if (!template) {
    return 'fa-file';
  }
  
  try {
    const config = getTemplateConfig(template);
    if (config && config.icon) {
      return config.icon;
    }
  } catch (e) {
    // Si l'import échoue, utiliser une icône par défaut
    console.warn(`Impossible de charger la config du template "${template}"`, e);
  }
  
  return 'fa-file'; // Icône par défaut
}

/**
 * Retourne le badge selon l'état
 * 
 * @param {String} state - État de la section
 * @returns {Object} { text, color, variant }
 */
function getStateBadge(state) {
  const badgeMap = {
    'draft': { text: 'Brouillon', color: 'warning', variant: 'soft' },
    'preview': { text: 'Prévisualisation', color: 'info', variant: 'soft' },
    'published': { text: 'Publié', color: 'success', variant: 'soft' },
    'archived': { text: 'Archivé', color: 'neutral', variant: 'soft' },
  };
  
  return badgeMap[state] || { text: 'Inconnu', color: 'neutral', variant: 'soft' };
}

/**
 * Retourne le label pour la visibilité
 * 
 * @param {String} visibility - Niveau de visibilité
 * @returns {String} Label lisible
 */
function getVisibilityLabel(visibility) {
  const labelMap = {
    'guest': 'Public',
    'user': 'Utilisateurs',
    'game_master': 'Maîtres de jeu',
    'admin': 'Administrateurs',
  };
  
  return labelMap[visibility] || 'Inconnu';
}

/**
 * Retourne le label pour le rôle d'édition
 * 
 * @param {String} editRole - Rôle requis pour éditer
 * @returns {String} Label lisible
 */
function getEditRoleLabel(editRole) {
  const labelMap = {
    'guest': 'Tous',
    'user': 'Utilisateurs',
    'game_master': 'Maîtres de jeu',
    'admin': 'Administrateurs uniquement',
  };
  
  return labelMap[editRole] || 'Inconnu';
}

/**
 * Retourne le statut combiné (pour affichage)
 * 
 * @param {Object} section - Section
 * @returns {Object} { text, color, icon }
 */
function getCombinedStatus(section) {
  const state = section.state || 'draft';
  const badge = getStateBadge(state);
  
  return {
    text: badge.text,
    color: badge.color,
    icon: getTemplateIcon(section.template || section.type),
    visibility: getVisibilityLabel(section.is_visible),
    editRole: getEditRoleLabel(section.can_edit_role),
  };
}

/**
 * Retourne la classe CSS pour le conteneur
 * 
 * @param {Object} section - Section
 * @returns {String} Classe CSS
 */
function getContainerClass(section) {
  const classes = ['section-container'];
  
  // Classe selon l'état
  classes.push(`section-state-${section.state || 'draft'}`);
  
  // Classe selon le template
  classes.push(`section-template-${section.template || section.type || 'text'}`);
  
  // Classe selon la visibilité
  classes.push(`section-visibility-${section.is_visible || 'guest'}`);
  
  return classes.join(' ');
}

/**
 * Retourne l'URL de la section (pour les liens)
 * 
 * @param {Object} section - Section
 * @returns {String} URL de la section
 */
function getSectionUrl(section) {
  if (!section.id || !section.page) return '';
  
  const pageSlug = section.page.slug || section.page_id;
  const sectionSlug = section.slug || section.id;
  
  try {
    if (typeof route !== 'undefined') {
      return `${route('pages.show', pageSlug)}#section-${sectionSlug}`;
    }
    return `/pages/${pageSlug}#section-${sectionSlug}`;
  } catch (e) {
    return `/pages/${pageSlug}#section-${sectionSlug}`;
  }
}

/**
 * Retourne les métadonnées pour l'affichage
 * 
 * @param {Object} section - Section
 * @returns {Object} Métadonnées
 */
function getMetadata(section) {
  return {
    createdAt: section.created_at || null,
    updatedAt: section.updated_at || null,
    createdBy: section.createdBy || section.created_by_user || null,
    order: section.order || 0,
    hasContent: hasContent(section),
    isEmpty: isEmpty(section),
  };
}

/**
 * Vérifie si la section a du contenu
 * 
 * Utilise une logique générique basée sur les données.
 * Pour des vérifications spécifiques par template, celles-ci devraient être
 * définies dans les configs des templates à l'avenir.
 * 
 * @param {Object} section - Section
 * @returns {Boolean} True si la section a du contenu
 */
function hasContent(section) {
  const data = section.data || {};
  
  // Vérification générique : si data existe et n'est pas vide
  if (!data || Object.keys(data).length === 0) {
    return false;
  }
  
  // Vérifier si au moins une valeur non-null/non-empty existe
  for (const key in data) {
    const value = data[key];
    if (value !== null && value !== undefined && value !== '') {
      // Si c'est un tableau, vérifier qu'il n'est pas vide
      if (Array.isArray(value)) {
        if (value.length > 0) {
          return true;
        }
      } else if (typeof value === 'string') {
        // Si c'est une chaîne, vérifier qu'elle n'est pas vide après trim
        if (value.trim().length > 0) {
          return true;
        }
      } else if (typeof value === 'object') {
        // Si c'est un objet, vérifier qu'il n'est pas vide
        if (Object.keys(value).length > 0) {
          return true;
        }
      } else {
        // Autres types (number, boolean, etc.)
        return true;
      }
    }
  }
  
  return false;
}

/**
 * Vérifie si la section est vide
 * 
 * @param {Object} section - Section
 * @returns {Boolean} True si la section est vide
 */
function isEmpty(section) {
  return !hasContent(section);
}

/**
 * Composable pour utiliser l'adapter dans un composant Vue
 * 
 * @param {Object|ComputedRef} section - Section (peut être réactif)
 * @returns {ComputedRef} Données UI adaptées
 */
export function useSectionUIAdapter(section) {
  return computed(() => {
    const sectionValue = typeof section === 'object' && 'value' in section 
      ? section.value 
      : section;
    
    return adaptSectionToUI(sectionValue);
  });
}

export default {
  adapt: adaptSectionToUI,
  useAdapter: useSectionUIAdapter,
};

