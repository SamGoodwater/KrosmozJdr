import { Node, mergeAttributes } from "@tiptap/core";

function safeJsonParse(raw, fallback = {}) {
    if (raw == null || raw === "") return { ...fallback };
    try {
        const v = JSON.parse(String(raw));
        return v && typeof v === "object" ? v : { ...fallback };
    } catch {
        return { ...fallback };
    }
}

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
    try {
        const b64 = String(title).replace(/-/g, "+").replace(/_/g, "/");
        const pad = b64.length % 4 === 0 ? "" : "=".repeat(4 - (b64.length % 4));
        const bin = atob(b64 + pad);
        const bytes = Uint8Array.from(bin, (c) => c.charCodeAt(0));
        const json = new TextDecoder().decode(bytes);
        const o = JSON.parse(json);
        if (!o || typeof o !== "object" || !o.t) return null;
        return {
            krefType: String(o.t),
            krefPayload: JSON.stringify(o.p && typeof o.p === "object" ? o.p : {}),
            label: String(o.l || "").trim(),
        };
    } catch {
        return null;
    }
}

/**
 * Nœud inline atomique : puce de référence.
 * Persistance : `<span class="kref" title="base64url(json)">label</span>`
 */
export const ReferenceInline = Node.create({
    name: "referenceInline",
    group: "inline",
    inline: true,
    atom: true,
    selectable: true,
    draggable: false,

    addAttributes() {
        return {
            krefType: {
                default: null,
            },
            krefPayload: {
                default: "{}",
            },
            label: {
                default: "",
            },
            refTitle: {
                default: "",
                parseHTML: (el) => {
                    if (typeof el === "string" || !(el instanceof HTMLElement)) return "";
                    return el.getAttribute("title") ?? "";
                },
                renderHTML: (attrs) => {
                    const t = encodeKrefTitle({
                        krefType: attrs.krefType,
                        krefPayload: attrs.krefPayload,
                        label: attrs.label,
                    });
                    return t ? { title: t } : {};
                },
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'span[class~="kref"]',
                getAttrs: (el) => {
                    if (typeof el === "string" || !(el instanceof HTMLElement)) return false;
                    const title = el.getAttribute("title");
                    const decoded = decodeKrefTitle(title || "");
                    if (!decoded) return false;
                    return {
                        krefType: decoded.krefType,
                        krefPayload: decoded.krefPayload,
                        label: decoded.label || el.textContent?.trim() || "",
                        refTitle: title || "",
                    };
                },
            },
        ];
    },

    renderHTML({ node, HTMLAttributes }) {
        const label = String(node.attrs.label || "").trim() || "Référence";
        const title = encodeKrefTitle({
            krefType: node.attrs.krefType,
            krefPayload: node.attrs.krefPayload,
            label: node.attrs.label,
        });
        const kt = String(node.attrs.krefType || "");
        const navigable = kt === "page" || kt === "pageSection" || kt === "entity";
        const cls = navigable ? "kref kref--nav" : "kref";
        return [
            "span",
            mergeAttributes(HTMLAttributes, {
                class: cls,
                title,
            }),
            label,
        ];
    },

    renderText({ node }) {
        return String(node.attrs.label || "").trim() || "Référence";
    },

    addCommands() {
        return {
            insertReferenceInline:
                (attrs) =>
                ({ commands }) => {
                    const krefType = attrs?.krefType;
                    if (!krefType) return false;
                    const payload = attrs?.payload && typeof attrs.payload === "object" ? attrs.payload : {};
                    const label = String(attrs?.label || "").trim() || "Référence";
                    const krefPayload = JSON.stringify(payload);
                    return commands.insertContent({
                        type: this.name,
                        attrs: {
                            krefType: String(krefType),
                            krefPayload,
                            label,
                            refTitle: encodeKrefTitle({
                                krefType: String(krefType),
                                krefPayload,
                                label,
                            }),
                        },
                    });
                },
        };
    },
});

export function parseKrefPayload(raw) {
    return safeJsonParse(raw, {});
}
