/**
 * Text truncation helpers (UI)
 *
 * @description
 * Convention unique pour tronquer sur **une ligne** via les classes SCSS `k-truncate*`.
 * - On choisit un preset (`context`) et une échelle (`scale`) pour obtenir une classe width cohérente.
 * - Le rendu final utilise toujours `k-truncate` + `k-truncate-{xs|sm|md|lg|xl}`.
 *
 * @example
 * getTruncateClass({ context: 'table', scale: 'sm' }) // "k-truncate k-truncate-sm"
 * getTruncateClass({ context: 'minimal', scale: 'lg' }) // "k-truncate k-truncate-md"
 */

/**
 * @typedef {'table'|'minimal'|'compact'|'extended'|'text'} TruncateContext
 * @typedef {'xs'|'sm'|'md'|'lg'|'xl'} TruncateScale
 */

/** @type {Record<TruncateContext, Record<TruncateScale, TruncateScale>>} */
const TRUNCATE_MAP = {
  // Minimal: on tronque vite
  minimal: { xs: "xs", sm: "xs", md: "sm", lg: "md", xl: "lg" },
  // Table: neutre
  table: { xs: "xs", sm: "sm", md: "md", lg: "lg", xl: "xl" },
  // Compact: un peu plus permissif
  compact: { xs: "sm", sm: "md", md: "lg", lg: "xl", xl: "xl" },
  // Extended/Text: le plus permissif
  extended: { xs: "md", sm: "lg", md: "xl", lg: "xl", xl: "xl" },
  text: { xs: "md", sm: "lg", md: "xl", lg: "xl", xl: "xl" },
};

/**
 * @param {{ context?: TruncateContext, scale?: TruncateScale }} [opts]
 * @returns {string}
 */
export function getTruncateClass(opts = {}) {
  const context = /** @type {TruncateContext} */ (opts.context || "table");
  const scale = /** @type {TruncateScale} */ (opts.scale || "md");
  const mapped = TRUNCATE_MAP?.[context]?.[scale] || "md";
  return `k-truncate k-truncate-${mapped}`;
}

/**
 * Mappe une "size" sémantique (small|normal|large) vers une échelle (xs..xl).
 *
 * @param {'small'|'normal'|'large'|string} size
 * @returns {TruncateScale}
 */
export function sizeToTruncateScale(size) {
  if (size === "small") return "sm";
  if (size === "large") return "lg";
  return "md";
}


