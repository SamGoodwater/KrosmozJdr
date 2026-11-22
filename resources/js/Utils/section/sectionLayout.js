/**
 * Gabarit de largeur des sections CMS d'une même page.
 *
 * Une section tableau (`*_table`) occupe toute la colonne. Les autres sections
 * de la page s'alignent sur cette largeur. Sans tableau, elles restent au
 * gabarit 2/3, centré. Le texte d'une section élargie peut rester dans une
 * mesure de lecture centrée.
 *
 * @example
 * pageSectionsUseFullWidth([{ template: 'text' }, { template: 'entity_table' }]); // true
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
