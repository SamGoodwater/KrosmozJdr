/**
 * Registre global pour Ctrl+S / Cmd+S : le dernier contexte enregistré (ex. modal) reçoit l’événement.
 *
 * @description
 * Un seul listener `window` en phase capture évite les doublons et garantit qu’un seul handler
 * s’exécute (celui au sommet de la pile). Appeler la fonction retournée au démontage ou à la
 * désactivation du formulaire.
 *
 * @example
 * const unregister = registerSaveShortcut(() => submit());
 * onUnmounted(unregister);
 *
 * @param {(event: KeyboardEvent) => void} handler
 * @returns {() => void} Désenregistrement
 */
const stack = [];
let idSeq = 0;
let listenerInstalled = false;

function onKeyDown(e) {
    if (!(e.ctrlKey || e.metaKey) || String(e.key || "").toLowerCase() !== "s") {
        return;
    }
    if (stack.length === 0) {
        return;
    }
    e.preventDefault();
    e.stopImmediatePropagation();
    const top = stack[stack.length - 1];
    try {
        top.fn(e);
    } catch (err) {
        console.error("[saveShortcutRegistry]", err);
    }
}

function ensureListener() {
    if (listenerInstalled || typeof window === "undefined") {
        return;
    }
    listenerInstalled = true;
    window.addEventListener("keydown", onKeyDown, true);
}

/**
 * @param {(event: KeyboardEvent) => void} handler
 * @returns {() => void}
 */
export function registerSaveShortcut(handler) {
    if (typeof handler !== "function") {
        return () => {};
    }
    ensureListener();
    const id = ++idSeq;
    stack.push({ id, fn: handler });
    return () => {
        const i = stack.findIndex((x) => x.id === id);
        if (i !== -1) {
            stack.splice(i, 1);
        }
    };
}
