/**
 * Indique si les overlays « hover » (tooltips) sont utilisables sans sticky tactile.
 *
 * Sur pointeur grossier / sans vrai hover, `mouseenter` ouvre et ne se ferme jamais —
 * préférer alors le mode clic + fermeture hors panneau.
 *
 * @returns {boolean}
 * @example
 * if (canUseHoverOverlay()) { /* hover OK *\/ }
 */
export function canUseHoverOverlay() {
    if (typeof window === "undefined" || typeof window.matchMedia !== "function") {
        return true;
    }
    return window.matchMedia("(hover: hover) and (pointer: fine)").matches;
}
