/**
 * Helpers pour les vignettes d’entité ({@link EntityThumb}).
 *
 * @example
 * resolveEntityImageUrl(monster);
 * normalizeEntityThumbLabel(monster?.creature?.name ?? monster?.name);
 */

/**
 * @param {string} raw
 * @returns {string}
 */
export function normalizeEntityThumbLabel(raw) {
    const title = String(raw ?? "").trim();
    if (title.startsWith("#")) {
        return title.slice(1).trim() || title;
    }

    return title;
}

/**
 * @param {Record<string, unknown>|string|null|undefined} entityOrUrl
 * @returns {string}
 */
export function resolveEntityImageUrl(entityOrUrl) {
    if (typeof entityOrUrl === "string") {
        const value = entityOrUrl.trim();
        if (!value || value.startsWith("fa-") || value.includes("fa-solid")) {
            return "";
        }

        return value;
    }

    const entity = entityOrUrl;
    if (!entity || typeof entity !== "object") {
        return "";
    }

    const candidates = [
        entity.image,
        entity.icon,
        entity.creature?.image,
        entity._data?.image,
        entity._data?.icon,
    ];

    for (const candidate of candidates) {
        const value = String(candidate ?? "").trim();
        if (!value || value.startsWith("fa-") || value.includes("fa-solid")) {
            continue;
        }

        return value;
    }

    return "";
}

/**
 * @param {Record<string, unknown>|null|undefined} entity
 * @param {string} [fallback='']
 * @returns {string}
 */
export function resolveEntityThumbLabel(entity, fallback = "") {
    if (!entity || typeof entity !== "object") {
        return normalizeEntityThumbLabel(fallback);
    }

    const name =
        entity.name
        ?? entity.title
        ?? entity.creature?.name
        ?? fallback;

    return normalizeEntityThumbLabel(String(name ?? ""));
}
