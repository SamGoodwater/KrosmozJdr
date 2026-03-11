/**
 * Référentiel zones d'impact (Effect, Spell) — source de vérité frontend.
 *
 * Aligné avec App\Support\AreaConstants.
 * Icônes dans storage/app/public/images/icons/areas/ (point.svg, line.svg, etc.)
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 */

const AREA_ICON_BASE = 'icons/areas';

/** Formes de base supportées. */
export const AREA_SHAPES = Object.freeze(['point', 'line', 'cross', 'circle', 'rect']);

/** Icônes par forme (point, line, cross, circle, rect). */
export const AREA_SHAPE_ICONS = Object.freeze({
  point: `${AREA_ICON_BASE}/point.svg`,
  line: `${AREA_ICON_BASE}/line.svg`,
  cross: `${AREA_ICON_BASE}/cross.svg`,
  circle: `${AREA_ICON_BASE}/circle.svg`,
  rect: `${AREA_ICON_BASE}/rect.svg`,
});

/** Libellés par forme. */
export const AREA_SHAPE_LABELS = Object.freeze({
  point: 'Point',
  line: 'Ligne',
  cross: 'Croix',
  circle: 'Cercle',
  rect: 'Rectangle',
});

/**
 * Extrait le nom de forme depuis une notation (point, line-1x9, circle-0-2, etc.).
 *
 * @param {string|null} area - Notation zone (ex: "line-1x9", "circle-0-2")
 * @returns {string|null} Forme (point, line, cross, circle, rect) ou null
 */
export function getAreaShape(area) {
  if (area == null || typeof area !== 'string') return null;
  const trimmed = area.trim();
  if (!trimmed) return null;

  const dashIdx = trimmed.indexOf('-');
  const shape = dashIdx >= 0 ? trimmed.slice(0, dashIdx) : trimmed;

  return AREA_SHAPES.includes(shape) ? shape : shape === 'shape' ? 'point' : null;
}

/**
 * Retourne le chemin de l'icône pour une notation.
 *
 * @param {string|null} area - Notation zone
 * @returns {string} Chemin icône (ex: "icons/areas/point.svg")
 */
export function getAreaIcon(area) {
  const shape = getAreaShape(area);
  return shape ? AREA_SHAPE_ICONS[shape] : `${AREA_ICON_BASE}/point.svg`;
}

/**
 * Libellé affiché pour une forme.
 *
 * @param {string|null} shape - Nom de forme
 * @returns {string}
 */
export function getAreaShapeLabel(shape) {
  return AREA_SHAPE_LABELS[shape ?? ''] ?? String(shape ?? '');
}
