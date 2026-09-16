/**
 * Résout les visuels de classe (symboles, logos, full).
 *
 * @param {Record<string, unknown>|null|undefined} entity
 * @param {string} key
 * @returns {string}
 */
export function breedImageField(entity, key) {
    if (!entity || typeof entity !== "object") {
        return "";
    }
    const raw = entity._data && typeof entity._data === "object" ? entity._data : entity;
    const value = raw?.[key] ?? entity?.[key];
    if (value == null) {
        return "";
    }
    const trimmed = String(value).trim();
    return trimmed;
}

/**
 * @param {Record<string, unknown>|null|undefined} entity
 * @param {string[]} keys
 * @returns {string}
 */
export function firstBreedImage(entity, keys) {
    for (const key of keys) {
        const value = breedImageField(entity, key);
        if (value) {
            return value;
        }
    }
    return "";
}

/**
 * Tête mâle (Minimal condensé, Line).
 *
 * @param {Record<string, unknown>|null|undefined} entity
 * @returns {string}
 */
export function breedLogoMaleUrl(entity) {
    return firstBreedImage(entity, ["logo_male", "logo_female", "image", "icon"]);
}

/**
 * Tête femelle (Minimal déployé).
 *
 * @param {Record<string, unknown>|null|undefined} entity
 * @returns {string}
 */
export function breedLogoFemaleUrl(entity) {
    return firstBreedImage(entity, ["logo_female", "logo_male", "image", "icon"]);
}

/**
 * Perso full mâle / femelle.
 *
 * @param {Record<string, unknown>|null|undefined} entity
 * @param {'m'|'f'} gender
 * @returns {string}
 */
export function breedFullUrl(entity, gender = "m") {
    if (gender === "f") {
        return firstBreedImage(entity, ["image_full_female", "image_full_male", "image", "icon"]);
    }
    return firstBreedImage(entity, ["image_full_male", "image_full_female", "image", "icon"]);
}

/**
 * @param {Record<string, unknown>|null|undefined} entity
 * @returns {boolean}
 */
export function breedHasBothFullImages(entity) {
    return Boolean(breedImageField(entity, "image_full_male") && breedImageField(entity, "image_full_female"));
}
