/**
 * useEntityTableRowPointer
 *
 * @description
 * Clics sur ligne de tableau : Ctrl/Meta = affichage modal, Alt = édition modale, sinon sélection.
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
 * @returns {'default'|'view'|'edit'}
 */
export function classifyRowPointerModifiers(event) {
    if (!event) return "default";
    if (event.ctrlKey || event.metaKey) return "view";
    if (event.altKey) return "edit";
    return "default";
}
