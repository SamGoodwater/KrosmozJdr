/**
 * size-utils
 *
 * @description
 * Helpers de manipulation des tailles DaisyUI (xs/sm/md/lg/xl) sous forme de pas (step).
 * Utile pour dériver des tailles "discrètes" (ex: toolbar actions en size - 1).
 *
 * @example
 * shiftUiSize('md', -1) // 'sm'
 * shiftUiSize('xs', -1) // 'xs' (clamp)
 * shiftUiSize('lg', 2)  // 'xl' (clamp)
 *
 * @param {string} size - Taille de base ('xs'|'sm'|'md'|'lg'|'xl')
 * @param {number} delta - Décalage en nombre de pas (ex: -1, +1)
 * @param {Array<string>} [scale] - Échelle optionnelle (par défaut xs->xl)
 * @returns {string} Taille ajustée (clampée dans l'échelle)
 */
export function shiftUiSize(size, delta = 0, scale = ["xs", "sm", "md", "lg", "xl"]) {
    const s = String(size || "md");
    const d = Number(delta);
    const safeDelta = Number.isFinite(d) ? d : 0;

    const idx = Math.max(0, scale.indexOf(s));
    const next = idx + safeDelta;
    const clamped = Math.max(0, Math.min(scale.length - 1, next));
    return scale[clamped] || "md";
}


