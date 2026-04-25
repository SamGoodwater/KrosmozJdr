import { decodeKrefElement, parseKrefPayload, normalizeKrefType } from "@/Composables/richText/krefCodec";
import { resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";

const NAVIGABLE_TYPES = new Set(["page", "pageSection", "entity"]);

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
            const slug = p.pageSlug;
            if (!slug) return null;
            return route("pages.show", slug);
        }
        if (t === "pageSection") {
            const slug = p.pageSlug;
            const sid = p.sectionId;
            if (!slug || sid == null || sid === "") return null;
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
 * Identifiant de section pour l’API d’aperçu (références `pageSection` uniquement).
 *
 * @param {{ krefType: string, payload: object }} info
 * @returns {number|string|null}
 */
export function getSectionIdForPreview(info) {
    if (normalizeKrefType(info?.krefType) !== "pageSection") return null;
    const sid = info?.payload?.sectionId;
    if (sid == null || sid === "") return null;
    const n = Number(sid);
    return Number.isFinite(n) ? n : sid;
}
