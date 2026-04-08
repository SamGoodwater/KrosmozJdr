/**
 * Cible pour `<Teleport>` des tooltips / hover cards (Floating UI).
 *
 * @description
 * Un `<dialog>` ouvert avec `showModal()` vit dans la **top layer** du navigateur : tout ce qui est
 * téléporté vers `document.body` avec un `z-index` élevé reste **derrière** le modal.
 * En téléportant **à l’intérieur** du `dialog` parent (comme pour les menus {@link Dropdown}),
 * le survol s’affiche au-dessus du contenu du modal (backdrop / box gérés par le même dialog).
 *
 * Ordre visuel recommandé : **layout** < **modals** (pile native : dernier `showModal` au-dessus) <
 * **tooltips** (z-index local élevé dans le dialog ou sur `body`).
 *
 * @param {HTMLElement|null|undefined} triggerEl - Nœud du déclencheur (ex. ref du wrapper)
 * @returns {HTMLElement|string} `dialog` ouvert parent, sinon `document.body`, ou `'body'` sans `document`
 *
 * @example
 * const teleportTarget = computed(() => {
 *   if (!isOpen.value || typeof document === 'undefined') return 'body';
 *   return resolveTooltipTeleportTarget(triggerRef.value);
 * });
 */
export function resolveTooltipTeleportTarget(triggerEl) {
    if (typeof document === "undefined") {
        return "body";
    }
    if (!triggerEl || typeof triggerEl.closest !== "function") {
        return document.body;
    }
    const dialogEl = triggerEl.closest("dialog");
    if (dialogEl instanceof HTMLDialogElement && dialogEl.open) {
        return dialogEl;
    }
    return document.body;
}
