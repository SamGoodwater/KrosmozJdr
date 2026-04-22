import { resolveDef } from "@/Composables/entity/useCharacteristicDisplay";

const WORD_REGEX = /[A-Za-zÀ-ÿ_][A-Za-zÀ-ÿ0-9_]*/g;

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

function candidateKeys(rawToken) {
    const base = String(rawToken || "").trim();
    const normalized = normalizeToken(base);
    const noUnderscore = normalized.replace(/_/g, "");
    const withUnderscore = noUnderscore.replace(/(vitalite|sagesse|force|intelligence|chance|agilite|wisdom|strength|vitality)/g, "_$1").replace(/^_/, "");

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

function tryResolveToken(token, sourceGroups) {
    const triedDefs = candidateKeys(token)
        .map((key) => resolveDef(key, undefined, { sourceGroups }))
        .filter(Boolean);

    if (triedDefs.length === 0) return null;
    const def = triedDefs[0];
    return {
        token,
        def,
        label: String(def.short_name || def.name || token),
        color: def._resolvedColor ?? def.color ?? null,
        icon: def._resolvedIcon ?? def.icon ?? null,
        tooltip: String(def.helper || def.descriptions || def.name || token),
    };
}

/**
 * Parse une formule en segments enrichis (texte + références caractéristiques).
 *
 * @param {string|null|undefined} formula
 * @param {{sourceGroups?: string[]}} [options]
 * @returns {Array<{type:'text',text:string}|{type:'characteristic',token:string,label:string,color:string|null,icon:string|null,tooltip:string,def:Object}>}
 */
export function parseCharacteristicFormulaRichText(formula, options = {}) {
    const sourceGroups = Array.isArray(options.sourceGroups) && options.sourceGroups.length
        ? options.sourceGroups
        : ["creature", "item", "resource", "consumable", "panoply", "spell", "capability"];

    const raw = String(formula || "");
    if (raw.trim() === "") {
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

        const resolved = tryResolveToken(token, sourceGroups);
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

