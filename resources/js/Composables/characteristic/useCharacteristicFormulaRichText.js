import { resolveDef } from "@/Composables/entity/useCharacteristicDisplay";

const WORD_REGEX = /[A-Za-zÀ-ÿ_][A-Za-zÀ-ÿ0-9_]*/g;

/** @typedef {'descriptions_first' | 'helper_first'} TooltipOrder */

function stripDiacritics(value) {
    return String(value || "").normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

function normalizeToken(value) {
    return stripDiacritics(value)
        .toLowerCase()
        .replace(/['’`]/g, "")
        .replace(/[^a-z0-9_]+/g, "_")
        .replace(/^_+|_+$/g, "");
}

/**
 * Rend les séparateurs (+) lisibles sans multiplier les espaces.
 *
 * @param {string} raw
 * @returns {string}
 */
export function normalizeCharacteristicFormulaText(raw) {
    return String(raw || "")
        .replace(/\s+/g, " ")
        .replace(/\s*\+\s*/g, " + ")
        .trim();
}

function candidateKeys(rawToken) {
    const base = String(rawToken || "").trim();
    const normalized = normalizeToken(base);
    const noUnderscore = normalized.replace(/_/g, "");
    const withUnderscore = noUnderscore
        .replace(/(vitalite|sagesse|force|intelligence|chance|agilite|wisdom|strength|vitality)/g, "_$1")
        .replace(/^_/, "");

    return Array.from(
        new Set([
            base,
            base.toLowerCase(),
            normalized,
            noUnderscore,
            withUnderscore,
            `${normalized}_creature`,
            `${normalized}_object`,
            `${normalized}_spell`,
        ].filter((v) => v && v.length >= 2)),
    );
}

/**
 * Texte d’aide affiché au survol d’une caractéristique résolue.
 *
 * @param {Object} def
 * @param {string} token
 * @param {TooltipOrder} order
 */
function buildCharacteristicTooltip(def, token, order) {
    const desc = def?.descriptions != null ? String(def.descriptions).trim() : "";
    const helper = def?.helper != null ? String(def.helper).trim() : "";
    const name = def?.name != null ? String(def.name).trim() : "";
    const extra =
        def?._formulaTokenTooltipExtra != null
            ? String(def._formulaTokenTooltipExtra).trim()
            : "";

    if (order === "helper_first") {
        const primary = helper || desc || name;
        const base = primary || token;
        return extra ? `${base}\n\n${extra}` : base;
    }

    const parts = [];
    if (desc) parts.push(desc);
    if (helper && helper !== desc) parts.push(helper);
    const base = parts.length ? parts.join("\n\n") : name || token;
    return extra ? `${base}\n\n${extra}` : base;
}

function tryResolveToken(token, sourceGroups, tooltipOrder) {
    const triedDefs = candidateKeys(token)
        .map((key) => resolveDef(key, undefined, { sourceGroups }))
        .filter(Boolean);

    if (triedDefs.length === 0) return null;
    const def = triedDefs[0];
    return {
        token,
        def,
        label: String(def._formulaTokenLabel || def.short_name || def.name || token),
        color: def._resolvedColor ?? def.color ?? null,
        icon: def._resolvedIcon ?? def.icon ?? null,
        tooltip: buildCharacteristicTooltip(def, token, tooltipOrder),
    };
}

/**
 * Parse une formule ou un texte libre en segments enrichis (texte + références caractéristiques).
 * Tout mot qui ressemble à une clé métier (`vitality_creature`, `level_object`, …) est tenté via {@link resolveDef}.
 *
 * @param {string|null|undefined} formula
 * @param {{sourceGroups?: string[], tooltipOrder?: TooltipOrder}} [options]
 * @returns {Array<{type:'text',text:string}|{type:'characteristic',token:string,label:string,color:string|null,icon:string|null,tooltip:string,def:Object}>}
 */
export function parseCharacteristicFormulaRichText(formula, options = {}) {
    const sourceGroups = Array.isArray(options.sourceGroups) && options.sourceGroups.length
        ? options.sourceGroups
        : ["creature", "item", "resource", "consumable", "panoply", "spell", "capability"];
    const tooltipOrder = options.tooltipOrder === "helper_first" ? "helper_first" : "descriptions_first";

    const raw = normalizeCharacteristicFormulaText(formula);
    if (raw === "") {
        return [{ type: "text", text: "—" }];
    }

    const segments = [];
    let cursor = 0;
    for (const match of raw.matchAll(WORD_REGEX)) {
        const token = match[0];
        const start = match.index ?? 0;
        const end = start + token.length;

        if (start > cursor) {
            segments.push({ type: "text", text: raw.slice(cursor, start) });
        }

        const resolved = tryResolveToken(token, sourceGroups, tooltipOrder);
        if (resolved) {
            segments.push({ type: "characteristic", ...resolved });
        } else {
            segments.push({ type: "text", text: token });
        }
        cursor = end;
    }

    if (cursor < raw.length) {
        segments.push({ type: "text", text: raw.slice(cursor) });
    }

    const compacted = [];
    for (const segment of segments) {
        const prev = compacted[compacted.length - 1];
        if (segment.type === "text" && prev?.type === "text") {
            prev.text += segment.text;
        } else {
            compacted.push(segment);
        }
    }

    return compacted.length ? compacted : [{ type: "text", text: raw }];
}

/**
 * Alias explicite : enrichit un texte arbitraire (titres, aides, formules) avec les métadonnées caractéristiques.
 * Comportement identique à {@link parseCharacteristicFormulaRichText}.
 *
 * @param {string|null|undefined} text
 * @param {{sourceGroups?: string[], tooltipOrder?: TooltipOrder}} [options]
 */
export function enrichTextWithCharacteristics(text, options = {}) {
    return parseCharacteristicFormulaRichText(text, options);
}
