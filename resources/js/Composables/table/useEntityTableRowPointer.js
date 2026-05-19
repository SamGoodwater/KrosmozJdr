/**
 * useEntityTableRowPointer
 *
 * @description
 * Clics sur ligne de tableau : Ctrl/Meta = page Show, Alt = édition, sinon sélection.
 */

/** @param {MouseEvent} event */
export function isRowInteractiveTarget(event) {
    const el = event?.target;
    if (!el || typeof el.closest !== "function") return false;
    return Boolean(
        el.closest(
            'a,button,input,select,textarea,[role="button"],[role="link"],[contenteditable="true"],[data-no-row-select]',
        ),
    );
}

/**
 * @param {MouseEvent} event
 * @returns {'default'|'page'|'edit'}
 */
export function classifyRowPointerModifiers(event) {
    if (!event) return "default";
    if (event.ctrlKey || event.metaKey) return "page";
    if (event.altKey) return "edit";
    return "default";
}

/**
 * @param {(eventName:string, ...args:any[]) => void} emit
 * @param {object} row
 * @param {MouseEvent} event
 */
export function emitLineRowClick(emit, row, event) {
    emit("row-click", row, event);
}

/**
 * @param {(eventName:string, ...args:any[]) => void} emit
 * @param {object} row
 * @param {MouseEvent} event
 */
export function emitLineRowDblClick(emit, row, event) {
    if (!isRowInteractiveTarget(event)) {
        emit("row-dblclick", row);
    }
}
