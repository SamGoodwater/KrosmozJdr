/**
 * Filtre CSS d’icône de sort (sorts élémentaires Dofus devenus basiques).
 *
 * - `null` / absent : image d’origine
 * - `{ preset: "beige"|"violet" }` : préréglage (les valeurs restent dans le code)
 * - `{ preset: "custom", grayscale, sepia, hue, saturate, brightness, contrast }` : libre
 *
 * @example
 * spellImageFilterCss({ image_filter: { preset: "beige" } })
 * // "grayscale(1) sepia(0.45) brightness(1.05) contrast(0.95)"
 */

/** @typedef {{ preset?: string, grayscale?: number, sepia?: number, hue?: number, saturate?: number, brightness?: number, contrast?: number }|null|undefined} SpellImageFilter */

/**
 * Valeurs numériques d’un préréglage (ordre CSS : grayscale → sepia → hue → saturate → brightness → contrast).
 * @type {Readonly<Record<"beige"|"violet", Required<Omit<{grayscale:number,sepia:number,hue:number,saturate:number,brightness:number,contrast:number}, never>>>>}
 */
export const SPELL_IMAGE_FILTER_PRESETS = Object.freeze({
    beige: Object.freeze({
        grayscale: 1,
        sepia: 0.45,
        hue: 0,
        saturate: 1,
        brightness: 1.05,
        contrast: 0.95,
    }),
    violet: Object.freeze({
        grayscale: 1,
        sepia: 1,
        hue: 220,
        saturate: 0.55,
        brightness: 1,
        contrast: 1,
    }),
});

/** Seuil de saturation moyenne (0–1) pour la suggestion d’édition. */
export const SPELL_IMAGE_SATURATION_HINT_THRESHOLD = 0.12;

/**
 * @param {unknown} entity
 * @returns {SpellImageFilter}
 */
export function readSpellImageFilter(entity) {
    if (!entity || typeof entity !== "object") return null;
    const raw =
        entity.imageFilter ??
        entity.image_filter ??
        entity._data?.image_filter ??
        entity._data?.imageFilter ??
        null;
    if (raw == null || typeof raw !== "object" || Array.isArray(raw)) return null;
    return /** @type {SpellImageFilter} */ (raw);
}

/**
 * @param {number|null|undefined} n
 * @param {number} min
 * @param {number} max
 * @param {number} fallback
 * @returns {number}
 */
function clamp(n, min, max, fallback) {
    const v = Number(n);
    if (!Number.isFinite(v)) return fallback;
    return Math.min(max, Math.max(min, v));
}

/**
 * Résout les paramètres numériques à appliquer (préréglage ou custom).
 *
 * @param {SpellImageFilter} filter
 * @returns {{ grayscale: number, sepia: number, hue: number, saturate: number, brightness: number, contrast: number }|null}
 */
export function resolveSpellImageFilterParams(filter) {
    if (!filter || typeof filter !== "object") return null;
    const preset = filter.preset != null ? String(filter.preset) : "";
    if (preset === "beige" || preset === "violet") {
        return { ...SPELL_IMAGE_FILTER_PRESETS[preset] };
    }
    if (preset !== "custom") return null;
    return {
        grayscale: clamp(filter.grayscale, 0, 2, 1),
        sepia: clamp(filter.sepia, 0, 2, 0.45),
        hue: clamp(filter.hue, 0, 360, 0),
        saturate: clamp(filter.saturate, 0, 3, 1),
        brightness: clamp(filter.brightness, 0, 2, 1.05),
        contrast: clamp(filter.contrast, 0, 2, 0.95),
    };
}

/**
 * @param {{ grayscale: number, sepia: number, hue: number, saturate: number, brightness: number, contrast: number }} params
 * @returns {string}
 */
export function paramsToCssFilter(params) {
    if (!params) return "";
    const parts = [
        `grayscale(${params.grayscale})`,
        `sepia(${params.sepia})`,
        `hue-rotate(${params.hue}deg)`,
        `saturate(${params.saturate})`,
        `brightness(${params.brightness})`,
        `contrast(${params.contrast})`,
    ];
    return parts.join(" ");
}

/**
 * @param {object|null|undefined} entityOrFilter - Sort ou objet `image_filter`
 * @returns {string}
 */
export function spellImageFilterCss(entityOrFilter) {
    const filter =
        entityOrFilter &&
        typeof entityOrFilter === "object" &&
        ("preset" in entityOrFilter ||
            "grayscale" in entityOrFilter ||
            "image_filter" in entityOrFilter ||
            "imageFilter" in entityOrFilter ||
            "_data" in entityOrFilter)
            ? "preset" in entityOrFilter || "grayscale" in entityOrFilter
                ? /** @type {SpellImageFilter} */ (entityOrFilter)
                : readSpellImageFilter(entityOrFilter)
            : readSpellImageFilter(entityOrFilter);
    const params = resolveSpellImageFilterParams(filter);
    return params ? paramsToCssFilter(params) : "";
}

/**
 * @param {object|null|undefined} entityOrFilter
 * @returns {{ filter: string }|undefined}
 */
export function spellImageFilterStyle(entityOrFilter) {
    const css = spellImageFilterCss(entityOrFilter);
    return css ? { filter: css } : undefined;
}

/**
 * Saturation moyenne (0–1) d’une ImageData (échantillonnage pour rester rapide).
 *
 * @param {ImageData} imageData
 * @param {number} [step=4]
 * @returns {number}
 */
export function averageSaturationFromImageData(imageData, step = 4) {
    if (!imageData?.data?.length) return 0;
    const data = imageData.data;
    const stride = Math.max(1, Math.floor(Number(step) || 4)) * 4;
    let sum = 0;
    let count = 0;
    for (let i = 0; i < data.length; i += stride) {
        const a = data[i + 3];
        if (a < 16) continue;
        const r = data[i] / 255;
        const g = data[i + 1] / 255;
        const b = data[i + 2] / 255;
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        const sat = max === 0 ? 0 : (max - min) / max;
        sum += sat;
        count += 1;
    }
    return count === 0 ? 0 : sum / count;
}

/**
 * Le sort est « basique » au sens élément : pas d’élément ou Neutre seul.
 *
 * @param {object|null|undefined} entity
 * @returns {boolean}
 */
export function spellLooksElementNeutral(entity) {
    if (!entity || typeof entity !== "object") return true;
    const raw =
        entity.element ??
        entity._data?.element ??
        null;
    if (raw == null || raw === "" || raw === 0 || raw === "0") return true;
    const n = Number(raw);
    if (!Number.isFinite(n)) return true;
    // Bitmask : Neutre = 1 (bit 0). Si seul Neutre (ou 0), on considère basique.
    return n === 0 || n === 1;
}

/**
 * @param {number} saturation
 * @param {number} [threshold]
 * @returns {boolean}
 */
export function shouldHintBeigeFilter(saturation, threshold = SPELL_IMAGE_SATURATION_HINT_THRESHOLD) {
    return Number(saturation) > Number(threshold);
}
