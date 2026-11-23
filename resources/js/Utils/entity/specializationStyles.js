/**
 * Tokens visuels des variantes / styles de spécialisation (icône + teinte).
 * Structure de fiche = toujours par niveau ; les styles ne sont que des pastilles.
 *
 * Artiste : famille rouge (image de la spé) → rouge / bordeaux / brun / cramoisi.
 *
 * @typedef {{ key: string, name: string, icon: string, hue: number, chroma?: number, lightness?: number }} SpeStyleToken
 */

/** @type {Record<string, SpeStyleToken>} */
const ARTISTE_STYLES = {
    musicien: {
        key: "musicien",
        name: "Musicien",
        icon: "fa-solid fa-music",
        hue: 22,
        chroma: 0.17,
        lightness: 0.7,
    },
    saltimbanque: {
        key: "saltimbanque",
        name: "Saltimbanque",
        icon: "fa-solid fa-masks-theater",
        hue: 8,
        chroma: 0.13,
        lightness: 0.58,
    },
    troubadour: {
        key: "troubadour",
        name: "Troubadour",
        icon: "fa-solid fa-feather-pointed",
        hue: 42,
        chroma: 0.11,
        lightness: 0.64,
    },
    voyant: {
        key: "voyant",
        name: "Voyant",
        icon: "fa-solid fa-eye",
        hue: 350,
        chroma: 0.14,
        lightness: 0.55,
    },
};

/** @type {Record<string, Record<string, SpeStyleToken>>} */
const BY_SPECIALIZATION = {
    artiste: ARTISTE_STYLES,
};

/**
 * @param {string} styleKey
 * @param {string} [specializationSlug='artiste']
 * @returns {SpeStyleToken|null}
 */
export function resolveSpecializationStyle(styleKey, specializationSlug = "artiste") {
    const key = String(styleKey || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{M}/gu, "");
    if (!key) return null;
    const bag =
        BY_SPECIALIZATION[String(specializationSlug || "artiste").toLowerCase()] || ARTISTE_STYLES;
    if (bag[key]) return bag[key];
    for (const token of Object.values(bag)) {
        const name = String(token.name || "")
            .toLowerCase()
            .normalize("NFD")
            .replace(/\p{M}/gu, "");
        if (name === key) return token;
    }
    return null;
}

/**
 * @param {string} [specializationSlug='artiste']
 * @returns {SpeStyleToken[]}
 */
export function listSpecializationStyles(specializationSlug = "artiste") {
    const bag =
        BY_SPECIALIZATION[String(specializationSlug || "artiste").toLowerCase()] || ARTISTE_STYLES;
    return Object.values(bag);
}

/**
 * Couleur dominante d’un style (texte / accent glass).
 *
 * @param {SpeStyleToken|null|undefined} token
 * @returns {string}
 */
export function specializationStyleAccentColor(token) {
    if (!token) return "";
    const hue = Number(token.hue);
    const chroma = Number(token.chroma ?? 0.12);
    const lightness = Number(token.lightness ?? 0.68);
    if (!Number.isFinite(hue)) return "";
    return `oklch(${lightness} ${chroma} ${hue})`;
}

/**
 * Style CSS inline pour pastille de variante.
 * `marker` = titre lisible (fond clair, pas un badge compétence).
 *
 * @param {SpeStyleToken|null|undefined} token
 * @param {{ appearance?: 'marker'|'badge' }} [options]
 * @returns {Record<string, string>|undefined}
 */
export function specializationStyleSurfaceStyle(token, options = {}) {
    if (!token) return undefined;
    const appearance = options.appearance === "badge" ? "badge" : "marker";
    const fg = specializationStyleAccentColor(token);
    if (!fg) return undefined;

    if (appearance === "marker") {
        return {
            color: fg,
            backgroundColor: "color-mix(in oklch, white 14%, transparent)",
            borderColor: "transparent",
            "--spe-style-color": fg,
            "--color": fg,
        };
    }

    const hue = Number(token.hue);
    const chroma = Number(token.chroma ?? 0.12);
    const lightness = Number(token.lightness ?? 0.68);
    const bg = `color-mix(in oklch, oklch(${Math.max(0.35, lightness - 0.2)} ${chroma} ${hue}) 28%, transparent)`;
    const border = `color-mix(in oklch, oklch(${Math.max(0.4, lightness - 0.1)} ${chroma} ${hue}) 55%, transparent)`;
    return {
        color: fg,
        backgroundColor: bg,
        borderColor: border,
        "--spe-style-color": fg,
        "--color": fg,
    };
}

/**
 * @param {string} styleKey
 * @param {string} [specializationSlug]
 * @param {{ appearance?: 'marker'|'badge' }} [options]
 * @returns {{ token: SpeStyleToken|null, style: Record<string, string>|undefined }}
 */
export function specializationStylePresentation(styleKey, specializationSlug, options = {}) {
    const token = resolveSpecializationStyle(styleKey, specializationSlug);
    return { token, style: specializationStyleSurfaceStyle(token, options) };
}
