/**
 * Référentiel éléments (Spell, Capability) — source de vérité frontend.
 *
 * Aligné avec App\Support\ElementConstants (0-29).
 * Icônes dans storage/app/public/images/icons/caracteristics/
 */

const ELEMENT_ICON_BASE = 'icons/caracteristics';

/** Icônes pour les 5 éléments primaires (0=Neutre, 1=Terre, 2=Feu, 3=Air, 4=Eau). */
export const ELEMENT_PRIMARY_ICONS = Object.freeze({
  0: `${ELEMENT_ICON_BASE}/neutral.webp`,
  1: `${ELEMENT_ICON_BASE}/earth.webp`,
  2: `${ELEMENT_ICON_BASE}/fire.webp`,
  3: `${ELEMENT_ICON_BASE}/air.webp`,
  4: `${ELEMENT_ICON_BASE}/water.webp`,
});

/** Tokens Tailwind par élément primaire (color-shade pour usage inline). */
export const ELEMENT_PRIMARY_COLORS = Object.freeze({
  0: 'slate-500',
  1: 'amber-700',
  2: 'red-600',
  3: 'emerald-600',
  4: 'blue-600',
});

/** Valeur → liste des indices primaires (pour dégradés). Ex: 9 → [1,2] (Terre-Feu). */
const ELEMENT_TO_PRIMARIES = Object.freeze({
  0: [0],
  1: [1],
  2: [2],
  3: [3],
  4: [4],
  5: [0, 1],
  6: [0, 2],
  7: [0, 3],
  8: [0, 4],
  9: [1, 2],
  10: [1, 3],
  11: [1, 4],
  12: [2, 3],
  13: [2, 4],
  14: [3, 4],
  15: [0, 1, 2],
  16: [0, 1, 3],
  17: [0, 1, 4],
  18: [0, 2, 3],
  19: [0, 2, 4],
  20: [0, 3, 4],
  21: [1, 2, 3],
  22: [1, 2, 4],
  23: [1, 3, 4],
  24: [2, 3, 4],
  25: [0, 1, 2, 3],
  26: [0, 1, 2, 4],
  27: [0, 1, 3, 4],
  28: [0, 2, 3, 4],
  29: [0, 1, 2, 3, 4],
});

export const ELEMENT_OPTIONS = Object.freeze([
  { value: 0, label: 'Neutre' },
  { value: 1, label: 'Terre' },
  { value: 2, label: 'Feu' },
  { value: 3, label: 'Air' },
  { value: 4, label: 'Eau' },
  { value: 5, label: 'Neutre-Terre' },
  { value: 6, label: 'Neutre-Feu' },
  { value: 7, label: 'Neutre-Air' },
  { value: 8, label: 'Neutre-Eau' },
  { value: 9, label: 'Terre-Feu' },
  { value: 10, label: 'Terre-Air' },
  { value: 11, label: 'Terre-Eau' },
  { value: 12, label: 'Feu-Air' },
  { value: 13, label: 'Feu-Eau' },
  { value: 14, label: 'Air-Eau' },
  { value: 15, label: 'Neutre-Terre-Feu' },
  { value: 16, label: 'Neutre-Terre-Air' },
  { value: 17, label: 'Neutre-Terre-Eau' },
  { value: 18, label: 'Neutre-Feu-Air' },
  { value: 19, label: 'Neutre-Feu-Eau' },
  { value: 20, label: 'Neutre-Air-Eau' },
  { value: 21, label: 'Terre-Feu-Air' },
  { value: 22, label: 'Terre-Feu-Eau' },
  { value: 23, label: 'Terre-Air-Eau' },
  { value: 24, label: 'Feu-Air-Eau' },
  { value: 25, label: 'Neutre-Terre-Feu-Air' },
  { value: 26, label: 'Neutre-Terre-Feu-Eau' },
  { value: 27, label: 'Neutre-Terre-Air-Eau' },
  { value: 28, label: 'Neutre-Feu-Air-Eau' },
  { value: 29, label: 'Neutre-Terre-Feu-Air-Eau' },
]);

export function getElementLabel(value) {
  const opt = ELEMENT_OPTIONS.find((o) => o.value === Number(value));
  return opt?.label ?? null;
}

export function getElementIcon(value) {
  const v = Number(value);
  return ELEMENT_PRIMARY_ICONS[v] ?? ELEMENT_PRIMARY_ICONS[0];
}

/**
 * @param {number} value
 * @returns {string} Token Tailwind (ex: 'amber-700') ou 'zinc-500' par défaut
 */
export function getElementColor(value) {
  const v = Number(value);
  return ELEMENT_PRIMARY_COLORS[v] ?? 'zinc-500';
}

/**
 * Retourne les indices primaires pour une combinaison.
 * @param {number} value - Valeur élément 0-29
 * @returns {number[]}
 */
export function getElementPrimaries(value) {
  return ELEMENT_TO_PRIMARIES[Number(value)] ?? [0];
}

/**
 * Option pour select (Spell, Capability).
 */
export function getElementOptions() {
  return ELEMENT_OPTIONS.map(({ value, label }) => ({ value, label }));
}
