/**
 * Titre public d'une section CMS.
 *
 * Un titre réel s'affiche. Un libellé de secours (vide, « Texte », « Section »)
 * ne s'affiche pas : ce n'est pas un titre de contenu.
 *
 * @example
 * publicSectionTitle('Niveau 1'); // "Niveau 1"
 * publicSectionTitle('Texte'); // ""
 */

const PLACEHOLDER_TITLES = new Set(["texte", "text", "section", "sections", "sans titre"]);

/**
 * @param {unknown} title
 * @returns {string}
 */
function normalizedTitle(title) {
    return String(title ?? "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{M}/gu, "");
}

/**
 * @param {unknown} title
 * @returns {boolean}
 */
export function isPlaceholderSectionTitle(title) {
    const normalized = normalizedTitle(title);
    if (normalized === "") {
        return true;
    }
    if (PLACEHOLDER_TITLES.has(normalized)) {
        return true;
    }
    return /^sections?\s+\d+$/.test(normalized);
}

/**
 * Titre à montrer au lecteur. Chaîne vide si le titre stocké est un bouche-trou.
 *
 * @param {unknown} title
 * @returns {string}
 */
export function publicSectionTitle(title) {
    const raw = String(title ?? "").trim();
    return isPlaceholderSectionTitle(raw) ? "" : raw;
}

/**
 * Texte brut d'un HTML de section (balises retirées).
 *
 * @param {unknown} html
 * @returns {string}
 */
export function plainTextFromSectionHtml(html) {
    return String(html ?? "")
        .replace(/<script[\s\S]*?<\/script>/gi, " ")
        .replace(/<style[\s\S]*?<\/style>/gi, " ")
        .replace(/<[^>]+>/g, " ")
        .replace(/&nbsp;/gi, " ")
        .replace(/&amp;/gi, "&")
        .replace(/&quot;/gi, '"')
        .replace(/&#39;|&apos;/gi, "'")
        .replace(/&lt;/gi, "<")
        .replace(/&gt;/gi, ">")
        .replace(/\s+/g, " ")
        .trim();
}

/**
 * Libellé de navigation : le titre réel, ou les premiers mots suivis de «...».
 *
 * @param {unknown} section - Section CMS, ou un titre brut
 * @param {{ maxWords?: number }} [options]
 * @returns {string}
 *
 * @example
 * sectionDisplayLabel({ title: "Niveau 1", data: { content: "<p>Ignoré</p>" } }); // "Niveau 1"
 * sectionDisplayLabel({ title: "Texte", data: { content: "<p>En tant qu'Artiste tu tiens la salle</p>" } });
 */
export function sectionDisplayLabel(section, options = {}) {
    const maxWords = Number(options.maxWords) > 0 ? Number(options.maxWords) : 7;
    const rawTitle = section && typeof section === "object" ? section.title : section;
    const title = publicSectionTitle(rawTitle);
    if (title) {
        return title;
    }

    const html =
        section && typeof section === "object"
            ? (section.data?.content ?? section.content ?? "")
            : "";
    const text = plainTextFromSectionHtml(html);
    if (!text) {
        return "";
    }

    const words = text.split(" ").filter(Boolean);
    return `${words.slice(0, maxWords).join(" ")}...`;
}
