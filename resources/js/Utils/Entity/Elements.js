/**
 * Référentiel éléments (Spell, Capability) — masque 7 bits, aligné avec App\Support\ElementBitmask.
 *
 * Primaires : 0 Neutre, 1 Terre, 2 Feu, 3 Air, 4 Eau, 5 Sagesse, 6 Vitalité.
 * Icônes : storage/app/public/images/icons/caracteristics/ (URL absolue `/storage/...`).
 */

/** Base publique (évite les URL relatives type `/entities/icons/...` sur les pages entités). */
const ELEMENT_ICON_BASE = '/storage/images/icons/caracteristics';

export const ELEMENT_PRIMARY_COUNT = 7;

/** @type {Readonly<Record<number, string>>} */
export const ELEMENT_PRIMARY_ICONS = Object.freeze({
  0: `${ELEMENT_ICON_BASE}/neutral.webp`,
  1: `${ELEMENT_ICON_BASE}/earth.webp`,
  2: `${ELEMENT_ICON_BASE}/fire.webp`,
  3: `${ELEMENT_ICON_BASE}/air.webp`,
  4: `${ELEMENT_ICON_BASE}/water.webp`,
  5: `${ELEMENT_ICON_BASE}/wisdom.webp`,
  6: `${ELEMENT_ICON_BASE}/vitality.webp`,
});

/** Tokens Tailwind (primaire seul). */
export const ELEMENT_PRIMARY_COLORS = Object.freeze({
  0: 'slate-500',
  1: 'amber-700',
  2: 'red-600',
  3: 'emerald-600',
  4: 'blue-600',
  5: 'violet-500',
  6: 'lime-600',
});

/** Variables CSS thème (dégradés ElementDisplay). */
export const ELEMENT_PRIMARY_CSS_VARS = Object.freeze({
  0: 'var(--color-slate-500)',
  1: 'var(--color-amber-700)',
  2: 'var(--color-red-600)',
  3: 'var(--color-emerald-600)',
  4: 'var(--color-blue-600)',
  5: 'var(--color-violet-500)',
  6: 'var(--color-lime-600)',
});

/** @type {Readonly<Record<number, string>>} */
export const ELEMENT_PRIMARY_LABELS = Object.freeze({
  0: 'Neutre',
  1: 'Terre',
  2: 'Feu',
  3: 'Air',
  4: 'Eau',
  5: 'Sagesse',
  6: 'Vitalité',
});

export const ELEMENT_MASK_MAX = 127;

/** Ancien code 0–29 → indices primaires (0–4 seulement). @type {Readonly<Record<number, number[]>>} */
const LEGACY_CODE_TO_PRIMARIES = Object.freeze({
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

/**
 * @param {number[]} indices
 * @returns {number}
 */
export function primariesToMask(indices) {
  let m = 0;
  for (const i of indices || []) {
    const n = Number(i);
    if (n >= 0 && n <= 6) {
      m |= 1 << n;
    }
  }
  return m & ELEMENT_MASK_MAX;
}

/**
 * @param {number} mask
 * @returns {number[]}
 */
export function maskToPrimaries(mask) {
  const m = Number(mask) & ELEMENT_MASK_MAX;
  const out = [];
  for (let i = 0; i <= 6; i += 1) {
    if (m & (1 << i)) {
      out.push(i);
    }
  }
  return out;
}

/**
 * @param {number} legacyCode
 * @returns {number}
 */
export function legacyCodeToMask(legacyCode) {
  const c = Math.max(0, Math.min(29, Number(legacyCode)));
  const primaries = LEGACY_CODE_TO_PRIMARIES[c] ?? [0];
  return primariesToMask(primaries);
}

/**
 * Valeur BDD / API → masque (migre 0–29 si besoin).
 *
 * @param {unknown} raw
 * @returns {number}
 */
export function normalizeElementStorageValue(raw) {
  const n = typeof raw === 'string' ? parseInt(raw, 10) : Number(raw);
  if (!Number.isFinite(n)) {
    return 0;
  }
  if (n >= 0 && n <= 29) {
    return legacyCodeToMask(n);
  }
  return n & ELEMENT_MASK_MAX;
}

/**
 * Libellé combinaison (ex. « Neutre-Terre »).
 *
 * @param {unknown} raw — masque ou ancien code
 * @returns {string|null} null si masque 0
 */
export function getElementLabel(raw) {
  const mask = normalizeElementStorageValue(raw);
  if (mask === 0) {
    return null;
  }
  const primaries = maskToPrimaries(mask);
  return primaries.map((i) => ELEMENT_PRIMARY_LABELS[i] ?? '?').join('-');
}

/**
 * @param {unknown} raw
 * @returns {number[]}
 */
export function getElementPrimaries(raw) {
  return maskToPrimaries(normalizeElementStorageValue(raw));
}

/**
 * Indices primaires → valeur à persister (masque).
 *
 * @param {number[]} primaries
 * @returns {number}
 */
export function primariesToElementValue(primaries) {
  const sorted = [...new Set((primaries || []).map((n) => Number(n)).filter((n) => n >= 0 && n <= 6))].sort(
    (a, b) => a - b,
  );
  return primariesToMask(sorted);
}

/** Options primaires pour UI multi-case (sort). */
export const SPELL_PRIMARY_ELEMENT_OPTIONS = Object.freeze(
  Object.keys(ELEMENT_PRIMARY_LABELS).map((k) => ({
    value: Number(k),
    label: ELEMENT_PRIMARY_LABELS[Number(k)],
  })),
);

/**
 * @deprecated Liste 0–29 obsolète ; préférer getElementLabel / filtres API.
 */
export const ELEMENT_OPTIONS = Object.freeze(
  Array.from({ length: 30 }, (_, v) => ({
    value: v,
    label: getElementLabel(legacyCodeToMask(v)) ?? String(v),
  })),
);

/**
 * @param {number} primaryIndex 0–6
 */
export function getElementIcon(primaryIndex) {
  const v = Number(primaryIndex);
  return ELEMENT_PRIMARY_ICONS[v] ?? ELEMENT_PRIMARY_ICONS[0];
}

/**
 * @param {unknown} raw — masque ou ancien code (utilise 1er primaire pour icône)
 */
export function getElementIconForValue(raw) {
  const primaries = getElementPrimaries(raw);
  const first = primaries[0] ?? 0;
  return getElementIcon(first);
}

/**
 * @param {number} primaryIndex
 * @returns {string}
 */
export function getElementColor(primaryIndex) {
  const v = Number(primaryIndex);
  return ELEMENT_PRIMARY_COLORS[v] ?? 'zinc-500';
}

/**
 * Style inline pour badge dégradé (masque multi-primaires).
 *
 * @param {unknown} raw
 * @returns {{ background?: string, boxShadow?: string }}
 */
export function getElementBadgeStyle(raw) {
  const mask = normalizeElementStorageValue(raw);
  const primaries = maskToPrimaries(mask);
  if (primaries.length === 0) {
    return {};
  }
  const cols = primaries.map((i) => ELEMENT_PRIMARY_CSS_VARS[i] ?? 'var(--color-slate-500)');
  const first = cols[0];
  let background;
  if (cols.length === 1) {
    background = first;
  } else {
    const stops = cols.map((c, idx) => {
      const pct = cols.length === 1 ? 0 : (idx / (cols.length - 1)) * 100;
      return `${c} ${pct}%`;
    });
    background = `linear-gradient(90deg, ${stops.join(', ')})`;
  }
  return {
    background,
    boxShadow: `0 0 10px 2px color-mix(in srgb, ${first} 40%, transparent)`,
  };
}

/**
 * @returns {Array<{ value: number, label: string }>}
 */
export function getElementOptions() {
  const out = [];
  for (let m = 1; m <= ELEMENT_MASK_MAX; m += 1) {
    const label = getElementLabel(m);
    if (label) {
      out.push({ value: m, label });
    }
  }
  return out;
}
