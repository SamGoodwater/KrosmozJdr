function safeJsonParse(raw, fallback = {}) {
    if (raw == null || raw === "") return { ...fallback };
    try {
        const v = JSON.parse(String(raw));
        return v && typeof v === "object" ? v : { ...fallback };
    } catch {
        return { ...fallback };
    }
}

const MAX_KREF_TITLE_LEN = 4096;
const MAX_KREF_LEGACY_PAYLOAD_LEN = 2048;
const MAX_KREF_LABEL_LEN = 300;
const ALLOWED_KREF_TYPES = new Set(["characteristic", "entity", "page", "pageSection"]);

/**
 * Encode refs pour l’attribut `title` (HTMLPurifier n’accepte pas fiablement data-* sur span).
 *
 * @param {{ krefType: string, krefPayload: string, label: string }} attrs
 * @returns {string}
 */
export function encodeKrefTitle(attrs) {
    const obj = {
        t: String(attrs.krefType || ""),
        p: safeJsonParse(attrs.krefPayload, {}),
        l: String(attrs.label || "").trim(),
    };
    const json = JSON.stringify(obj);
    const utf8 = new TextEncoder().encode(json);
    let bin = "";
    for (let i = 0; i < utf8.length; i += 1) {
        bin += String.fromCharCode(utf8[i]);
    }
    return btoa(bin).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
}

/**
 * @param {string} title
 * @returns {{ krefType: string, krefPayload: string, label: string }|null}
 */
export function decodeKrefTitle(title) {
    if (title == null || String(title).trim() === "") return null;
    if (String(title).length > MAX_KREF_TITLE_LEN) return null;
    try {
        const b64 = String(title).replace(/-/g, "+").replace(/_/g, "/");
        const pad = b64.length % 4 === 0 ? "" : "=".repeat(4 - (b64.length % 4));
        const bin = atob(b64 + pad);
        const bytes = Uint8Array.from(bin, (c) => c.charCodeAt(0));
        const json = new TextDecoder().decode(bytes);
        const o = JSON.parse(json);
        if (!o || typeof o !== "object" || !o.t) return null;
        const normalizedType = normalizeKrefType(o.t);
        if (!ALLOWED_KREF_TYPES.has(normalizedType)) return null;
        return {
            krefType: normalizedType,
            krefPayload: JSON.stringify(o.p && typeof o.p === "object" ? o.p : {}),
            label: String(o.l || "").trim(),
        };
    } catch {
        return null;
    }
}

export function parseKrefPayload(raw) {
    return safeJsonParse(raw, {});
}

/**
 * Normalise les alias de type (legacy) vers le format interne.
 *
 * @param {string} rawType
 * @returns {string}
 */
export function normalizeKrefType(rawType) {
    const t = String(rawType || "").trim();
    if (t === "page_section") return "pageSection";
    return t;
}

/**
 * @param {string} krefType
 * @returns {boolean}
 */
export function isSupportedKrefType(krefType) {
    return ALLOWED_KREF_TYPES.has(normalizeKrefType(krefType));
}

/**
 * Lit un span kref legacy (`data-kref-*`) si présent.
 *
 * @param {HTMLElement} el
 * @returns {{ krefType: string, krefPayload: string, label: string }|null}
 */
export function decodeLegacyKrefAttributes(el) {
    if (!(el instanceof HTMLElement)) return null;
    const rawType = el.getAttribute("data-kref-type") || "";
    const krefType = normalizeKrefType(rawType);
    if (!isSupportedKrefType(krefType)) return null;
    const payloadRaw = el.getAttribute("data-kref-payload") || "{}";
    if (payloadRaw.length > MAX_KREF_LEGACY_PAYLOAD_LEN) return null;
    const payload = safeJsonParse(payloadRaw, {});
    const label = String(el.textContent || "").trim().slice(0, MAX_KREF_LABEL_LEN);
    return {
        krefType,
        krefPayload: JSON.stringify(payload),
        label,
    };
}

/**
 * Décode un élément `span.kref` en priorisant le format moderne (`title`)
 * puis fallback vers le format legacy (`data-kref-*`).
 *
 * @param {HTMLElement} el
 * @returns {{ krefType: string, krefPayload: string, label: string }|null}
 */
export function decodeKrefElement(el) {
    if (!(el instanceof HTMLElement)) return null;
    const fromTitle = decodeKrefTitle(el.getAttribute("title") || "");
    const parsed = fromTitle || decodeLegacyKrefAttributes(el);
    if (!parsed) return null;
    return {
        krefType: normalizeKrefType(parsed.krefType),
        krefPayload: parsed.krefPayload,
        label: String(parsed.label || "").trim().slice(0, MAX_KREF_LABEL_LEN),
    };
}

/**
 * Indique si le payload minimal est présent pour le type (côté client, hors droits).
 *
 * @param {string} krefType
 * @param {object} payload
 * @returns {boolean}
 */
export function isKrefPayloadComplete(krefType, payload) {
    const p = payload && typeof payload === "object" ? payload : {};
    const t = normalizeKrefType(krefType);
    if (!isSupportedKrefType(t)) return false;
    if (t === "characteristic") return Boolean(p.key);
    if (t === "entity") return Boolean(p.entityType) && p.id != null && p.id !== "";
    if (t === "page") return Boolean(p.pageSlug);
    if (t === "pageSection") {
        if (!p.pageSlug) return false;
        if (p.sectionSlug != null && String(p.sectionSlug).trim() !== "") return true;
        return p.sectionId != null && p.sectionId !== "";
    }
    return Boolean(t);
}
