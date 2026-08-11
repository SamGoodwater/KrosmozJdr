/**
 * Extrait le libellé affichable des hyperliens Ankama/DofusDB.
 * Format : `{{spell,32891,1::Évadé}}` → `Évadé`.
 *
 * @param {string|null|undefined} text
 * @returns {string}
 * @example
 * toDisplayLabel('{{spell,32891,1::Évadé}}'); // 'Évadé'
 */
export function toDisplayLabel(text) {
    if (text == null) {
        return "";
    }
    let out = String(text).trim();
    if (out === "" || !out.includes("{{")) {
        return out;
    }

    const re = /\{\{[^{}]*?::((?:(?!\}\}).)*?)\}\}/gu;
    let previous = null;
    let guard = 0;
    while (previous !== out && guard < 16) {
        previous = out;
        out = out.replace(re, "$1");
        guard += 1;
    }

    return out.trim();
}
