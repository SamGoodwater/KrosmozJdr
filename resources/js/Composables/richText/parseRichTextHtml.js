import { decodeKrefElement } from "@/Composables/richText/krefCodec";

const ALLOWED_TAGS = new Set([
    "p",
    "br",
    "strong",
    "b",
    "em",
    "i",
    "u",
    "s",
    "blockquote",
    "code",
    "pre",
    "ul",
    "ol",
    "li",
    "h1",
    "h2",
    "h3",
    "h4",
    "h5",
    "h6",
    "a",
    "img",
    "span",
    "div",
    "table",
    "thead",
    "tbody",
    "tfoot",
    "tr",
    "th",
    "td",
    "hr",
    "figure",
    "figcaption",
    "sup",
    "sub",
]);

const VOID_TAGS = new Set(["br", "img", "hr", "wbr"]);

const SAFE_ATTRS = ["href", "src", "alt", "title", "class", "id", "target", "rel", "colspan", "rowspan", "width", "height"];

/**
 * @param {HTMLElement} el
 * @returns {Record<string, string>}
 */
function pickAttrs(el) {
    const attrs = {};
    SAFE_ATTRS.forEach((name) => {
        const value = el.getAttribute(name);
        if (value != null && value !== "") {
            attrs[name] = value;
        }
    });
    return attrs;
}

/**
 * @param {Node} node
 * @returns {object|null}
 */
function walk(node) {
    if (node.nodeType === Node.TEXT_NODE) {
        if (!node.textContent) return null;
        return { kind: "text", text: node.textContent };
    }
    if (node.nodeType !== Node.ELEMENT_NODE) return null;

    const el = /** @type {HTMLElement} */ (node);
    if (el.matches("span.kref")) {
        const decoded = decodeKrefElement(el);
        if (decoded) {
            return {
                kind: "kref",
                krefType: decoded.krefType,
                krefPayload: decoded.krefPayload,
                label: decoded.label || el.textContent?.trim() || "",
            };
        }
    }

    const tag = el.tagName.toLowerCase();
    if (!ALLOWED_TAGS.has(tag)) {
        return { kind: "frag", children: walkChildren(el) };
    }

    return {
        kind: "el",
        tag,
        attrs: pickAttrs(el),
        children: VOID_TAGS.has(tag) ? [] : walkChildren(el),
    };
}

/**
 * @param {ParentNode} parent
 * @returns {object[]}
 */
function walkChildren(parent) {
    const out = [];
    parent.childNodes.forEach((child) => {
        const built = walk(child);
        if (!built) return;
        if (built.kind === "frag") {
            out.push(...(built.children || []));
            return;
        }
        out.push(built);
    });
    return out;
}

/**
 * Découpe du HTML sanitizé en arbre de rendu. Les spans `.kref` deviennent des nœuds `kref`
 * (puce Vue) au lieu d’un title base64 affiché par le navigateur.
 *
 * @param {string} html
 * @returns {object[]}
 * @example
 * parseRichTextHtml('<p>Coût <span class="kref" title="...">PA</span></p>');
 */
export function parseRichTextHtml(html) {
    if (typeof DOMParser === "undefined") return [];
    const source = String(html || "");
    if (source.trim() === "") return [];
    const doc = new DOMParser().parseFromString(source, "text/html");
    return walkChildren(doc.body);
}
