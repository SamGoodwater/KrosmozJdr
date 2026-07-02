/**
 * Validation de la notation de zone Krosmoz (alignée avec App\Support\AreaNotation).
 *
 * @see docs/features/effects/README.md
 */

/** Message d’aide pour les champs zone (UI). */
export const AREA_NOTATION_HELP =
    'Formes : point ; line-1xL (L≥1) ; cross-a-b et circle-a-b (a≤b) ; rect-WxH (W,H≥1) ; shape-ID ou shape-ID-p1-p2 (ID≥1).';

/**
 * Chaîne vide ou uniquement des espaces = valide (champ optionnel).
 *
 * @param {string|null|undefined} area
 * @returns {boolean}
 */
export function isValidAreaNotation(area) {
    if (area == null) return true;
    if (typeof area !== 'string') return false;
    const s = area.trim();
    if (s === '') return true;
    if (s.length > 64) return false;

    if (s === 'point') return true;

    let m = /^line-1x(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1;

    m = /^cross-(\d+)-(\d+)$/.exec(s);
    if (m) {
        const a = parseInt(m[1], 10);
        const b = parseInt(m[2], 10);
        return a <= b;
    }

    m = /^circle-(\d+)-(\d+)$/.exec(s);
    if (m) {
        const a = parseInt(m[1], 10);
        const b = parseInt(m[2], 10);
        return a <= b;
    }

    m = /^rect-(\d+)x(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1 && parseInt(m[2], 10) >= 1;

    m = /^shape-(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1;

    m = /^shape-(\d+)-(\d+)-(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1;

    return false;
}

/**
 * @param {string|null|undefined} raw
 * @returns {string}
 */
export function normalizeAreaInput(raw) {
    if (raw == null || typeof raw !== 'string') return '';
    return raw.trim();
}
