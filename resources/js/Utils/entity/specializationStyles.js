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

/**
 * Une teinte par spécialisation, assez écartée pour que les styles se lisent entre eux.
 * L'icône est un glyphe nu : pas de pastille pleine derrière.
 *
 * @param {number} hue
 * @param {string} key
 * @param {string} name
 * @param {string} icon
 * @param {number} [chroma]
 * @param {number} [lightness]
 * @returns {SpeStyleToken}
 */
function styleToken(hue, key, name, icon, chroma = 0.14, lightness = 0.68) {
    return { key, name, icon, hue, chroma, lightness };
}

/** @type {Record<string, Record<string, SpeStyleToken>>} */
const BY_SPECIALIZATION = {
    artiste: ARTISTE_STYLES,
    "artisan-e": {
        forgeron: styleToken(28, "forgeron", "Forgeron", "fa-solid fa-hammer", 0.14, 0.7),
        faconneur: styleToken(55, "faconneur", "Façonneur", "fa-solid fa-scissors", 0.1, 0.62),
        alchimiste: styleToken(145, "alchimiste", "Alchimiste", "fa-solid fa-flask", 0.12, 0.64),
    },
    devot: {
        officiant: styleToken(85, "officiant", "Officiant", "fa-solid fa-book-open", 0.1, 0.72),
        soignant: styleToken(155, "soignant", "Soignant", "fa-solid fa-hand-holding-heart", 0.12, 0.66),
        pelerin: styleToken(45, "pelerin", "Pèlerin", "fa-solid fa-person-walking", 0.1, 0.68),
    },
    "milicien-ne": {
        bonta: styleToken(250, "bonta", "Bonta", "fa-solid fa-shield-halved", 0.1, 0.72),
        brakmar: styleToken(15, "brakmar", "Brâkmar", "fa-solid fa-fire", 0.16, 0.62),
        astrub: styleToken(95, "astrub", "Astrub", "fa-solid fa-scale-balanced", 0.12, 0.7),
        amakna: styleToken(55, "amakna", "Amakna", "fa-solid fa-house", 0.1, 0.66),
    },
    "courtisan-e": {
        secretaire: styleToken(265, "secretaire", "Secrétaire", "fa-solid fa-feather", 0.1, 0.7),
        emissaire: styleToken(330, "emissaire", "Émissaire", "fa-solid fa-scroll", 0.12, 0.66),
        "favori-te": styleToken(300, "favori-te", "Favori·te", "fa-solid fa-crown", 0.12, 0.72),
        "espion-ne": styleToken(230, "espion-ne", "Espion·ne", "fa-solid fa-user-secret", 0.08, 0.6),
    },
    "explorateur-rice": {
        guide: styleToken(145, "guide", "Guide", "fa-solid fa-compass", 0.1, 0.68),
        "pisteur-euse": styleToken(80, "pisteur-euse", "Pisteur·euse", "fa-solid fa-shoe-prints", 0.1, 0.62),
        "eclaireur-euse": styleToken(200, "eclaireur-euse", "Éclaireur·euse", "fa-solid fa-binoculars", 0.08, 0.66),
        "campeur-euse": styleToken(40, "campeur-euse", "Campeur·euse", "fa-solid fa-campground", 0.12, 0.64),
    },
    "voleur-euse": {
        "tire-laine": styleToken(300, "tire-laine", "Tire-laine", "fa-solid fa-hand", 0.1, 0.68),
        "cambrioleur-euse": styleToken(265, "cambrioleur-euse", "Cambrioleur·euse", "fa-solid fa-key", 0.08, 0.66),
        ombre: styleToken(275, "ombre", "Ombre", "fa-solid fa-moon", 0.08, 0.6),
        gouttiere: styleToken(220, "gouttiere", "Gouttière", "fa-solid fa-house-chimney", 0.06, 0.64),
    },
    "marin-e": {
        matelot: styleToken(230, "matelot", "Matelot", "fa-solid fa-anchor", 0.1, 0.66),
        timonier: styleToken(200, "timonier", "Timonier", "fa-solid fa-compass", 0.1, 0.7),
        corsaire: styleToken(255, "corsaire", "Corsaire", "fa-solid fa-flag", 0.12, 0.62),
        "pecheur-euse": styleToken(185, "pecheur-euse", "Pêcheur·euse", "fa-solid fa-fish", 0.1, 0.66),
    },
    "negociant-e": {
        comptoir: styleToken(75, "comptoir", "Comptoir", "fa-solid fa-scale-balanced", 0.12, 0.7),
        caravanier: styleToken(45, "caravanier", "Caravanier", "fa-solid fa-boxes-stacked", 0.1, 0.64),
        courtier: styleToken(90, "courtier", "Courtier", "fa-solid fa-handshake", 0.1, 0.68),
        "marche-gris": styleToken(280, "marche-gris", "Marché gris", "fa-solid fa-mask", 0.08, 0.6),
    },
    "sylvain-e": {
        meute: styleToken(130, "meute", "Meute", "fa-solid fa-paw", 0.12, 0.64),
        racine: styleToken(100, "racine", "Racine", "fa-solid fa-seedling", 0.12, 0.66),
        saison: styleToken(70, "saison", "Saison", "fa-solid fa-sun", 0.14, 0.72),
        taniere: styleToken(150, "taniere", "Tanière", "fa-solid fa-tree", 0.1, 0.58),
    },
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
            backgroundColor: "transparent",
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
