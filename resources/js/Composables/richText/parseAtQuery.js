import { resolveAtPrefix } from "@/Composables/richText/krefEntityRegistry";

/**
 * @typedef {"all"|"characteristic"|"section"|"entityType"} AtQueryMode
 *
 * @typedef {Object} ParsedAtQuery
 * @property {boolean} isMatch
 * @property {string} trigger
 * @property {string} raw
 * @property {AtQueryMode} mode
 * @property {string} query
 * @property {string|null} prefix
 * @property {string|null} entityType
 */

/**
 * Parse un trigger `@...` en mode de recherche exploitable.
 *
 * Exemples:
 * - `@vita` -> mode `all`, query `vita`
 * - `@carac:force` -> mode `characteristic`, query `force`
 * - `@section:initiative` -> mode `section`, query `initiative`
 * - `@monstre:bouftou` -> mode `entityType`, entityType `monsters`
 *
 * @param {string} textBeforeCursor
 * @returns {ParsedAtQuery}
 */
export function parseAtQuery(textBeforeCursor) {
    const source = String(textBeforeCursor || "");
    const match = source.match(/@([a-zA-Z0-9_.:-]*)$/);
    if (!match) {
        return {
            isMatch: false,
            trigger: "",
            raw: "",
            mode: "all",
            query: "",
            prefix: null,
            entityType: null,
        };
    }

    const trigger = String(match[0] || "");
    const raw = String(match[1] || "");
    const separatorIdx = raw.indexOf(":");
    if (separatorIdx < 0) {
        return {
            isMatch: true,
            trigger,
            raw,
            mode: "all",
            query: raw.trim(),
            prefix: null,
            entityType: null,
        };
    }

    const prefix = raw.slice(0, separatorIdx).trim();
    const query = raw.slice(separatorIdx + 1).trim();
    const resolved = resolveAtPrefix(prefix);
    if (!resolved) {
        return {
            isMatch: true,
            trigger,
            raw,
            mode: "all",
            query,
            prefix,
            entityType: null,
        };
    }

    if (resolved.mode === "entityType") {
        return {
            isMatch: true,
            trigger,
            raw,
            mode: "entityType",
            query,
            prefix,
            entityType: resolved.entityType || null,
        };
    }

    return {
        isMatch: true,
        trigger,
        raw,
        mode: resolved.mode,
        query,
        prefix,
        entityType: null,
    };
}
