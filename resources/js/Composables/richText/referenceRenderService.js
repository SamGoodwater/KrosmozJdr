import { parseKrefPayload, isKrefPayloadComplete, normalizeKrefType } from "@/Composables/richText/krefCodec";

const TYPE_META = {
    characteristic: {
        iconClass: "fa-solid fa-chart-simple",
        typeLabel: "Caractéristique",
    },
    entity: {
        iconClass: "fa-solid fa-cube",
        typeLabel: "Entité",
    },
    page: {
        iconClass: "fa-solid fa-file-lines",
        typeLabel: "Page",
    },
    pageSection: {
        iconClass: "fa-solid fa-anchor",
        typeLabel: "Section",
    },
};

const DEFAULT_META = {
    iconClass: "fa-solid fa-link",
    typeLabel: "Référence",
};

/**
 * Présentation visuelle d’une référence inline (édition + lecture TipTap).
 *
 * @param {{ krefType?: string|null, krefPayload?: string, label?: string }} attrs — attributs du nœud ProseMirror
 * @returns {{
 *   displayLabel: string,
 *   iconClass: string,
 *   typeLabel: string,
 *   navigable: boolean,
 *   invalidPayload: boolean,
 *   wrapperClasses: string[],
 * }}
 */
export function getReferencePresentation(attrs) {
    const krefType = normalizeKrefType(attrs?.krefType);
    const label = String(attrs?.label || "").trim() || "Référence";
    const payload = parseKrefPayload(attrs?.krefPayload);
    const meta = TYPE_META[krefType] || DEFAULT_META;
    const navigable = krefType === "page" || krefType === "pageSection" || krefType === "entity";
    const invalidPayload = Boolean(krefType) && !isKrefPayloadComplete(krefType, payload);

    const wrapperClasses = [
        "kref",
        `kref--type-${krefType || "unknown"}`,
        navigable ? "kref--nav" : "",
        invalidPayload ? "kref--invalid" : "",
    ].filter(Boolean);

    if (import.meta.env?.DEV && invalidPayload) {
        // eslint-disable-next-line no-console
        console.debug("[kref] payload incomplet", { krefType, payload });
    }

    return {
        displayLabel: label,
        iconClass: meta.iconClass,
        typeLabel: meta.typeLabel,
        navigable,
        invalidPayload,
        wrapperClasses,
    };
}

/**
 * Classes CSS utilitaires pour le rendu inline côté HTML statique (renderHTML TipTap).
 *
 * @param {{ krefType?: string|null, krefPayload?: string, label?: string }} attrs
 * @returns {string}
 */
export function getReferenceClassName(attrs) {
    return getReferencePresentation(attrs).wrapperClasses.join(" ");
}
