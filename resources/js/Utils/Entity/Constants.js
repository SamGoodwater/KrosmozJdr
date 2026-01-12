/**
 * Entity Constants — Constantes communes à tous les descriptors et configurations d'entités
 *
 * @description
 * Ce fichier centralise toutes les constantes partagées entre les différentes entités.
 * Cela évite la duplication et facilite la maintenance.
 * 
 * Source de vérité pour les options de rareté, visibilité, hostilité, etc.
 */

/**
 * Options de rareté (0-5)
 * Utilisé par : Resource, Item, Spell, Monster, etc.
 */
export const RARITY_OPTIONS = Object.freeze([
  { value: 0, label: "Commun", color: "gray", icon: "fa-solid fa-circle" },
  { value: 1, label: "Peu commun", color: "blue", icon: "fa-solid fa-circle" },
  { value: 2, label: "Rare", color: "green", icon: "fa-solid fa-circle" },
  { value: 3, label: "Très rare", color: "purple", icon: "fa-solid fa-circle" },
  { value: 4, label: "Légendaire", color: "orange", icon: "fa-solid fa-star" },
  { value: 5, label: "Unique", color: "red", icon: "fa-solid fa-star" },
]);

/**
 * Fonction helper pour obtenir les options de rareté
 * @returns {Array<{value: number, label: string}>}
 */
export function getRarityOptions() {
  return RARITY_OPTIONS.map(({ value, label }) => ({ value, label }));
}

/**
 * Fonction helper pour obtenir le label d'une rareté
 * @param {number} value
 * @returns {string}
 */
export function getRarityLabel(value) {
  const option = RARITY_OPTIONS.find((opt) => opt.value === value);
  return option?.label || `Rareté ${value}`;
}

/**
 * Fonction helper pour obtenir la couleur d'une rareté
 * @param {number} value
 * @returns {string}
 */
export function getRarityColor(value) {
  const option = RARITY_OPTIONS.find((opt) => opt.value === value);
  return option?.color || "gray";
}

/**
 * Options de visibilité
 * Utilisé par : Resource, Item, Spell, Monster, etc.
 */
export const VISIBILITY_OPTIONS = Object.freeze([
  { value: "guest", label: "Invité", color: "gray" },
  { value: "user", label: "Utilisateur", color: "blue" },
  { value: "game_master", label: "Maître de jeu", color: "purple" },
  { value: "admin", label: "Administrateur", color: "red" },
]);

/**
 * Options d'hostilité (pour les créatures)
 */
export const HOSTILITY_OPTIONS = Object.freeze([
  { value: 0, label: "Amical", color: "green" },
  { value: 1, label: "Curieux", color: "blue" },
  { value: 2, label: "Neutre", color: "gray" },
  { value: 3, label: "Hostile", color: "orange" },
  { value: 4, label: "Aggressif", color: "red" },
]);

/**
 * Breakpoints responsive (en pixels)
 * Aligné avec Tailwind CSS
 */
export const BREAKPOINTS = Object.freeze({
  xs: 0,    // smartphone (< 640px)
  sm: 640,  // tablet (≥ 640px)
  md: 1024, // laptop (≥ 1024px)
  lg: 1280, // desktop (≥ 1280px)
  xl: 1536, // large screen (≥ 1536px)
});

/**
 * Tailles d'écran disponibles
 */
export const SCREEN_SIZES = Object.freeze(["xs", "sm", "md", "lg", "xl"]);

/**
 * Types de cellules disponibles dans les tableaux
 */
export const CELL_TYPES = Object.freeze([
  "text",      // Texte simple
  "badge",     // Badge coloré
  "number",    // Nombre formaté
  "image",     // Image (thumb)
  "icon",      // Icône seule
  "bool",      // Booléen (icône ou badge)
  "date",      // Date formatée
  "link",      // Lien cliquable
  "route",     // Lien vers page de détail
  "routeExternal", // Lien externe
  "form",      // Champ éditable dans le tableau
]);

/**
 * Types de champs de formulaire disponibles
 */
export const FORM_TYPES = Object.freeze([
  "text",      // Champ texte
  "textarea",  // Zone de texte multiligne
  "select",    // Liste déroulante
  "checkbox",  // Case à cocher
  "number",    // Champ numérique
  "date",      // Sélecteur de date
  "file",      // Upload de fichier
]);

/**
 * Groupes de champs recommandés pour organiser les formulaires
 */
export const RECOMMENDED_GROUPS = Object.freeze({
  "Informations générales": "Champs de base (nom, description, etc.)",
  Métier: "Champs métier (type, rareté, niveau, etc.)",
  Statut: "Champs de statut (visible, utilisable, auto-update, etc.)",
  Métadonnées: "Champs techniques (prix, poids, version, etc.)",
  Contenu: "Champs de contenu (description, image, etc.)",
  Image: "Champs liés aux images",
  Caractéristiques: "Champs de caractéristiques (pour créatures, monstres, etc.)",
});

/**
 * Modes d'affichage disponibles
 */
export const DISPLAY_MODES = Object.freeze({
  text: "Affichage texte simple",
  route: "Affichage comme lien cliquable (vers la page de détail)",
  routeExternal: "Affichage comme lien externe",
  badge: "Affichage sous forme de badge coloré",
  thumb: "Affichage sous forme de miniature (pour images)",
  boolIcon: "Affichage booléen sous forme d'icône",
  boolBadge: "Affichage booléen sous forme de badge",
  dateShort: "Affichage date courte",
  dateTime: "Affichage date + heure",
});

/**
 * Formats de champs disponibles
 */
export const FIELD_FORMATS = Object.freeze({
  text: "Texte simple",
  number: "Nombre",
  bool: "Booléen",
  date: "Date",
  image: "Image (URL)",
  link: "Lien externe",
  enum: "Énumération (select)",
});
