/**
 * Affichage des formules de dégâts de sort : jetons courts → clés, Bonus, critique doublé.
 *
 * @example
 * abbreviateDamageFormula('1d4+[intel]', 'fire')
 * // { visible: [...], full: [...], hasBonus: true }
 */

/** @type {Readonly<Record<string, string>>} */
export const SHORT_ABILITY_TO_MOD_KEY = Object.freeze({
    intel: "modifier_intelligence_creature",
    intelligence: "modifier_intelligence_creature",
    strong: "modifier_strength_creature",
    strength: "modifier_strength_creature",
    force: "modifier_strength_creature",
    agi: "modifier_agility_creature",
    agility: "modifier_agility_creature",
    chance: "modifier_chance_creature",
    vitality: "modifier_vitality_creature",
    sagesse: "modifier_wisdom_creature",
    wisdom: "modifier_wisdom_creature",
});

/** Élément (characteristic / primary label) → bonus de dégâts fixes. */
const ELEMENT_TO_DAMAGE_BONUSES = Object.freeze({
    fire: [
        "fixed_damage_fire_creature",
        "fixed_damage_multiple_creature",
        "fixed_damage_fire_object",
        "fixed_damage_fire_consumable",
        "fixed_damage_fire_spell",
        "fixed_damage_multiple_object",
    ],
    earth: [
        "fixed_damage_earth_creature",
        "fixed_damage_multiple_creature",
        "fixed_damage_earth_object",
        "fixed_damage_earth_consumable",
        "fixed_damage_earth_spell",
        "fixed_damage_multiple_object",
    ],
    air: [
        "fixed_damage_air_creature",
        "fixed_damage_multiple_creature",
        "fixed_damage_air_object",
        "fixed_damage_air_consumable",
        "fixed_damage_air_spell",
        "fixed_damage_multiple_object",
    ],
    water: [
        "fixed_damage_water_creature",
        "fixed_damage_multiple_creature",
        "fixed_damage_water_object",
        "fixed_damage_water_consumable",
        "fixed_damage_water_spell",
        "fixed_damage_multiple_object",
    ],
    neutral: [
        "fixed_damage_neutral_creature",
        "fixed_damage_multiple_creature",
        "fixed_damage_neutral_object",
        "fixed_damage_neutral_consumable",
        "fixed_damage_neutral_spell",
        "fixed_damage_multiple_object",
    ],
});

/** Characteristic de sous-effet → clé d’élément pour les bonus. */
const CHARACTERISTIC_TO_ELEMENT = Object.freeze({
    fire: "fire",
    feu: "fire",
    earth: "earth",
    terre: "earth",
    air: "air",
    water: "water",
    eau: "water",
    neutral: "neutral",
    neutre: "neutral",
    fixed_damage_fire_spell: "fire",
    fixed_damage_earth_spell: "earth",
    fixed_damage_air_spell: "air",
    fixed_damage_water_spell: "water",
    fixed_damage_neutral_spell: "neutral",
});

/**
 * @param {string|null|undefined} characteristic
 * @returns {string|null}
 */
export function resolveDamageElementKey(characteristic) {
    if (characteristic == null) return null;
    const k = String(characteristic).trim().toLowerCase();
    if (!k) return null;
    return CHARACTERISTIC_TO_ELEMENT[k] ?? null;
}

/**
 * Remplace `[intel]` / `[strong]`… par la clé modificateur créature.
 *
 * @param {string} formula
 * @returns {string}
 */
export function expandShortAbilityTokens(formula) {
    const raw = formula != null ? String(formula) : "";
    if (!raw) return "";
    return raw.replace(/\[([a-zA-Z0-9_]+)\]/g, (match, token) => {
        const lower = String(token).toLowerCase();
        const mapped = SHORT_ABILITY_TO_MOD_KEY[lower];
        return mapped ? `[${mapped}]` : match;
    });
}

/**
 * Double chaque notation de dés (1d4 → 2d4). Les termes hors dés restent inchangés.
 *
 * @param {string} formula
 * @returns {string}
 *
 * @example
 * doubleDiceInFormula('1d4+[intel]') // '2d4+[intel]'
 */
export function doubleDiceInFormula(formula) {
    const raw = formula != null ? String(formula).trim() : "";
    if (!raw) return "";
    return raw.replace(/(\d+)\s*[dD]\s*(\d+)/g, (_, count, sides) => {
        const n = Math.max(1, Number(count) || 1);
        return `${n * 2}d${sides}`;
    });
}

/**
 * @typedef {{ type: 'text', text: string }|{ type: 'chip', key: string }|{ type: 'bonus' }} FormulaDisplayPart
 */

/**
 * Découpe une formule en texte et puces `[clé]`.
 *
 * @param {string} formula
 * @returns {FormulaDisplayPart[]}
 */
export function parseFormulaToParts(formula) {
    const raw = formula != null ? String(formula).trim() : "";
    if (!raw) return [];
    /** @type {FormulaDisplayPart[]} */
    const parts = [];
    const re = /\[([a-zA-Z0-9_]+)\]/g;
    let last = 0;
    let m;
    while ((m = re.exec(raw)) !== null) {
        if (m.index > last) {
            const text = raw.slice(last, m.index);
            if (text) parts.push({ type: "text", text });
        }
        parts.push({ type: "chip", key: m[1] });
        last = m.index + m[0].length;
    }
    if (last < raw.length) {
        const text = raw.slice(last);
        if (text) parts.push({ type: "text", text });
    }
    return parts;
}

/**
 * Ajoute les bonus de dégâts manquants après le modificateur, pour une attaque.
 *
 * @param {string} expandedFormula déjà passée par {@link expandShortAbilityTokens}
 * @param {string|null|undefined} characteristic
 * @returns {string}
 */
export function appendDamageBonuses(expandedFormula, characteristic) {
    const base = expandedFormula != null ? String(expandedFormula).trim() : "";
    if (!base) return "";
    const element = resolveDamageElementKey(characteristic);
    if (!element) return base;
    const bonuses = ELEMENT_TO_DAMAGE_BONUSES[element];
    if (!bonuses?.length) return base;
    let out = base;
    for (const key of bonuses) {
        const token = `[${key}]`;
        if (out.includes(token)) continue;
        out = `${out} + ${token}`;
    }
    return out;
}

/**
 * Formule critique affichée : stockée, ou dés doublés si Frapper sans critique.
 *
 * @param {{ actionSlug?: string|null, valueFormula?: string|null, valueFormulaCrit?: string|null }} opts
 * @returns {string}
 */
export function resolveCritFormula({ actionSlug, valueFormula, valueFormulaCrit }) {
    const crit = valueFormulaCrit != null ? String(valueFormulaCrit).trim() : "";
    if (crit) return crit;
    const slug = actionSlug != null ? String(actionSlug).trim().toLowerCase() : "";
    if (slug !== "frapper" && slug !== "damage") return "";
    const base = valueFormula != null ? String(valueFormula).trim() : "";
    if (!base) return "";
    return doubleDiceInFormula(base);
}

/**
 * Abrège une formule de dégâts : dés + premier modificateur + Bonus.
 *
 * @param {string|null|undefined} formula
 * @param {string|null|undefined} characteristic
 * @returns {{
 *   visible: FormulaDisplayPart[],
 *   full: FormulaDisplayPart[],
 *   fullFormula: string,
 *   hasBonus: boolean,
 * }}
 *
 * @example
 * abbreviateDamageFormula('1d4+[intel]', 'fire')
 */
export function abbreviateDamageFormula(formula, characteristic) {
    const expanded = expandShortAbilityTokens(formula ?? "");
    const fullFormula = appendDamageBonuses(expanded, characteristic);
    const full = parseFormulaToParts(fullFormula);

    if (!full.length) {
        return { visible: [], full: [], fullFormula: "", hasBonus: false };
    }

    /** @type {FormulaDisplayPart[]} */
    const visible = [];
    let firstChipSeen = false;
    let bonusCount = 0;

    for (const part of full) {
        if (part.type === "text") {
            if (!firstChipSeen) {
                visible.push(part);
            }
            continue;
        }
        if (part.type === "chip") {
            if (!firstChipSeen) {
                visible.push(part);
                firstChipSeen = true;
            } else {
                bonusCount += 1;
            }
        }
    }

    const hasBonus = bonusCount > 0;
    if (hasBonus) {
        if (visible.length && visible[visible.length - 1].type === "text") {
            const last = visible[visible.length - 1];
            if (!/\+\s*$/.test(last.text)) {
                visible.push({ type: "text", text: " + " });
            }
        } else if (visible.length) {
            visible.push({ type: "text", text: " + " });
        }
        visible.push({ type: "bonus" });
    }

    return { visible, full, fullFormula, hasBonus };
}

/**
 * Libellé d’onglet de degré (réexport pour tests).
 *
 * @param {object|null|undefined} deg
 * @param {number} index
 * @returns {string}
 */
export function formatSpellDegreeTabLabel(deg, index = 0) {
    const lvl = Number(deg?.required_level);
    if (!Number.isNaN(lvl) && lvl > 0) {
        return `Niv ${lvl}`;
    }
    const pos = Number(deg?.position);
    if (!Number.isNaN(pos) && pos > 0) {
        return `Niv ${pos}`;
    }
    return `Niv ${index + 1}`;
}
