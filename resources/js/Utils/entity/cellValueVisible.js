/**
 * Indique si une cellule scalaire / texte doit être affichée.
 * Exclut les chaînes vides, « — », et les valeurs parasites type `"false"` (imports JSON).
 *
 * @param {{ value?: unknown }|null|undefined} cell
 * @returns {boolean}
 */
export function isRenderableScalarCellValue(cell) {
    if (!cell) return false;
    const v = cell.value;
    if (v === null || v === undefined || v === false) return false;
    if (typeof v === "boolean") return v === true;
    const s = String(v).trim();
    if (s === "" || s === "—" || s === "-") return false;
    if (s.toLowerCase() === "false") return false;
    return true;
}
