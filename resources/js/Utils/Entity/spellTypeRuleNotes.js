/**
 * Notes de fiche sort (règles faciles à oublier à table).
 *
 * @description
 * Soin (type ou effet), invocation, piège/glyphe (`target_type`), vol de vie,
 * cible consentante, réaction, PV temporaires.
 *
 * @example
 * spellTypeRuleNotes({ spellTypes: [{ name: "Soin" }] });
 */

export const HEAL_COMBAT_PA_NOTE =
    "Gratuité des PA : seulement tant que le combat a un enjeu.";

export const INVOCATION_RULE_NOTE =
    "Contrôlée, joue à ton tour, 1 h puis disparaît (sauf exception). Ne laisse rien.";

export const TRAP_RULE_NOTE =
    "Caché : Perception pour le voir, se déclenche au passage. Pas sous une créature.";

export const GLYPH_RULE_NOTE =
    "Visible. Début de tour si tu es dessus (hors combat : à l'arrêt). Cumulable.";

export const LIFE_STEAL_RULE_NOTE = "Vol de vie : pas sur les boucliers.";

export const WILLING_TARGET_RULE_NOTE = "Cible consentante : réussite auto.";

export const REACTION_RULE_NOTE = "Réaction : 1 / round, hors de ton tour.";

export const TEMP_HP_RULE_NOTE = "PV temp. : ne se cumulent pas ; ne réveillent pas.";

export const TRAP_GLYPH_CAP_NOTE = "Si le max est atteint, le plus ancien disparaît.";

export const GLYPH_DISPEL_NOTE = "Dissipation : l’effet revient si tu restes dessus.";

export const SHIELD_RULE_NOTE = "Bouclier : cumulable. Pas à 0 PV. Dissipable.";

const HEAL_ACTION_SLUGS = new Set(["soigner", "heal", "heal_percent"]);
const HEAL_TYPE_NAMES = new Set(["soin", "heal"]);
const INVOCATION_ACTION_SLUGS = new Set(["invoquer", "summon"]);
const INVOCATION_TYPE_NAMES = new Set(["invocation"]);
const TEMP_HP_ACTION_SLUGS = new Set(["donner-pv-temporaires", "donner_pv_temporaires"]);
const SHIELD_ACTION_SLUGS = new Set(["proteger", "protéger", "shield"]);

/**
 * @param {unknown} value
 * @returns {string}
 */
function normalizeSlug(value) {
    return String(value ?? "")
        .trim()
        .toLowerCase();
}

/**
 * @param {object|null|undefined} entity
 * @returns {object}
 */
function spellPayload(entity) {
    if (entity == null || typeof entity !== "object") {
        return {};
    }
    if (entity._data && typeof entity._data === "object") {
        return entity._data;
    }
    return entity;
}

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function isTruthyFlag(value) {
    return value === true || value === 1 || value === "1" || value === "true";
}

/**
 * @param {object|null|undefined} entity
 * @param {string} camelKey
 * @param {string} snakeKey
 * @returns {boolean}
 */
function entityHasFlag(entity, camelKey, snakeKey) {
    if (entity == null || typeof entity !== "object") {
        return false;
    }
    const data = spellPayload(entity);
    return isTruthyFlag(entity[camelKey] ?? entity[snakeKey] ?? data[snakeKey] ?? data[camelKey]);
}

/**
 * @param {object} entity
 * @param {object} data
 * @returns {unknown[]}
 */
function collectSpellTypes(entity, data) {
    const lists = [entity.spellTypes, entity.spell_types, data.spellTypes, data.spell_types];
    const out = [];
    for (const list of lists) {
        if (Array.isArray(list)) {
            out.push(...list);
        }
    }
    return out;
}

/**
 * @param {unknown} type
 * @param {Set<string>} names
 * @returns {boolean}
 */
function typeNameInSet(type, names) {
    if (typeof type === "string") {
        return names.has(normalizeSlug(type));
    }
    if (type == null || typeof type !== "object") {
        return false;
    }
    return names.has(normalizeSlug(type.name ?? type.label ?? type.slug));
}

/**
 * @param {object|null|undefined} entity
 * @param {Set<string>} names
 * @returns {boolean}
 */
function entityHasSpellType(entity, names) {
    if (entity == null || typeof entity !== "object") {
        return false;
    }
    return collectSpellTypes(entity, spellPayload(entity)).some((type) => typeNameInSet(type, names));
}

/**
 * @param {unknown} node
 * @param {Set<string>} slugs
 * @returns {boolean}
 */
function nodeHasActionSlug(node, slugs) {
    if (node == null || typeof node !== "object") {
        return false;
    }
    const candidates = [node.action_slug, node.actionSlug, node.slug, node.type_slug, node.typeSlug];
    return candidates.some((s) => slugs.has(normalizeSlug(s)));
}

/**
 * @param {unknown} node
 * @returns {boolean}
 */
function nodeLooksLikeHeal(node) {
    if (nodeHasActionSlug(node, HEAL_ACTION_SLUGS)) {
        return true;
    }
    if (node == null || typeof node !== "object") {
        return false;
    }
    return normalizeSlug(node.characteristic) === "soin_spell";
}

/**
 * @param {unknown} node
 * @returns {boolean}
 */
function nodeLooksLikeLifeSteal(node) {
    if (node == null || typeof node !== "object") {
        return false;
    }
    const direct = node.life_steal_formula ?? node.lifeStealFormula;
    if (typeof direct === "string" && direct.trim() !== "") {
        return true;
    }
    const params = node.params;
    if (params && typeof params === "object") {
        const nested = params.life_steal_formula ?? params.lifeStealFormula;
        return typeof nested === "string" && nested.trim() !== "";
    }
    return false;
}

/**
 * @param {unknown} def
 * @param {(node: unknown) => boolean} pred
 * @returns {boolean}
 */
function definitionMatches(def, pred) {
    if (def == null || typeof def !== "object") {
        return false;
    }
    if (pred(def)) {
        return true;
    }
    const sub = def.sub_effect ?? def.subEffect;
    if (pred(sub)) {
        return true;
    }
    const degrees = def.degrees;
    if (Array.isArray(degrees) && degrees.some((d) => definitionMatches(d, pred))) {
        return true;
    }
    const rows = def.rows;
    return Array.isArray(rows) && rows.some((r) => definitionMatches(r, pred));
}

/**
 * @param {object|null|undefined} entity
 * @param {(node: unknown) => boolean} pred
 * @returns {boolean}
 */
function entityHasMatchingEffect(entity, pred) {
    if (entity == null || typeof entity !== "object") {
        return false;
    }
    const data = spellPayload(entity);
    const chips = entity.effectUsagesChips ?? data.effect_usages_chips ?? data.effectUsagesChips;
    if (Array.isArray(chips) && chips.some(pred)) {
        return true;
    }
    const defs = entity.effectsDefinitions ?? data.effects_definitions ?? data.effectsDefinitions;
    return Array.isArray(defs) && defs.some((def) => definitionMatches(def, pred));
}

/**
 * @param {unknown} value
 * @returns {string}
 */
function normalizeTargetType(value) {
    const s = normalizeSlug(value);
    if (s === "trap" || s === "piege" || s === "piège") {
        return "trap";
    }
    if (s === "glyph" || s === "glyphe") {
        return "glyph";
    }
    return "";
}

/**
 * @param {object|null|undefined} entity
 * @returns {Set<string>}
 */
function collectEffectTargetTypes(entity) {
    const types = new Set();
    if (entity == null || typeof entity !== "object") {
        return types;
    }
    const data = spellPayload(entity);
    const candidates = [entity.targetType, entity.target_type, data.target_type, data.targetType];
    const chips = entity.effectUsagesChips ?? data.effect_usages_chips ?? data.effectUsagesChips;
    if (Array.isArray(chips)) {
        for (const chip of chips) {
            if (chip && typeof chip === "object") {
                candidates.push(chip.target_type, chip.targetType);
            }
        }
    }
    const walk = (node) => {
        if (node == null || typeof node !== "object") {
            return;
        }
        if (Array.isArray(node)) {
            for (const item of node) {
                walk(item);
            }
            return;
        }
        candidates.push(node.target_type, node.targetType);
        if (Array.isArray(node.degrees)) {
            walk(node.degrees);
        }
        if (Array.isArray(node.rows)) {
            walk(node.rows);
        }
    };
    walk(entity.effectsDefinitions ?? data.effects_definitions ?? data.effectsDefinitions);
    for (const raw of candidates) {
        const t = normalizeTargetType(raw);
        if (t) {
            types.add(t);
        }
    }
    return types;
}

export function spellHasHealType(entity) {
    return entityHasSpellType(entity, HEAL_TYPE_NAMES);
}

export function spellHasHealEffect(entity) {
    return entityHasMatchingEffect(entity, nodeLooksLikeHeal);
}

export function spellHasInvocationType(entity) {
    return entityHasSpellType(entity, INVOCATION_TYPE_NAMES);
}

export function spellHasInvocationEffect(entity) {
    return entityHasMatchingEffect(entity, (n) => nodeHasActionSlug(n, INVOCATION_ACTION_SLUGS));
}

export function spellHealCombatPaNote(entity) {
    return spellHasHealType(entity) || spellHasHealEffect(entity) ? HEAL_COMBAT_PA_NOTE : null;
}

export function spellShowsHealCombatPaNote(entity) {
    return spellHealCombatPaNote(entity) != null;
}

export function spellShowsInvocationRuleNote(entity) {
    return spellHasInvocationType(entity) || spellHasInvocationEffect(entity);
}

export function spellHasTrapEffect(entity) {
    return collectEffectTargetTypes(entity).has("trap");
}

export function spellHasGlyphEffect(entity) {
    return collectEffectTargetTypes(entity).has("glyph");
}

export function spellHasLifeStealEffect(entity) {
    return entityHasMatchingEffect(entity, nodeLooksLikeLifeSteal);
}

export function spellHasWillingAutoSuccess(entity) {
    return entityHasFlag(entity, "autoSuccessIfWillingTarget", "auto_success_if_willing_target");
}

export function spellAllowsReaction(entity) {
    return entityHasFlag(entity, "allowsReaction", "allows_reaction");
}

export function spellHasTempHpEffect(entity) {
    return entityHasMatchingEffect(entity, (node) => nodeHasActionSlug(node, TEMP_HP_ACTION_SLUGS));
}

export function spellHasShieldEffect(entity) {
    return entityHasMatchingEffect(entity, (node) => {
        if (nodeHasActionSlug(node, SHIELD_ACTION_SLUGS)) {
            return true;
        }
        if (node == null || typeof node !== "object") {
            return false;
        }
        return normalizeSlug(node.characteristic) === "bouclier_spell";
    });
}

/**
 * @param {object|null|undefined} entity
 * @returns {string[]}
 */
export function spellTypeRuleNotes(entity) {
    const notes = [];
    if (spellHasHealType(entity) || spellHasHealEffect(entity)) {
        notes.push(HEAL_COMBAT_PA_NOTE);
    }
    if (spellHasInvocationType(entity) || spellHasInvocationEffect(entity)) {
        notes.push(INVOCATION_RULE_NOTE);
    }
    if (spellHasTrapEffect(entity)) {
        notes.push(TRAP_RULE_NOTE);
    }
    if (spellHasGlyphEffect(entity)) {
        notes.push(GLYPH_RULE_NOTE);
        notes.push(GLYPH_DISPEL_NOTE);
    }
    if (spellHasTrapEffect(entity) || spellHasGlyphEffect(entity)) {
        notes.push(TRAP_GLYPH_CAP_NOTE);
    }
    if (spellHasLifeStealEffect(entity)) {
        notes.push(LIFE_STEAL_RULE_NOTE);
    }
    if (spellHasWillingAutoSuccess(entity)) {
        notes.push(WILLING_TARGET_RULE_NOTE);
    }
    if (spellAllowsReaction(entity)) {
        notes.push(REACTION_RULE_NOTE);
    }
    if (spellHasTempHpEffect(entity)) {
        notes.push(TEMP_HP_RULE_NOTE);
    }
    if (spellHasShieldEffect(entity)) {
        notes.push(SHIELD_RULE_NOTE);
    }
    return notes;
}
