/**
 * SharedConstants — Constantes partagées entre plusieurs entités
 *
 * @description
 * Centralise les constantes communes utilisées par plusieurs entités :
 * - Labels traduits (français)
 * - Couleurs et dégradés
 * - Icônes
 * - Options de sélection
 *
 * Ce fichier est la source de vérité unique pour les constantes partagées.
 * Facilement modifiable et accessible depuis n'importe où dans l'application.
 *
 * @example
 * import { FIELD_LABELS, LEVEL_COLORS, RARITY_GRADIENT } from '@/Utils/Entity/SharedConstants';
 * const label = FIELD_LABELS.level; // "Niveau"
 * const color = LEVEL_COLORS[15]; // Couleur pour le niveau 15
 */

/**
 * Labels traduits en français pour les champs communs
 * Utilisé pour les headers de colonnes et les labels de formulaires
 */
export const FIELD_LABELS = Object.freeze({
  // Champs communs
  level: 'Niveau',
  min_level: 'Niveau minimum',
  max_level: 'Niveau maximum',
  rarity: 'Rareté',
  price: 'Prix',
  weight: 'Poids',
  usable: 'Utilisable',
  is_visible: 'Visible',
  auto_update: 'Mise à jour automatique',
  dofusdb_id: 'ID DofusDB',
  official_id: 'ID Officiel',
  image: 'Image',
  name: 'Nom',
  description: 'Description',
  resource_type: 'Type de ressource',
  resource_type_id: 'Type de ressource',
  category: 'Catégorie',
  element: 'Élément',
  hostility: 'Hostilité',
  visibility: 'Visibilité',
});

/**
 * Icônes FontAwesome pour les champs communs
 */
export const FIELD_ICONS = Object.freeze({
  level: 'fa-solid fa-level-up-alt',
  min_level: 'fa-solid fa-level-up-alt',
  max_level: 'fa-solid fa-level-up-alt',
  rarity: 'fa-solid fa-gem',
  price: 'fa-solid fa-coins',
  weight: 'fa-solid fa-weight',
  usable: 'fa-solid fa-check-circle',
  is_visible: 'fa-solid fa-eye',
  auto_update: 'fa-solid fa-sync',
  dofusdb_id: 'fa-solid fa-database',
  official_id: 'fa-solid fa-id-card',
  image: 'fa-solid fa-image',
  name: 'fa-solid fa-font',
  description: 'fa-solid fa-align-left',
  resource_type: 'fa-solid fa-tag',
  resource_type_id: 'fa-solid fa-tag',
  category: 'fa-solid fa-folder',
  element: 'fa-solid fa-fire',
  hostility: 'fa-solid fa-shield-halved',
  visibility: 'fa-solid fa-eye',
});

/**
 * Dégradé de couleurs pour les niveaux (1-30)
 * Dégradé progressif du gris clair vers le violet foncé
 */
export const LEVEL_COLORS = Object.freeze({
  // Niveau 0 : valeur “neutre” (fallback / non défini)
  0: 'grey-200',
  // Niveaux 1-5 : Gris clair → Gris moyen
  1: 'grey-300',
  2: 'grey-400',
  3: 'grey-500',
  4: 'grey-600',
  5: 'grey-700',
  
  // Niveaux 6-10 : Gris moyen → Bleu clair
  6: 'blue-300',
  7: 'blue-400',
  8: 'blue-500',
  9: 'blue-600',
  10: 'blue-700',
  
  // Niveaux 11-15 : Bleu → Vert
  11: 'green-300',
  12: 'green-400',
  13: 'green-500',
  14: 'green-600',
  15: 'green-700',
  
  // Niveaux 16-20 : Vert → Orange  
  16: 'amber-300',
  17: 'amber-400',
  18: 'amber-500',
  19: 'amber-600',
  20: 'amber-700',
  
  // Niveaux 21-25 : Orange → Rouge
  21: 'red-300',
  22: 'red-400',
  23: 'red-500',
  24: 'red-600',
  25: 'red-700',
  
  // Niveaux 26-30 : Rouge → Violet foncé (primary)
  26: 'purple-500',
  27: 'purple-600',
  28: 'purple-700',
  29: 'fuchsia-600',
  30: 'fuchsia-700',
});

/**
 * Fonction pour obtenir la couleur d'un niveau
 * @param {number} level - Niveau (0-30, au-delà => noir)
 * @returns {string} Token couleur (Tailwind `color-shade` ou DaisyUI)
 */
export function getLevelColor(level) {
  if (typeof level !== 'number' || !Number.isFinite(level)) return 'neutral';

  // En dessous de 0 => clamp à 0 (évite les valeurs aberrantes)
  if (level <= 0) return LEVEL_COLORS[0] || 'neutral';

  // Au-delà de la plage définie => noir
  if (level > 30) return 'black';

  return LEVEL_COLORS[level] || 'neutral';
}

/**
 * Dégradé de couleurs pour la rareté (0-5)
 * Dégradé progressif : Gris → Bleu → Vert → Orange → Rouge → Rose → Violet foncé
 */
export const RARITY_GRADIENT = Object.freeze({
  0: {
    label: 'Commun',
    // Gradient stable (Tailwind token) — indépendant du thème DaisyUI
    color: 'gray-400',
    icon: 'fa-solid fa-circle',
    // Compat / fallback éventuel
    daisyColor: 'neutral',
  },
  1: {
    label: 'Peu commun',
    color: 'sky-400',
    icon: 'fa-solid fa-circle',
    daisyColor: 'info',
  },
  2: {
    label: 'Rare',
    color: 'emerald-500',
    icon: 'fa-solid fa-circle',
    daisyColor: 'success',
  },
  3: {
    label: 'Très rare',
    color: 'amber-500',
    icon: 'fa-solid fa-circle',
    daisyColor: 'warning',
  },
  4: {
    label: 'Légendaire',
    color: 'rose-600',
    icon: 'fa-solid fa-circle',
    daisyColor: 'error',
  },
  5: {
    label: 'Unique',
    color: 'fuchsia-700',
    icon: 'fa-solid fa-star',
    daisyColor: 'primary',
  },
});

/**
 * Fonction pour obtenir les options de rareté
 * Compatible avec l'ancien système RARITY_OPTIONS
 * @returns {Array<{value: number, label: string, color: string, icon: string}>}
 */
export function getRarityOptions() {
  return Object.entries(RARITY_GRADIENT).map(([value, config]) => ({
    value: parseInt(value, 10),
    label: config.label,
    color: config.color,
    icon: config.icon,
    daisyColor: config.daisyColor,
  }));
}

/**
 * Fonction pour obtenir la configuration de rareté pour une valeur
 * @param {number} rarity - Valeur de rareté (0-5)
 * @returns {Object|null} Configuration de rareté ou null
 */
export function getRarityConfig(rarity) {
  if (typeof rarity !== 'number' || rarity < 0 || rarity > 5) {
    return null;
  }
  return RARITY_GRADIENT[rarity] || null;
}

/**
 * Options de rareté (compatibilité avec l'ancien système)
 * @deprecated Utiliser getRarityOptions() à la place
 */
export const RARITY_OPTIONS = getRarityOptions();

/**
 * Rôles utilisateurs avec traductions et couleurs
 * Aligné avec app/Models/User.php
 */
export const USER_ROLES = Object.freeze({
  0: {
    key: 'guest',
    label: 'Invité·e',
    color: 'neutral',
    daisyColor: 'neutral',
    icon: 'fa-solid fa-user',
  },
  1: {
    key: 'user',
    label: 'Utilisateur·ice',
    color: 'info',
    daisyColor: 'info',
    icon: 'fa-solid fa-user',
  },
  2: {
    key: 'player',
    label: 'Joueur·euse',
    color: 'success',
    daisyColor: 'success',
    icon: 'fa-solid fa-dice',
  },
  3: {
    key: 'game_master',
    label: 'Maître de jeu',
    color: 'warning',
    daisyColor: 'warning',
    icon: 'fa-solid fa-crown',
  },
  4: {
    key: 'admin',
    label: 'Administrateur·ice',
    color: 'error',
    daisyColor: 'error',
    icon: 'fa-solid fa-shield-halved',
  },
  5: {
    key: 'super_admin',
    label: 'Super administrateur·ice',
    color: 'primary',
    daisyColor: 'primary',
    icon: 'fa-solid fa-shield',
  },
});

/**
 * Fonction pour obtenir la configuration d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {Object|null} Configuration du rôle ou null
 */
export function getRoleConfig(role) {
  if (typeof role === 'string') {
    // Chercher par clé
    const entry = Object.entries(USER_ROLES).find(([_, config]) => config.key === role);
    return entry ? USER_ROLES[entry[0]] : null;
  }
  if (typeof role === 'number' && role >= 0 && role <= 5) {
    return USER_ROLES[role] || null;
  }
  return null;
}

/**
 * Fonction pour obtenir le label traduit d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {string} Label traduit ou 'Inconnu'
 */
export function getRoleLabel(role) {
  const config = getRoleConfig(role);
  return config?.label || 'Inconnu';
}

/**
 * Fonction pour obtenir la couleur DaisyUI d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {string} Couleur DaisyUI ou 'neutral'
 */
export function getRoleColor(role) {
  const config = getRoleConfig(role);
  return config?.daisyColor || 'neutral';
}

/**
 * Fonction pour obtenir l'icône d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {string} Icône FontAwesome ou null
 */
export function getRoleIcon(role) {
  const config = getRoleConfig(role);
  return config?.icon || null;
}
