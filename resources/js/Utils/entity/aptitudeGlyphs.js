/**
 * Glyphs d’aptitudes — icône FA + teinte stables par nom (ou id).
 *
 * Forme de fond (bouclier) côté AptitudeIcon ; ici uniquement le pictogramme.
 * Mapping explicite des 37 aptitudes seedées + repli par mots-clés / hash.
 *
 * @example
 * resolveAptitudeGlyph({ name: 'Politicien' })
 * // → { icon: 'fa-solid fa-landmark', hue: 318, chroma: 0.14, lightness: 0.62 }
 */

/**
 * @typedef {{ icon: string, hue: number, chroma?: number, lightness?: number }} AptitudeGlyph
 */

/** @type {Record<string, AptitudeGlyph>} */
const BY_NAME = {
    politicien: { icon: "fa-solid fa-landmark", hue: 318, chroma: 0.14, lightness: 0.58 },
    "expertise en wakfu": { icon: "fa-solid fa-wand-magic-sparkles", hue: 292, chroma: 0.16, lightness: 0.62 },
    "freres d armes": { icon: "fa-solid fa-handshake-angle", hue: 24, chroma: 0.12, lightness: 0.58 },
    "position d autorite": { icon: "fa-solid fa-crown", hue: 45, chroma: 0.14, lightness: 0.62 },
    general: { icon: "fa-solid fa-flag", hue: 8, chroma: 0.15, lightness: 0.55 },
    "argot des voleurs": { icon: "fa-solid fa-user-secret", hue: 260, chroma: 0.1, lightness: 0.52 },
    "furtivite supreme": { icon: "fa-solid fa-eye-slash", hue: 230, chroma: 0.08, lightness: 0.48 },
    insaisissable: { icon: "fa-solid fa-wind", hue: 200, chroma: 0.1, lightness: 0.6 },
    "refuge du pelerin": { icon: "fa-solid fa-campground", hue: 35, chroma: 0.12, lightness: 0.55 },
    "medecine de terrain": { icon: "fa-solid fa-kit-medical", hue: 350, chroma: 0.14, lightness: 0.55 },
    "haranguer les foules": { icon: "fa-solid fa-bullhorn", hue: 15, chroma: 0.16, lightness: 0.58 },
    "manipulateur subtil": { icon: "fa-solid fa-hand", hue: 280, chroma: 0.12, lightness: 0.55 },
    "jamais vraiment perdu": { icon: "fa-solid fa-compass", hue: 165, chroma: 0.12, lightness: 0.5 },
    rituel: { icon: "fa-solid fa-fire", hue: 25, chroma: 0.16, lightness: 0.55 },
    "oeil de l eclaireur euse": { icon: "fa-solid fa-binoculars", hue: 145, chroma: 0.12, lightness: 0.5 },
    "reseau d ateliers": { icon: "fa-solid fa-gears", hue: 210, chroma: 0.1, lightness: 0.52 },
    "gestes economiques": { icon: "fa-solid fa-coins", hue: 55, chroma: 0.14, lightness: 0.58 },
    "maitre d un geste": { icon: "fa-solid fa-hand-fist", hue: 5, chroma: 0.14, lightness: 0.52 },
    "remise de comptoir": { icon: "fa-solid fa-tags", hue: 330, chroma: 0.12, lightness: 0.55 },
    "oreilles partout": { icon: "fa-solid fa-ear-listen", hue: 185, chroma: 0.1, lightness: 0.52 },
    "reputation de place": { icon: "fa-solid fa-medal", hue: 48, chroma: 0.14, lightness: 0.58 },
    "les betes te jaugent": { icon: "fa-solid fa-paw", hue: 30, chroma: 0.12, lightness: 0.5 },
    "peau des saisons": { icon: "fa-solid fa-cloud-sun", hue: 75, chroma: 0.12, lightness: 0.58 },
    "sang contre poison": { icon: "fa-solid fa-flask", hue: 130, chroma: 0.14, lightness: 0.48 },
    "estomac de mer": { icon: "fa-solid fa-anchor", hue: 205, chroma: 0.12, lightness: 0.48 },
    "yeux de vigie": { icon: "fa-solid fa-tower-observation", hue: 220, chroma: 0.1, lightness: 0.5 },
    "langue des quais": { icon: "fa-solid fa-comments", hue: 195, chroma: 0.1, lightness: 0.55 },
    "tenue correcte exigee": { icon: "fa-solid fa-user-tie", hue: 250, chroma: 0.08, lightness: 0.48 },
    "memoire des blasons": { icon: "fa-solid fa-shield-halved", hue: 40, chroma: 0.12, lightness: 0.52 },
    "nom qui ouvre": { icon: "fa-solid fa-key", hue: 50, chroma: 0.14, lightness: 0.55 },
    "memoire des formules": { icon: "fa-solid fa-scroll", hue: 270, chroma: 0.12, lightness: 0.55 },
    "oreille du public": { icon: "fa-solid fa-users", hue: 310, chroma: 0.12, lightness: 0.55 },
    "presence de scene": { icon: "fa-solid fa-masks-theater", hue: 340, chroma: 0.14, lightness: 0.55 },
    "habitude de la route": { icon: "fa-solid fa-road", hue: 28, chroma: 0.1, lightness: 0.5 },
    "main legere": { icon: "fa-solid fa-hand-sparkles", hue: 300, chroma: 0.12, lightness: 0.58 },
    "troupe familiere": { icon: "fa-solid fa-people-group", hue: 18, chroma: 0.12, lightness: 0.55 },
    "legende vivante": { icon: "fa-solid fa-book-open", hue: 285, chroma: 0.14, lightness: 0.55 },
};

/** @type {Array<{ test: RegExp, glyph: AptitudeGlyph }>} */
const KEYWORD_RULES = [
    { test: /polit|autorit|cour|roi|reine/, glyph: { icon: "fa-solid fa-landmark", hue: 318 } },
    { test: /wakfu|magie|sort|rituel/, glyph: { icon: "fa-solid fa-wand-magic-sparkles", hue: 292 } },
    { test: /arme|frere|soeur|alli/, glyph: { icon: "fa-solid fa-handshake-angle", hue: 24 } },
    { test: /furtif|ombre|voleur|argot|insaisiss/, glyph: { icon: "fa-solid fa-user-secret", hue: 260 } },
    { test: /medec|soin|poison|sang/, glyph: { icon: "fa-solid fa-kit-medical", hue: 350 } },
    { test: /oeil|yeux|vigie|eclaire/, glyph: { icon: "fa-solid fa-eye", hue: 145 } },
    { test: /oreille|ouie|ecoute/, glyph: { icon: "fa-solid fa-ear-listen", hue: 185 } },
    { test: /mer|navire|quai|ancre|bateau/, glyph: { icon: "fa-solid fa-anchor", hue: 205 } },
    { test: /bete|animal|troupe|familier/, glyph: { icon: "fa-solid fa-paw", hue: 30 } },
    { test: /route|voyage|pelerin|chemin/, glyph: { icon: "fa-solid fa-road", hue: 28 } },
    { test: /scene|public|theatre|foule/, glyph: { icon: "fa-solid fa-masks-theater", hue: 340 } },
    { test: /memoire|blason|formule|livre|legende/, glyph: { icon: "fa-solid fa-scroll", hue: 270 } },
    { test: /commerce|comptoir|remise|atelier|geste/, glyph: { icon: "fa-solid fa-coins", hue: 55 } },
    { test: /cle|nom qui|ouvre/, glyph: { icon: "fa-solid fa-key", hue: 50 } },
];

/** Pool de repli déterministe (hash du nom / id). */
const FALLBACK_ICONS = [
    "fa-solid fa-sparkles",
    "fa-solid fa-star",
    "fa-solid fa-gem",
    "fa-solid fa-leaf",
    "fa-solid fa-moon",
    "fa-solid fa-sun",
    "fa-solid fa-bolt",
    "fa-solid fa-heart",
    "fa-solid fa-feather",
    "fa-solid fa-ring",
    "fa-solid fa-dice",
    "fa-solid fa-puzzle-piece",
];

/**
 * Normalise un libellé pour lookup (sans accents, minuscule, espaces simples).
 *
 * @param {string} value
 * @returns {string}
 */
export function normalizeAptitudeName(value) {
    return String(value || "")
        .trim()
        .toLowerCase()
        .replace(/œ/g, "oe")
        .replace(/æ/g, "ae")
        .normalize("NFD")
        .replace(/\p{M}/gu, "")
        .replace(/['’]/g, " ")
        .replace(/[^a-z0-9]+/g, " ")
        .replace(/\s+/g, " ")
        .trim();
}

/**
 * @param {string} seed
 * @returns {number}
 */
function hashSeed(seed) {
    let h = 2166136261;
    const s = String(seed || "");
    for (let i = 0; i < s.length; i += 1) {
        h ^= s.charCodeAt(i);
        h = Math.imul(h, 16777619);
    }
    return h >>> 0;
}

/**
 * @param {string} seed
 * @returns {AptitudeGlyph}
 */
function glyphFromHash(seed) {
    const h = hashSeed(seed);
    return {
        icon: FALLBACK_ICONS[h % FALLBACK_ICONS.length],
        hue: h % 360,
        chroma: 0.12,
        lightness: 0.55,
    };
}

/**
 * Résout le pictogramme d’une aptitude.
 *
 * @param {{ name?: string, id?: number|string, label?: string }|string|null|undefined} aptitude
 * @returns {AptitudeGlyph}
 */
export function resolveAptitudeGlyph(aptitude) {
    const raw =
        typeof aptitude === "string"
            ? aptitude
            : aptitude?.name || aptitude?.label || "";
    const key = normalizeAptitudeName(raw);
    if (key && BY_NAME[key]) {
        return { chroma: 0.12, lightness: 0.55, ...BY_NAME[key] };
    }
    for (const rule of KEYWORD_RULES) {
        if (key && rule.test.test(key)) {
            return { chroma: 0.12, lightness: 0.55, ...rule.glyph };
        }
    }
    const seed = key || String(aptitude?.id ?? "aptitude");
    return glyphFromHash(seed);
}

/**
 * Couleur de fond du bouclier (oklch).
 *
 * @param {AptitudeGlyph|null|undefined} glyph
 * @returns {string}
 */
export function aptitudeGlyphFill(glyph) {
    if (!glyph) return "var(--color-aptitude-700, #a21caf)";
    const hue = Number(glyph.hue);
    if (!Number.isFinite(hue)) return "var(--color-aptitude-700, #a21caf)";
    const chroma = Number(glyph.chroma ?? 0.12);
    const lightness = Number(glyph.lightness ?? 0.55);
    return `oklch(${lightness} ${chroma} ${hue})`;
}
