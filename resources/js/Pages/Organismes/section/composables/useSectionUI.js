/**
 * Composable pour gérer l'UI d'une section
 * 
 * @description
 * **Interface unifiée** pour l'affichage des sections dans l'UI.
 * Combine le mapper (Entity → Model) et l'adapter (Model → UI) pour fournir
 * toutes les données nécessaires à l'affichage.
 * 
 * **Flux de données :**
 * ```
 * Raw Section (props) → Mapper → Section Model → Adapter → UI Data
 * ```
 * 
 * **Retourne :**
 * - `sectionModel` : Modèle Section normalisé (accès aux propriétés)
 * - `uiData` : Données adaptées pour l'UI (couleurs, badges, icônes, classes CSS)
 * - `canEdit`, `canDelete` : Permissions calculées
 * - `templateInfo`, `stateInfo`, `visibilityInfo` : Informations structurées
 * - `metadata`, `url`, `hasContent`, `isEmpty` : Métadonnées et helpers
 * 
 * @param {Object|ComputedRef} rawSection - Section brute depuis props (peut être réactif)
 * @returns {Object} Interface complète pour l'UI
 * 
 * @example
 * // Utilisation basique
 * const { sectionModel, canEdit, templateInfo } = useSectionUI(props.section);
 * 
 * // Utilisation dans le template
 * <Badge :color="stateInfo.badge.color">{{ stateInfo.badge.text }}</Badge>
 * <Icon :source="templateInfo.icon" />
 * <button v-if="canEdit" @click="edit">Éditer</button>
 */
import { computed } from 'vue';
import { SectionMapper } from '@/Utils/Services/Mappers';
import { useTemplateRegistry } from './useTemplateRegistry';

// Registry de templates (singleton, au niveau module)
const registry = useTemplateRegistry();

/**
 * Composable pour gérer l'UI d'une section
 * 
 * @param {Object|ComputedRef} rawSection - Section brute (peut être réactif)
 * @returns {Object} { sectionModel, uiData, status, canEdit, ... }
 */
export function useSectionUI(rawSection) {
  // Section normalisée (Model)
  const sectionModel = computed(() => {
    let sectionValue;
    
    // Gérer différents types d'entrée :
    // 1. ComputedRef (avec .value)
    if (typeof rawSection === 'object' && rawSection !== null && 'value' in rawSection) {
      sectionValue = rawSection.value;
    }
    // 2. Fonction (getter) - appeler la fonction pour obtenir la valeur
    else if (typeof rawSection === 'function') {
      sectionValue = rawSection();
    }
    // 3. Valeur directe
    else {
      sectionValue = rawSection;
    }
    
    return SectionMapper.mapToModel(sectionValue);
  });

  // Données UI adaptées (logique intégrée, plus besoin d'adapter séparé)
  const uiData = computed(() => {
    const section = sectionModel.value;
    if (!section) {
      return getEmptyUIData();
    }
    
    return {
      // Couleur selon l'état
      color: getStateColor(section.state),
      
      // Icône selon le template
      icon: getTemplateIcon(section.template || section.type),
      
      // Badge pour l'état
      badge: getStateBadge(section.state),
      
      // Label pour la visibilité
      visibilityLabel: getVisibilityLabel(section.isVisible),
      
      // Label pour le rôle d'édition
      editRoleLabel: getEditRoleLabel(section.canEditRole),
      
      // Statut combiné (pour affichage)
      status: getCombinedStatus(section),
      
      // Classe CSS pour le conteneur
      containerClass: getContainerClass(section),
      
      // URL de la section (pour les liens)
      url: getSectionUrl(section),
      
      // Métadonnées pour l'affichage
      metadata: getMetadata(section),
    };
  });

  // Statut combiné
  const status = computed(() => {
    return uiData.value.status;
  });

  // Permissions
  // 
  // Flux des permissions :
  // 1. Backend : SectionResource calcule 'can.update' via SectionPolicy::update()
  // 2. SectionPolicy::update() appelle Section::canBeEditedBy($user)
  // 3. Section::canBeEditedBy() vérifie :
  //    - Les droits sur la section (can_edit_role de la section)
  //    - ET les droits sur la page (can_edit_role de la page parente)
  // 4. Frontend : SectionMapper extrait 'can.update' depuis les données
  // 5. Section Model expose canUpdate via getter
  // 6. useSectionUI expose canEdit qui utilise sectionModel.canUpdate
  const canEdit = computed(() => {
    return sectionModel.value?.canUpdate || false;
  });

  const canDelete = computed(() => {
    return sectionModel.value?.canDelete || false;
  });

  // Informations de template
  const templateInfo = computed(() => {
    const template = sectionModel.value?.template || sectionModel.value?.type || 'text';
    return {
      value: template,
      icon: uiData.value.icon,
      label: getTemplateLabel(template),
    };
  });

  // Informations d'état
  const stateInfo = computed(() => {
    const state = sectionModel.value?.state || 'draft';
    return {
      value: state,
      badge: uiData.value.badge,
      color: uiData.value.color,
      label: uiData.value.badge.text,
    };
  });

  // Informations de visibilité
  const visibilityInfo = computed(() => {
    const visibility = sectionModel.value?.isVisible || 'guest';
    return {
      value: visibility,
      label: uiData.value.visibilityLabel,
      icon: getVisibilityIcon(visibility),
    };
  });

  // Informations de rôle d'édition
  const editRoleInfo = computed(() => {
    const editRole = sectionModel.value?.canEditRole || 'admin';
    return {
      value: editRole,
      label: uiData.value.editRoleLabel,
      icon: getEditRoleIcon(editRole),
    };
  });

  // Métadonnées
  const metadata = computed(() => uiData.value.metadata);
  const url = computed(() => uiData.value.url);
  const hasContent = computed(() => metadata.value.hasContent);
  const isEmpty = computed(() => metadata.value.isEmpty);

  return {
    // Model
    sectionModel,
    
    // UI Data
    uiData,
    status,
    
    // Permissions
    canEdit,
    canDelete,
    
    // Informations
    templateInfo,
    stateInfo,
    visibilityInfo,
    editRoleInfo,
    
    // Métadonnées
    metadata,
    url,
    hasContent,
    isEmpty,
    
    // Helpers
    getContainerClass: () => uiData.value.containerClass,
  };
}

/**
 * Retourne le label du template
 * 
 * Utilise la configuration du template pour récupérer le nom.
 * Si le template n'est pas trouvé, retourne 'Inconnu'.
 * 
 * @param {String} template - Type de template
 * @returns {String} Label lisible (nom du template depuis config)
 */
function getTemplateLabel(template) {
  if (!template) {
    return 'Inconnu';
  }
  
  const config = registry.getConfig(template);
  
  return config?.name || 'Inconnu';
}

/**
 * Retourne l'icône de visibilité
 * 
 * @param {String} visibility - Niveau de visibilité
 * @returns {String} Nom de l'icône FontAwesome
 */
function getVisibilityIcon(visibility) {
  const iconMap = {
    'guest': 'fa-globe',
    'user': 'fa-users',
    'game_master': 'fa-user-shield',
    'admin': 'fa-user-cog',
  };
  
  return iconMap[visibility] || 'fa-eye';
}

/**
 * Retourne l'icône de rôle d'édition
 * 
 * @param {String} editRole - Rôle requis pour éditer
 * @returns {String} Nom de l'icône FontAwesome
 */
function getEditRoleIcon(editRole) {
  const iconMap = {
    'guest': 'fa-edit',
    'user': 'fa-user-edit',
    'game_master': 'fa-user-shield',
    'admin': 'fa-user-cog',
  };
  
  return iconMap[editRole] || 'fa-lock';
}

// ============================================
// FONCTIONS UTILITAIRES (intégrées depuis l'adapter)
// ============================================

/**
 * Retourne la couleur selon l'état
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
 */
function getTemplateIcon(template) {
  if (!template) return 'fa-file';
  
  const config = registry.getConfig(template);
  
  return config?.icon || 'fa-file';
}

/**
 * Retourne le badge selon l'état
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
 * Retourne le statut combiné
 */
function getCombinedStatus(section) {
  const state = section?.state || 'draft';
  const badge = getStateBadge(state);
  
  return {
    text: badge.text,
    color: badge.color,
    icon: getTemplateIcon(section?.template || section?.type),
    visibility: getVisibilityLabel(section?.isVisible),
    editRole: getEditRoleLabel(section?.canEditRole),
  };
}

/**
 * Retourne la classe CSS pour le conteneur
 */
function getContainerClass(section) {
  const classes = ['section-container'];
  classes.push(`section-state-${section?.state || 'draft'}`);
  classes.push(`section-template-${section?.template || section?.type || 'text'}`);
  classes.push(`section-visibility-${section?.isVisible || 'guest'}`);
  return classes.join(' ');
}

/**
 * Retourne l'URL de la section
 */
function getSectionUrl(section) {
  if (!section?.id || !section?.page) return '';
  
  const pageSlug = section.page.slug || section.pageId;
  const sectionSlug = section.slug || section.id;
  
  try {
    if (typeof route !== 'undefined') {
      return `${route('pages.show', pageSlug)}#section-${sectionSlug}`;
    }
    return `/pages/${pageSlug}#section-${sectionSlug}`;
  } catch {
    return `/pages/${pageSlug}#section-${sectionSlug}`;
  }
}

/**
 * Retourne les métadonnées
 */
function getMetadata(section) {
  return {
    createdAt: section?.created_at || null,
    updatedAt: section?.updated_at || null,
    createdBy: section?.createdBy || section?.created_by_user || null,
    order: section?.order || 0,
    hasContent: hasContent(section),
    isEmpty: isEmpty(section),
  };
}

/**
 * Vérifie si la section a du contenu
 */
function hasContent(section) {
  const data = section?.data || {};
  
  if (!data || Object.keys(data).length === 0) {
    return false;
  }
  
  for (const key in data) {
    const value = data[key];
    if (value !== null && value !== undefined && value !== '') {
      if (Array.isArray(value)) {
        if (value.length > 0) return true;
      } else if (typeof value === 'string') {
        if (value.trim().length > 0) return true;
      } else if (typeof value === 'object') {
        if (Object.keys(value).length > 0) return true;
      } else {
        return true;
      }
    }
  }
  
  return false;
}

/**
 * Vérifie si la section est vide
 */
function isEmpty(section) {
  return !hasContent(section);
}

/**
 * Retourne des données UI vides (fallback)
 */
function getEmptyUIData() {
  return {
    color: 'neutral',
    icon: 'fa-file',
    badge: { text: 'Inconnu', color: 'neutral', variant: 'soft' },
    visibilityLabel: 'Inconnu',
    editRoleLabel: 'Inconnu',
    status: { text: 'Inconnu', color: 'neutral', icon: 'fa-file' },
    containerClass: 'section-container',
    url: '',
    metadata: {
      createdAt: null,
      updatedAt: null,
      createdBy: null,
      order: 0,
      hasContent: false,
      isEmpty: true,
    },
  };
}

export default useSectionUI;

