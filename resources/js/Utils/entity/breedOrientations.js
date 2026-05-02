/**
 * Voix élémentaires et URLs d’icônes (aligné sur config/breed_element_orientations.php).
 *
 * @see config/breed_element_orientations.php
 */

/** @typedef {'air'|'earth'|'fire'|'water'} BreedElementKey */

/** @type {BreedElementKey[]} */
export const BREED_ELEMENT_KEYS = ["air", "earth", "fire", "water"];

/** Libellés FR pour tooltips */
export const BREED_ELEMENT_LABELS = {
    air: "Air",
    earth: "Terre",
    fire: "Feu",
    water: "Eau",
};

const VOICE_ICON_BASE = "/storage/images/icons";

/** @type {Record<BreedElementKey, string>} chemin relatif sous public storage */
export const BREED_ELEMENT_VOICE_ICON_PATH = {
    air: `${VOICE_ICON_BASE}/caracteristics/air.webp`,
    earth: `${VOICE_ICON_BASE}/caracteristics/earth.webp`,
    fire: `${VOICE_ICON_BASE}/caracteristics/fire.webp`,
    water: `${VOICE_ICON_BASE}/caracteristics/water.webp`,
};

const ORIENTATION_EXT = "png";
const ORIENTATION_BASE = `${VOICE_ICON_BASE}/breed_orientations`;

/**
 * URL publique de l’icône d’orientation (fichier dans breed_orientations).
 *
 * @param {string|null|undefined} orientationKey stem sans extension
 * @returns {string|null}
 */
export function breedOrientationIconUrl(orientationKey) {
    if (orientationKey == null || String(orientationKey).trim() === "") {
        return null;
    }
    const k = String(orientationKey).trim();
    return `${ORIENTATION_BASE}/${k}.${ORIENTATION_EXT}`;
}

/**
 * @param {Record<string, string|null|undefined>|null|undefined} map
 * @returns {Record<BreedElementKey, string|null>}
 */
export function normalizeElementOrientationMap(map) {
    const out = {
        air: null,
        earth: null,
        fire: null,
        water: null,
    };
    if (!map || typeof map !== "object") {
        return out;
    }
    for (const key of BREED_ELEMENT_KEYS) {
        if (Object.prototype.hasOwnProperty.call(map, key)) {
            const v = map[key];
            out[key] = v != null && String(v).trim() !== "" ? String(v).trim() : null;
        }
    }
    return out;
}
