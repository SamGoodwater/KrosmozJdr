/**
 * Formate les deux bornes persistées en une portée compacte.
 *
 * @param {unknown} poMin
 * @param {unknown} poMax
 * @returns {string}
 *
 * @example formatPoRange('2', '6') // '2-6'
 */
export function formatPoRange(poMin, poMax) {
    const min = poMin == null ? '' : String(poMin).trim();
    const max = poMax == null ? '' : String(poMax).trim();

    if (!min && !max) return '';
    if (!min || !max || min === max) return min || max;
    return `${min}-${max}`;
}

/**
 * Convertit la saisie compacte en bornes persistables.
 * Les tirets à l'intérieur de crochets ne séparent pas les bornes.
 *
 * @param {unknown} value
 * @returns {{ po_min: string|null, po_max: string|null }}
 *
 * @example parsePoRange('4') // { po_min: '4', po_max: '4' }
 */
export function parsePoRange(value) {
    const raw = value == null ? '' : String(value).trim();
    if (!raw || /^[-–—]\s*$/.test(raw)) {
        return { po_min: null, po_max: null };
    }

    let bracketDepth = 0;
    let separator = -1;
    for (let i = 0; i < raw.length; i += 1) {
        const char = raw[i];
        if (char === '[') bracketDepth += 1;
        if (char === ']') bracketDepth = Math.max(0, bracketDepth - 1);
        if (bracketDepth === 0 && ['-', '–', '—'].includes(char)) {
            separator = i;
            break;
        }
    }

    if (separator < 0) {
        return { po_min: raw, po_max: raw };
    }

    const poMin = raw.slice(0, separator).trim();
    const poMax = raw.slice(separator + 1).trim();
    if (!poMin && !poMax) {
        return { po_min: null, po_max: null };
    }

    return {
        po_min: poMin || poMax || null,
        po_max: poMax || poMin || null,
    };
}
