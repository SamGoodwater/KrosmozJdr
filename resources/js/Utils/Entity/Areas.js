/**
 * Référentiel zones d'impact (Effect, Spell) — source de vérité frontend.
 *
 * Aligné avec App\Support\AreaConstants.
 * Icônes dans storage/app/public/images/icons/areas/ (point.svg, line.svg, etc.)
 *
 * @see docs/features/effects/README.md
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

/**
 * Libellé court pour affichage tabulaire (icône + texte compact).
 *
 * @param {string|null|undefined} area - Notation zone
 * @returns {string}
 */
export function getAreaShortLabel(area) {
  if (area == null || typeof area !== 'string') return '';
  const raw = area.trim();
  if (!raw) return '';

  const shape = getAreaShape(area);
  if (!shape) {
    if (raw.startsWith('shape-')) {
      return raw.length > 14 ? `${raw.slice(0, 12)}…` : raw;
    }
    return raw.length > 14 ? `${raw.slice(0, 12)}…` : raw;
  }

  const dashIdx = raw.indexOf('-');
  const params = dashIdx >= 0 ? raw.slice(dashIdx + 1) : '';

  switch (shape) {
    case 'point':
      return '1';
    case 'line': {
      const m = /^1x(\d+)$/.exec(params);
      return m ? `L${m[1]}` : params ? `L ${params}` : 'L';
    }
    case 'cross': {
      const m = /^(\d+)-(\d+)$/.exec(params);
      return m ? `×${m[1]}-${m[2]}` : params ? `× ${params}` : '×';
    }
    case 'circle': {
      const m = /^(\d+)-(\d+)$/.exec(params);
      return m ? `○${m[1]}-${m[2]}` : params ? `○ ${params}` : '○';
    }
    case 'rect': {
      const m = /^(\d+)x(\d+)$/.exec(params);
      return m ? `${m[1]}×${m[2]}` : params ? `▭ ${params}` : '▭';
    }
    default:
      return raw;
  }
}

/**
 * Ligne récapitulative pour infobulle (forme lisible + notation complète).
 *
 * @param {string|null|undefined} area
 * @returns {string}
 */
export function getAreaSummaryLine(area) {
  if (area == null || typeof area !== 'string') return '';
  const raw = area.trim();
  if (!raw) return '';
  const shape = getAreaShape(area);
  const name = getAreaShapeLabel(shape);
  return name ? `${name} — ${raw}` : raw;
}

/**
 * Libellé lisible joueur (fiches sort, journal d’effets) — « cible unique », « ligne de N cases », etc.
 *
 * @param {string|null|undefined} area
 * @returns {string}
 */
export function getAreaHumanReadable(area) {
  if (area == null || typeof area !== 'string') return '';
  const raw = area.trim();
  if (!raw) return '';

  if (raw === 'point') {
    return 'Cible unique';
  }

  const shape = getAreaShape(area);
  const dashIdx = raw.indexOf('-');
  const params = dashIdx >= 0 ? raw.slice(dashIdx + 1) : '';

  if (shape === 'line') {
    const m = /^1x(\d+)$/.exec(params);
    if (m) {
      const n = parseInt(m[1], 10);
      return `Ligne de ${n} case${n > 1 ? 's' : ''}`;
    }
    return params ? `Ligne (${params})` : 'Ligne';
  }

  if (shape === 'cross') {
    const m = /^(\d+)-(\d+)$/.exec(params);
    if (m) {
      return `Croix, portées ${m[1]} à ${m[2]} cases`;
    }
    return params ? `Croix (${params})` : 'Croix';
  }

  if (shape === 'circle') {
    const m = /^(\d+)-(\d+)$/.exec(params);
    if (m) {
      return `Cercle, rayons ${m[1]} à ${m[2]} cases`;
    }
    return params ? `Cercle (${params})` : 'Cercle';
  }

  if (shape === 'rect') {
    const m = /^(\d+)x(\d+)$/.exec(params);
    if (m) {
      return `Rectangle ${m[1]}×${m[2]} cases`;
    }
    return params ? `Rectangle (${params})` : 'Rectangle';
  }

  if (raw.startsWith('shape-')) {
    return `Forme DofusDB : ${raw}`;
  }

  return raw;
}
