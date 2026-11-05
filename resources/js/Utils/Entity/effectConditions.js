/**
 * Conditions et règles de valeur des effets de sort (params.conditions / value_rules).
 *
 * Une condition compare deux formules : left op right.
 * Une règle de valeur remplace, ajoute ou soustrait une formule si ses conditions sont vraies.
 *
 * @example
 * formatConditionText({ left: '[caster:agi]', op: '>=', right: '3' })
 * // → « Agi du lanceur ≥ 3 » (selon resolveLabel)
 */

/** @type {ReadonlyArray<{ value: string, label: string, symbol: string }>} */
export const CONDITION_OPERATORS = Object.freeze([
    { value: "<", label: "inférieur à", symbol: "<" },
    { value: "<=", label: "inférieur ou égal à", symbol: "≤" },
    { value: ">", label: "supérieur à", symbol: ">" },
    { value: ">=", label: "supérieur ou égal à", symbol: "≥" },
    { value: "=", label: "égal à", symbol: "=" },
    { value: "><", label: "différent de", symbol: "≠" },
]);

/** @type {ReadonlyArray<{ value: string, label: string }>} */
export const CONDITION_JOINS = Object.freeze([
    { value: "AND", label: "ET" },
    { value: "OR", label: "OU" },
]);

/** @type {ReadonlyArray<{ value: string, label: string, short: string }>} */
export const VALUE_RULE_ACTIONS = Object.freeze([
    { value: "replace", label: "Remplacer la valeur ci-dessus", short: "=" },
    { value: "add", label: "S’ajouter à la valeur", short: "+" },
    { value: "subtract", label: "Se soustraire de la valeur", short: "−" },
]);

/** @type {ReadonlyArray<{ value: string, label: string, short: string }>} */
export const APPLIES_TO_OPTIONS = Object.freeze([
    { value: "targets", label: "Les cibles", short: "" },
    { value: "caster", label: "Toi (lanceur)", short: "sur toi" },
    { value: "caster_and_targets", label: "Toi et les cibles", short: "toi et les cibles" },
]);

const OP_SET = new Set(CONDITION_OPERATORS.map((o) => o.value));
const JOIN_SET = new Set(CONDITION_JOINS.map((j) => j.value));
const ACTION_SET = new Set(VALUE_RULE_ACTIONS.map((a) => a.value));
const APPLIES_SET = new Set(APPLIES_TO_OPTIONS.map((a) => a.value));

const ACTOR_LABELS = Object.freeze({
    caster: "du lanceur",
    target: "de la cible",
});

/** Alias courts → libellé d’affichage de secours. */
const SHORT_LABELS = Object.freeze({
    agi: "Agi",
    agility: "Agi",
    modifier_agility_creature: "Agi",
    intel: "Intel",
    intelligence: "Intel",
    modifier_intelligence_creature: "Intel",
    strong: "Force",
    strength: "Force",
    force: "Force",
    modifier_strength_creature: "Force",
    chance: "Chance",
    modifier_chance_creature: "Chance",
    vitality: "Vitalité",
    modifier_vitality_creature: "Vitalité",
    sagesse: "Sagesse",
    wisdom: "Sagesse",
    modifier_wisdom_creature: "Sagesse",
    level: "Niv.",
    niveau: "Niv.",
});

/**
 * @param {mixed} raw
 * @returns {Array<{ left: string, op: string, right: string, join?: string }>}
 */
export function normalizeConditions(raw) {
    if (!Array.isArray(raw)) return [];
    const out = [];
    for (let i = 0; i < raw.length; i++) {
        const row = raw[i];
        if (!row || typeof row !== "object") continue;
        const left = String(row.left ?? "").trim();
        const right = String(row.right ?? "").trim();
        const op = String(row.op ?? "");
        if (!left && !right) continue;
        if (!OP_SET.has(op)) continue;
        /** @type {{ left: string, op: string, right: string, join?: string }} */
        const item = { left, op, right };
        if (i > 0 || row.join != null) {
            const join = String(row.join ?? "AND").toUpperCase();
            if (JOIN_SET.has(join)) item.join = join;
        }
        out.push(item);
        if (out.length >= 10) break;
    }
    return out;
}

/**
 * @param {mixed} raw
 * @returns {Array<{ conditions: ReturnType<typeof normalizeConditions>, action: string, formula: string }>}
 */
export function normalizeValueRules(raw) {
    if (!Array.isArray(raw)) return [];
    const out = [];
    for (const row of raw) {
        if (!row || typeof row !== "object") continue;
        const action = String(row.action ?? "");
        const formula = String(row.formula ?? "").trim();
        if (!ACTION_SET.has(action) || !formula) continue;
        out.push({
            conditions: normalizeConditions(row.conditions),
            action,
            formula,
        });
        if (out.length >= 10) break;
    }
    return out;
}

/**
 * @param {mixed} raw
 * @returns {'targets'|'caster'|'caster_and_targets'}
 */
export function normalizeAppliesTo(raw) {
    const v = String(raw ?? "");
    return APPLIES_SET.has(v) ? /** @type {'targets'|'caster'|'caster_and_targets'} */ (v) : "targets";
}

/**
 * @param {string} token contenu entre crochets, éventuellement préfixé
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @returns {string}
 */
export function formatBracketToken(token, resolveLabel) {
    const raw = String(token ?? "").trim();
    const m = /^(caster|target):(.+)$/i.exec(raw);
    const actor = m ? m[1].toLowerCase() : null;
    const key = m ? m[2] : raw;
    let label = null;
    if (typeof resolveLabel === "function") {
        label = resolveLabel(key) || resolveLabel(raw);
    }
    if (!label) {
        label = SHORT_LABELS[key] || key;
    }
    if (actor && ACTOR_LABELS[actor]) {
        return `${label} ${ACTOR_LABELS[actor]}`;
    }
    return label;
}

/**
 * Remplace les [tokens] d’une formule par des libellés lisibles.
 *
 * @param {string} formula
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @param {{ abbreviate?: boolean }} [opts]
 */
export function formatFormulaReadable(formula, resolveLabel, opts = {}) {
    const src = String(formula ?? "").trim();
    if (!src) return "";
    return src.replace(/\[([^\]]+)\]/g, (_all, inner) => {
        const readable = formatBracketToken(inner, resolveLabel);
        return opts.abbreviate ? readable : readable;
    });
}

/**
 * @param {{ left?: string, op?: string, right?: string }} condition
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @param {{ abbreviate?: boolean }} [opts]
 */
export function formatConditionText(condition, resolveLabel, opts = {}) {
    if (!condition) return "";
    const left = formatFormulaReadable(condition.left ?? "", resolveLabel, opts);
    const right = formatFormulaReadable(condition.right ?? "", resolveLabel, opts);
    const opMeta = CONDITION_OPERATORS.find((o) => o.value === condition.op);
    const op = opMeta?.symbol ?? condition.op ?? "?";
    if (!left && !right) return "";
    return `${left} ${op} ${right}`.trim();
}

/**
 * @param {Array<{ left?: string, op?: string, right?: string, join?: string }>} conditions
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @param {{ abbreviate?: boolean }} [opts]
 */
export function formatConditionsListText(conditions, resolveLabel, opts = {}) {
    const list = normalizeConditions(conditions);
    if (list.length === 0) return "";
    return list
        .map((c, i) => {
            const text = formatConditionText(c, resolveLabel, opts);
            if (i === 0) return text;
            const join = c.join === "OR" ? "ou" : "et";
            return `${join} ${text}`;
        })
        .filter(Boolean)
        .join(" ");
}

/**
 * @param {{ action?: string, formula?: string, conditions?: unknown }} rule
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @param {{ abbreviate?: boolean }} [opts]
 */
export function formatValueRuleText(rule, resolveLabel, opts = {}) {
    if (!rule) return "";
    const actionMeta = VALUE_RULE_ACTIONS.find((a) => a.value === rule.action);
    const formula = formatFormulaReadable(rule.formula ?? "", resolveLabel, opts);
    if (!formula) return "";
    const prefix =
        rule.action === "add"
            ? `+${formula}`
            : rule.action === "subtract"
              ? `−${formula}`
              : `${actionMeta?.short ?? "="} ${formula}`;
    const cond = formatConditionsListText(rule.conditions, resolveLabel, opts);
    return cond ? `${prefix} si ${cond}` : prefix;
}

/**
 * @param {string} appliesTo
 * @param {{ abbreviate?: boolean }} [opts]
 */
export function formatAppliesToText(appliesTo, opts = {}) {
    const v = normalizeAppliesTo(appliesTo);
    if (v === "targets") return opts.abbreviate ? "" : "";
    const meta = APPLIES_TO_OPTIONS.find((a) => a.value === v);
    return meta?.short || meta?.label || "";
}

/**
 * Suggestions d’autocomplétion avec préfixe lanceur / cible.
 *
 * @param {Array<{ id: string, name?: string|null, short_name?: string|null }>} baseSuggestions
 * @returns {Array<{ id: string, name: string, short_name?: string|null }>}
 */
export function buildActorPrefixedSuggestions(baseSuggestions) {
    const out = [];
    for (const s of baseSuggestions ?? []) {
        const id = String(s?.id ?? "").trim();
        if (!id) continue;
        const name = String(s?.name ?? s?.short_name ?? id);
        const short = s?.short_name ?? null;
        out.push({
            id: `caster:${id}`,
            name: `Lanceur · ${name}`,
            short_name: short ? `Lanceur · ${short}` : null,
        });
        out.push({
            id: `target:${id}`,
            name: `Cible · ${name}`,
            short_name: short ? `Cible · ${short}` : null,
        });
    }
    return out;
}

/**
 * Condition vide pour l’éditeur.
 *
 * @param {number} index
 */
export function emptyCondition(index = 0) {
    /** @type {{ left: string, op: string, right: string, join?: string }} */
    const c = { left: "", op: ">=", right: "" };
    if (index > 0) c.join = "AND";
    return c;
}

/**
 * Règle de valeur vide pour l’éditeur.
 */
export function emptyValueRule() {
    return {
        conditions: [emptyCondition(0)],
        action: "add",
        formula: "",
    };
}
