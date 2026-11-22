/**
 * Libellé de plan publié par la section une fois son HTML chargé.
 * Les sections différées n'ont pas de texte dans le payload initial.
 */
import { reactive } from "vue";

const labels = reactive({});

/**
 * @param {number|string|null|undefined} id
 * @param {string} label
 */
export function publishSectionPlanLabel(id, label) {
    if (id == null || id === "") return;
    labels[String(id)] = String(label || "").trim();
}

/**
 * @param {number|string|null|undefined} id
 * @returns {string}
 */
export function publishedSectionPlanLabel(id) {
    if (id == null || id === "") return "";
    return labels[String(id)] || "";
}
