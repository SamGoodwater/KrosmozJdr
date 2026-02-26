/**
 * Libellés pour l’affichage prévisualisation et comparaison (scrapping).
 * Mapping clé technique → libellé et section → libellé, partagé entre SearchPreviewSection et EntityDiffTable.
 */

/** Libellés des sections (clé top-level : creatures, monsters, items, spells, etc.). */
export const SECTION_LABELS = {
    creatures: "Identité",
    monster: "Monstre",
    monsters: "Monstre",
    items: "Objet",
    item: "Objet",
    spells: "Sort",
    spell: "Sort",
    breed: "Classe",
    breeds: "Classe",
    panoply: "Panoplie",
    panoplies: "Panoplie",
    resource: "Ressource",
    consumable: "Consommable",
    equipment: "Équipement",
    record: "Enregistrement",
};

/** Libellés des propriétés (nom de clé, éventuellement avec préfixe section.). */
export const FIELD_LABELS = {
    name: "Nom",
    level: "Niveau",
    life: "PV",
    raceId: "Race",
    typeId: "Type",
    breedId: "Classe",
    grade: "Grade",
    rarity: "Rareté",
    effects: "Effets",
    effect: "Bonus convertis",
    bonus: "Bonus (JSON brut)",
    description: "Description",
    image: "Image",
    length: "Nombre d’éléments",
};

/**
 * Retourne le libellé d’une section (clé top-level).
 * @param {string} sectionKey
 * @returns {string}
 */
export function getSectionLabel(sectionKey) {
    if (!sectionKey || typeof sectionKey !== "string") return sectionKey ?? "";
    return SECTION_LABELS[sectionKey] ?? sectionKey;
}

/**
 * Retourne le libellé d’une propriété (dernier segment de clé ou clé complète).
 * @param {string} fieldKey - Ex. "level", "monsters.level", "creatures.name"
 * @returns {string}
 */
export function getFieldLabel(fieldKey) {
    if (!fieldKey || typeof fieldKey !== "string") return fieldKey ?? "";
    const lastSegment = fieldKey.includes(".") ? fieldKey.split(".").pop() : fieldKey;
    return FIELD_LABELS[lastSegment] ?? FIELD_LABELS[fieldKey] ?? fieldKey;
}

/**
 * Retourne le nom de section pour regroupement à partir d’une clé aplatie (ex. "monsters.level" → "Monstre").
 * @param {string} flatKey
 * @returns {string}
 */
export function getSectionFromFlatKey(flatKey) {
    if (!flatKey || !flatKey.includes(".")) return "Autres";
    const prefix = flatKey.split(".")[0];
    return getSectionLabel(prefix);
}

export default { SECTION_LABELS, FIELD_LABELS, getSectionLabel, getFieldLabel, getSectionFromFlatKey };
