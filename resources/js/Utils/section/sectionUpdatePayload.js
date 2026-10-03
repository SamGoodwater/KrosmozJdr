/**
 * Payload de mise à jour de section CMS (contenu Inertia différé).
 *
 * @example
 * sanitizeSectionUpdatePayload({ data: { content: null, content_deferred: true } })
 * // → { data: {} }
 */

/**
 * Indique si le HTML de la section n’est pas encore chargé.
 *
 * @param {object|null|undefined} section
 * @returns {boolean}
 */
export function isSectionContentDeferred(section) {
    if (!section || typeof section !== "object") {
        return false;
    }

    return Boolean(section.content_deferred) || Boolean(section.data?.content_deferred);
}

/**
 * HTML vide ou placeholder TipTap (`<p></p>`), y compris `null`.
 *
 * @param {unknown} value
 * @returns {boolean}
 */
export function isEmptySectionHtml(value) {
    if (value === null || value === undefined) {
        return true;
    }
    if (typeof value !== "string") {
        return false;
    }
    const trimmed = value.trim();
    if (trimmed === "") {
        return true;
    }
    const text = trimmed
        .replace(/<[^>]*>/g, "")
        .replace(/&nbsp;/gi, " ")
        .trim();

    return text === "";
}

/**
 * Retire les marqueurs de chargement différé pour ne pas les persister.
 *
 * Un `content: null` (placeholder Inertia) n’est pas envoyé : le HTML en base
 * ne doit pas être écrasé. Un HTML vide *avec* `content_deferred` est aussi
 * écarté. Vider volontairement une section déjà hydratée (`content: ""`
 * sans flag) reste possible.
 *
 * @param {object} updates
 * @returns {object}
 */
export function sanitizeSectionUpdatePayload(updates) {
    if (!updates || typeof updates !== "object") {
        return updates;
    }

    const next = { ...updates };
    for (const key of ["data", "params"]) {
        if (!next[key] || typeof next[key] !== "object" || Array.isArray(next[key])) {
            continue;
        }
        const bag = { ...next[key] };
        const deferred = Boolean(bag.content_deferred);
        delete bag.content_deferred;

        if (Object.prototype.hasOwnProperty.call(bag, "content")) {
            const incoming = bag.content;
            if (incoming === null || incoming === undefined || (deferred && isEmptySectionHtml(incoming))) {
                delete bag.content;
            }
        }

        next[key] = bag;
    }

    if (Object.prototype.hasOwnProperty.call(next, "content_deferred")) {
        delete next.content_deferred;
    }

    return next;
}
