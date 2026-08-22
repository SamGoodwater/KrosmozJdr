/**
 * Raccourcis clavier du tableau TanStack (implémentés dans `TanStackTable.vue` sur le conteneur `tableRootRef`).
 *
 * @description
 * Référence unique pour l’aide intégrée, la doc et `handleTableKeydown` dans l’organisme.
 */

/** @typedef {{ keys: string, action: string }} TanStackShortcutRow */

/**
 * Découpe une chaîne du type « Alt+N », « Ctrl+Shift+A », « ↑ / ↓ », « Alt+Entrée ou Alt+E »
 * pour affichage avec le composant DaisyUI {@link https://daisyui.com/components/kbd/ Kbd}.
 *
 * @param {string} s
 * @returns {Array<{ joiner: "+" | "/", parts: string[] }>}
 */
export function parseShortcutKeysForDisplay(s) {
    const raw = String(s || "").trim();
    if (!raw) {
        return [];
    }
    const orGroups = raw.split(/\s+ou\s+/i).map((x) => x.trim()).filter(Boolean);
    return orGroups.map((chunk) => {
        if (/\s\/\s/.test(chunk) && !chunk.includes("+")) {
            return {
                joiner: "/",
                parts: chunk.split(/\s*\/\s*/).map((p) => p.trim()).filter(Boolean),
            };
        }
        return {
            joiner: "+",
            parts: chunk.split("+").map((p) => p.trim()).filter(Boolean),
        };
    });
}

/** @type {TanStackShortcutRow[]} */
export const TANSTACK_TABLE_KEYBOARD_SHORTCUTS = [
    { keys: "Alt+N", action: "Page suivante" },
    { keys: "Alt+B", action: "Page précédente" },
    { keys: "Ctrl+N", action: "Création (événement create-request)" },
    { keys: "Ctrl+A", action: "Tout sélectionner (page courante)" },
    { keys: "Ctrl+D", action: "Tout désélectionner" },
    { keys: "Ctrl+Shift+A", action: "Basculer sélection sur la page" },
    { keys: "↑ / ↓", action: "Ligne précédente / suivante (focus)" },
    { keys: "Espace", action: "Sélectionner ou désélectionner la ligne focalisée" },
    { keys: "Entrée", action: "Ouvrir l’aperçu (modal vue)" },
    { keys: "Ctrl+Entrée", action: "Ouvrir la page fiche entité" },
    { keys: "Alt+Entrée ou Alt+E", action: "Ouvrir la page d’édition" },
    { keys: "Alt+O", action: "Ouvrir le menu actions (comme clic droit sur la ligne)" },
];

/** @type {TanStackShortcutRow[]} */
export const TANSTACK_TABLE_POINTER_SHORTCUTS = [
    { keys: "Clic", action: "Sélectionner la ligne (si sélection activée) ; quick edit si option activée" },
    { keys: "Double-clic", action: "Selon la page (souvent aperçu / modal)" },
    { keys: "Ctrl+clic / Cmd+clic", action: "Ouvrir la page fiche entité" },
    { keys: "Alt+clic", action: "Ouvrir la page d’édition" },
];

/**
 * Détermine l’intention Entrée (vue / page / édition) pour une ligne.
 *
 * @param {KeyboardEvent} e
 * @returns {"open-view"|"open-show-page"|"open-edit"|null}
 */
export function matchTableEnterIntent(e) {
    if (String(e.key || "").toLowerCase() !== "enter") {
        return null;
    }
    if (e.altKey && !e.ctrlKey && !e.metaKey) {
        return "open-edit";
    }
    if ((e.ctrlKey || e.metaKey) && !e.altKey) {
        return "open-show-page";
    }
    if (!e.altKey && !e.ctrlKey && !e.metaKey) {
        return "open-view";
    }
    return null;
}
