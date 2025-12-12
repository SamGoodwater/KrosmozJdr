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
import { Section } from '@/Models';
import { SectionMapper } from '@/Utils/Services/Mappers';
import { adaptSectionToUI } from '../adapters/sectionUIAdapter';
import { getTemplateConfig } from '../templates';

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

  // Données UI adaptées
  const uiData = computed(() => {
    return adaptSectionToUI(sectionModel.value);
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
  
  try {
    const config = getTemplateConfig(template);
    if (config && config.name) {
      return config.name;
    }
  } catch (e) {
    // Si l'import échoue, utiliser un label par défaut
    console.warn(`Impossible de charger la config du template "${template}"`, e);
  }
  
  return 'Inconnu';
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

export default useSectionUI;

