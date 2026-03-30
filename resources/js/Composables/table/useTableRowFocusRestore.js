/**
 * Remet le focus sur le bloc ligne du tableau (`[data-table-row-focus]`).
 *
 * @param {string|number|null|undefined} rowId
 * @returns {void}
 */
export function focusTableRowById(rowId) {
    if (rowId === undefined || rowId === null) {
        return;
    }
    const safe = String(rowId);
    const sel =
        typeof CSS !== "undefined" && CSS.escape
            ? `[data-table-row-focus][data-row-id="${CSS.escape(safe)}"]`
            : `[data-table-row-focus][data-row-id="${safe.replace(/"/g, "")}"]`;
    const el = typeof document !== "undefined" ? document.querySelector(sel) : null;
    el?.focus?.();
}
