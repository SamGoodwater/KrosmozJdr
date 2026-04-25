import { Node, mergeAttributes } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import ReferenceInlineNodeView from "@/Composables/richText/ReferenceInlineNodeView.vue";
import {
    encodeKrefTitle,
    decodeKrefElement,
    normalizeKrefType,
} from "@/Composables/richText/krefCodec";
import { getReferenceClassName } from "@/Composables/richText/referenceRenderService";

export { encodeKrefTitle } from "@/Composables/richText/krefCodec";

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
                    const parsed = decodeKrefElement(el);
                    if (!parsed) return false;
                    return {
                        krefType: parsed.krefType,
                        krefPayload: parsed.krefPayload,
                        label: parsed.label || el.textContent?.trim() || "",
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
        const cls = getReferenceClassName({
            krefType: normalizeKrefType(node.attrs.krefType),
            krefPayload: node.attrs.krefPayload,
            label: node.attrs.label,
        });
        return [
            "span",
            mergeAttributes(HTMLAttributes, {
                class: cls,
                title,
            }),
            label,
        ];
    },

    addNodeView() {
        return VueNodeViewRenderer(ReferenceInlineNodeView);
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
                            krefType: normalizeKrefType(krefType),
                            krefPayload,
                            label,
                            refTitle: encodeKrefTitle({
                                krefType: normalizeKrefType(krefType),
                                krefPayload,
                                label,
                            }),
                        },
                    });
                },
        };
    },
});
