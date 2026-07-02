/**
 * Déplacement en cases : règle Krosmoz 1 case = 1,5 m.
 *
 * @see docs/features/effects/README.md
 */

/** Mètres équivalents à une case (JDR Krosmoz). */
export const METERS_PER_CASE = 1.5;

/**
 * Formate un nombre pour l’affichage français, au plus une décimale (sans forcer les .0 inutiles sauf besoin).
 *
 * @param {number} n
 * @returns {string}
 */
export function formatNumberFrOneDecimal(n) {
    if (!Number.isFinite(n)) {
        return "";
    }
    return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 1,
    }).format(n);
}

/**
 * Distance en mètres pour un nombre de cases (arrondi à 1 décimale pour l’affichage).
 *
 * @param {number} cases
 * @returns {number}
 */
export function metersFromCases(cases) {
    if (!Number.isFinite(cases)) {
        return NaN;
    }
    return Math.round(cases * METERS_PER_CASE * 10) / 10;
}

/**
 * Indique si la chaîne est un nombre littéral (entier ou décimal), sans dés ni variable.
 *
 * @param {string} s
 * @returns {boolean}
 */
export function isLiteralDisplacementNumber(s) {
    const t = String(s ?? "")
        .trim()
        .replace(/\s/g, "");
    if (t === "") {
        return false;
    }
    if (/[dD]/.test(t) || t.includes("[") || /[a-zA-Z]/.test(t)) {
        return false;
    }
    return /^-?\d+([.,]\d+)?$/.test(t);
}

/**
 * Parse un nombre littéral (virgule ou point).
 *
 * @param {string} s
 * @returns {number|null}
 */
export function parseLiteralDisplacementNumber(s) {
    if (!isLiteralDisplacementNumber(s)) {
        return null;
    }
    const n = parseFloat(String(s).trim().replace(/\s/g, "").replace(",", "."));
    return Number.isFinite(n) ? n : null;
}

/**
 * Libellé complet pour l’UI : « X case(s) (Y m) » si nombre pur, sinon « formule cases » sans mètres.
 *
 * @param {string|null|undefined} raw — valeur affichée (résolue ou formule)
 * @returns {string}
 */
export function formatDisplacementForDisplay(raw) {
    const s = raw != null ? String(raw).trim() : "";
    if (s === "") {
        return "";
    }
    const n = parseLiteralDisplacementNumber(s);
    if (n !== null) {
        const m = metersFromCases(n);
        const casesLabel = Math.abs(n - 1) < 1e-9 ? "case" : "cases";
        return `${formatNumberFrOneDecimal(n)} ${casesLabel} (${formatNumberFrOneDecimal(m)} m)`;
    }
    return `${s} cases`;
}

/**
 * Aperçu mètres sous l’input éditeur : uniquement pour un littéral numérique saisi seul.
 *
 * @param {string|null|undefined} cellsFormula
 * @returns {string|null} texte court ou null si pas d’aperçu
 */
export function previewMetersFromCellsFormula(cellsFormula) {
    const s = cellsFormula != null ? String(cellsFormula).trim() : "";
    if (s === "") {
        return null;
    }
    const n = parseLiteralDisplacementNumber(s);
    if (n === null) {
        return null;
    }
    const m = metersFromCases(n);
    return `≈ ${formatNumberFrOneDecimal(m)} m (${formatNumberFrOneDecimal(n)} case${Math.abs(n - 1) < 1e-9 ? "" : "s"} × ${formatNumberFrOneDecimal(METERS_PER_CASE)} m)`;
}
