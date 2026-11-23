/**
 * Gabarit de largeur des sections CMS d'une même page.
 *
 * Une section tableau (`*_table`) occupe toute la colonne. Les autres sections
 * de la page s'alignent sur cette largeur. Sans tableau, elles restent au
 * gabarit 2/3, centré. Le texte d'une section élargie peut rester dans une
 * mesure de lecture centrée.
 *
 * Rangées côte à côte : sections consécutives avec le même `settings.layoutRowId`
 * partagent une ligne flex ; `settings.layoutWidth` pilote la largeur de chaque cellule.
 *
 * @example
 * pageSectionsUseFullWidth([{ template: 'text' }, { template: 'entity_table' }]); // true
 * groupSectionsIntoRows([{ settings: { layoutRowId: 'a', layoutWidth: 'half' } }, …]);
 */

/**
 * @param {unknown} section
 * @returns {string}
 */
export function sectionTemplateValue(section) {
    const raw = section?.template ?? section?.type ?? "text";
    if (raw && typeof raw === "object" && "value" in raw) {
        return String(raw.value || "text");
    }
    return String(raw || "text");
}

/**
 * @param {unknown} template
 * @returns {boolean}
 */
export function isFullWidthSectionTemplate(template) {
    return String(template || "").endsWith("_table");
}

/**
 * Vrai si au moins une section de la page doit occuper toute la colonne.
 *
 * @param {unknown[]|null|undefined} sections
 * @returns {boolean}
 */
export function pageSectionsUseFullWidth(sections) {
    const list = Array.isArray(sections) ? sections : [];
    return list.some((section) => isFullWidthSectionTemplate(sectionTemplateValue(section)));
}

/**
 * @param {boolean} fullWidth
 * @returns {string}
 */
export function sectionWidthClass(fullWidth) {
    return fullWidth
        ? "w-full min-w-0 max-w-full self-stretch"
        : "w-full min-w-0 max-w-full self-center md:w-2/3";
}

/**
 * @param {unknown} section
 * @returns {Record<string, unknown>}
 */
export function sectionSettingsObject(section) {
    const raw = section?.settings;
    return raw && typeof raw === "object" && !Array.isArray(raw) ? raw : {};
}

/**
 * @param {unknown} section
 * @returns {string}
 */
export function sectionLayoutRowId(section) {
    return String(sectionSettingsObject(section).layoutRowId || "").trim();
}

/**
 * Normalise `layoutWidth` : auto | fill | half | third | twoThirds | percent 1–100.
 *
 * @param {unknown} raw
 * @returns {{ mode: 'auto'|'fill'|'half'|'third'|'twoThirds'|'percent', percent?: number }}
 */
export function normalizeLayoutWidth(raw) {
    if (raw == null || raw === "") {
        return { mode: "fill" };
    }
    if (typeof raw === "number" && Number.isFinite(raw)) {
        const percent = Math.min(100, Math.max(1, Math.round(raw)));
        return { mode: "percent", percent };
    }
    const s = String(raw).trim().toLowerCase();
    if (s === "auto" || s === "content") return { mode: "auto" };
    if (s === "fill" || s === "grow" || s === "flex") return { mode: "fill" };
    if (s === "half" || s === "1/2" || s === "50%") return { mode: "half" };
    if (s === "third" || s === "1/3" || s === "33%") return { mode: "third" };
    if (s === "twothirds" || s === "two_thirds" || s === "2/3" || s === "66%") {
        return { mode: "twoThirds" };
    }
    const asNum = Number(s.replace(/%$/, ""));
    if (Number.isFinite(asNum) && asNum > 0) {
        const percent = Math.min(100, Math.max(1, Math.round(asNum)));
        return { mode: "percent", percent };
    }
    return { mode: "fill" };
}

/**
 * Classes Tailwind pour une cellule de rangée adjacente.
 *
 * @param {unknown} layoutWidth
 * @returns {string}
 */
export function adjacentCellClass(layoutWidth) {
    const { mode } = normalizeLayoutWidth(layoutWidth);
    if (mode === "auto") return "min-w-0 max-w-full shrink grow-0 basis-auto";
    if (mode === "half") return "min-w-0 max-w-full flex-[1_1_18rem] sm:flex-[1_1_calc(50%-0.5rem)]";
    if (mode === "third") return "min-w-0 max-w-full flex-[1_1_14rem] sm:flex-[1_1_calc(33.333%-0.5rem)]";
    if (mode === "twoThirds") {
        return "min-w-0 max-w-full flex-[1_1_18rem] sm:flex-[1_1_calc(66.666%-0.5rem)]";
    }
    if (mode === "percent") return "min-w-0 max-w-full";
    // fill
    return "min-w-0 max-w-full flex-1 basis-0";
}

/**
 * Style inline pour une largeur en % personnalisée.
 *
 * @param {unknown} layoutWidth
 * @returns {Record<string, string>|undefined}
 */
export function adjacentCellStyle(layoutWidth) {
    const { mode, percent } = normalizeLayoutWidth(layoutWidth);
    if (mode !== "percent" || percent == null) return undefined;
    return {
        flex: `1 1 ${percent}%`,
        maxWidth: "100%",
    };
}

/**
 * Groupe les sections consécutives partageant le même `layoutRowId` non vide.
 *
 * @param {unknown[]|null|undefined} sections
 * @returns {Array<{ key: string, rowId: string|null, items: Array<{ section: object, layoutWidth: unknown }> }>}
 */
export function groupSectionsIntoRows(sections) {
    const list = Array.isArray(sections) ? sections : [];
    /** @type {ReturnType<typeof groupSectionsIntoRows>} */
    const rows = [];
    let current = null;

    list.forEach((section, index) => {
        const rowId = sectionLayoutRowId(section);
        const layoutWidth = sectionSettingsObject(section).layoutWidth;
        const item = { section, layoutWidth };

        if (rowId && current && current.rowId === rowId) {
            current.items.push(item);
            return;
        }

        current = {
            key: rowId ? `row-${rowId}-${index}` : `solo-${section?.id ?? index}`,
            rowId: rowId || null,
            items: [item],
        };
        rows.push(current);
    });

    return rows;
}
