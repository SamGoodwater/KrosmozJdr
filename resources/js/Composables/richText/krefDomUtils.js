import { decodeKrefElement, parseKrefPayload, normalizeKrefType } from "@/Composables/richText/krefCodec";
import { resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";

const NAVIGABLE_TYPES = new Set(["page", "pageSection", "entity"]);

function readPayloadValue(payload, camelKey, snakeKey) {
    if (!payload || typeof payload !== "object") return null;
    if (payload[camelKey] != null && String(payload[camelKey]).trim() !== "") return payload[camelKey];
    if (payload[snakeKey] != null && String(payload[snakeKey]).trim() !== "") return payload[snakeKey];
    return null;
}

/**
 * @param {string} krefType
 * @returns {boolean}
 */
export function isKrefNavigableType(krefType) {
    return NAVIGABLE_TYPES.has(normalizeKrefType(krefType));
}

/**
 * @param {HTMLElement} el
 * @returns {{ krefType: string, payload: object, label: string }|null}
 */
export function getDecodedKrefFromElement(el) {
    if (!el || typeof el.closest !== "function") return null;
    const span = el.classList?.contains("kref") ? el : el.closest?.("span.kref");
    if (!(span instanceof HTMLElement) || !span.classList.contains("kref")) return null;
    const decoded = decodeKrefElement(span);
    if (!decoded) return null;
    return {
        krefType: decoded.krefType,
        payload: parseKrefPayload(decoded.krefPayload),
        label: decoded.label,
    };
}

/**
 * @param {{ krefType: string, payload: object }} info
 * @returns {string|null} href relatif ou null
 */
export function buildHrefFromKref(info) {
    const t = normalizeKrefType(info?.krefType);
    const p = info?.payload && typeof info.payload === "object" ? info.payload : {};
    try {
        if (t === "page") {
            const slug = readPayloadValue(p, "pageSlug", "page_slug");
            if (!slug) return null;
            return route("pages.show", slug);
        }
        if (t === "pageSection") {
            const slug = readPayloadValue(p, "pageSlug", "page_slug");
            if (!slug) return null;
            const secSlugRaw = readPayloadValue(p, "sectionSlug", "section_slug");
            const secSlug = secSlugRaw != null ? String(secSlugRaw).trim() : null;
            if (secSlug) {
                return `${route("pages.show", slug)}#ssec-${secSlug}`;
            }
            const sid = readPayloadValue(p, "sectionId", "section_id");
            if (sid == null || sid === "") return null;
            return `${route("pages.show", slug)}#section-${sid}`;
        }
        if (t === "entity") {
            const entityType = p.entityType;
            const id = p.id;
            if (!entityType || id == null || id === "") return null;
            return resolveEntityRouteHref(String(entityType), "show", id) || null;
        }
    } catch {
        return null;
    }
    return null;
}

/**
 * URL JSON d’aperçu page (popover sommaire).
 *
 * @param {{ krefType: string, payload: object }} info
 * @returns {string|null}
 */
export function buildPagePreviewSnippetUrl(info) {
    if (normalizeKrefType(info?.krefType) !== "page") return null;
    const p = info?.payload && typeof info.payload === "object" ? info.payload : {};
    const pageSlugRaw = readPayloadValue(p, "pageSlug", "page_slug");
    const pageSlug = pageSlugRaw != null ? String(pageSlugRaw).trim() : null;
    if (!pageSlug) return null;

    return route("api.cms.pages.preview-snippet", { page: pageSlug });
}

/**
 * Identifiant de section pour l’API d’aperçu (références `pageSection` uniquement).
 *
 * @param {{ krefType: string, payload: object }} info
 * @returns {number|string|null}
 */
export function getSectionIdForPreview(info) {
    if (normalizeKrefType(info?.krefType) !== "pageSection") return null;
    const sid = readPayloadValue(info?.payload, "sectionId", "section_id");
    if (sid == null || sid === "") return null;
    const n = Number(sid);
    return Number.isFinite(n) ? n : sid;
}

/**
 * URL JSON d’aperçu section (popover) : id numérique ou couple page_slug / section_slug.
 *
 * @param {{ krefType: string, payload: object }} info
 * @returns {string|null}
 */
export function buildSectionPreviewSnippetUrl(info) {
    if (normalizeKrefType(info?.krefType) !== "pageSection") return null;
    const p = info?.payload && typeof info.payload === "object" ? info.payload : {};
    const sid = readPayloadValue(p, "sectionId", "section_id");
    if (sid != null && String(sid).trim() !== "") {
        return route("api.cms.sections.preview-snippet", { section: sid });
    }

    const pageSlugRaw = readPayloadValue(p, "pageSlug", "page_slug");
    const sectionSlugRaw = readPayloadValue(p, "sectionSlug", "section_slug");
    const pageSlug = pageSlugRaw != null ? String(pageSlugRaw).trim() : null;
    const sectionSlug = sectionSlugRaw != null ? String(sectionSlugRaw).trim() : null;
    if (!pageSlug || !sectionSlug) return null;

    return route("api.cms.sections.preview-snippet-query", {
        page_slug: pageSlug,
        section_slug: sectionSlug,
    });
}
