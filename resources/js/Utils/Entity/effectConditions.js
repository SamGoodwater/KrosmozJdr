/**
 * Conditions et règles de valeur des effets de sort (params.conditions / value_rules).
 *
 * Syntaxe caractéristique entre crochets :
 * - `[clé]` ou `[clé:caster]` → caractéristique du lanceur
 * - `[clé:target]` → caractéristique de la cible
 * (ancienne forme `[caster:clé]` / `[target:clé]` encore lue à l’affichage)
 *
 * @example
 * formatConditionText({ left: '[modifier_agility_creature:target]', op: '>=', right: '3' })
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
 * Décompose un jeton entre crochets : clé + acteur (caster par défaut).
 *
 * @param {string} token
 * @returns {{ key: string, actor: 'caster'|'target'|null }}
 */
export function parseBracketToken(token) {
    const raw = String(token ?? "").trim();
    // Nouvelle forme : clé:acteur
    let m = /^([a-zA-Z_][a-zA-Z0-9_]*):(caster|target)$/i.exec(raw);
    if (m) {
        return { key: m[1], actor: /** @type {'caster'|'target'} */ (m[2].toLowerCase()) };
    }
    // Ancienne forme : acteur:clé
    m = /^(caster|target):([a-zA-Z_][a-zA-Z0-9_]*)$/i.exec(raw);
    if (m) {
        return { key: m[2], actor: /** @type {'caster'|'target'} */ (m[1].toLowerCase()) };
    }
    return { key: raw, actor: null };
}

/**
 * Normalise pour persistance : ignore les lignes sans formules.
 *
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
        if (out.length > 0 || row.join != null) {
            const join = String(row.join ?? "AND").toUpperCase();
            if (JOIN_SET.has(join)) item.join = join;
        }
        out.push(item);
        if (out.length >= 10) break;
    }
    return out;
}

/**
 * Copie pour l’éditeur : conserve les lignes en cours de saisie (même vides).
 *
 * @param {mixed} raw
 * @returns {Array<{ left: string, op: string, right: string, join?: string }>}
 */
export function draftConditions(raw) {
    if (!Array.isArray(raw)) return [];
    return raw.slice(0, 10).map((row, i) => {
        const src = row && typeof row === "object" ? row : {};
        /** @type {{ left: string, op: string, right: string, join?: string }} */
        const item = {
            left: String(src.left ?? ""),
            op: OP_SET.has(String(src.op ?? "")) ? String(src.op) : ">=",
            right: String(src.right ?? ""),
        };
        if (i > 0) {
            const join = String(src.join ?? "AND").toUpperCase();
            item.join = JOIN_SET.has(join) ? join : "AND";
        }
        return item;
    });
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
 * Copie pour l’éditeur : conserve les règles incomplètes.
 *
 * @param {mixed} raw
 * @returns {Array<{ conditions: ReturnType<typeof draftConditions>, action: string, formula: string }>}
 */
export function draftValueRules(raw) {
    if (!Array.isArray(raw)) return [];
    return raw.slice(0, 10).map((row) => {
        const src = row && typeof row === "object" ? row : {};
        const action = String(src.action ?? "add");
        return {
            conditions: draftConditions(src.conditions),
            action: ACTION_SET.has(action) ? action : "add",
            formula: String(src.formula ?? ""),
        };
    });
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
 * @param {mixed} raw
 * @returns {boolean}
 */
export function normalizeValueUpTo(raw) {
    return raw === true || raw === 1 || raw === "1" || raw === "true";
}

/**
 * @param {string} token contenu entre crochets
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @returns {string}
 */
export function formatBracketToken(token, resolveLabel) {
    const { key, actor } = parseBracketToken(token);
    let label = null;
    if (typeof resolveLabel === "function") {
        label = resolveLabel(key) || resolveLabel(token);
    }
    if (!label) {
        label = SHORT_LABELS[key] || key;
    }
    if (actor === "target") {
        return `${label} ${ACTOR_LABELS.target}`;
    }
    if (actor === "caster") {
        return `${label} ${ACTOR_LABELS.caster}`;
    }
    return label;
}

/**
 * @param {string} formula
 * @param {(key: string) => string|null|undefined} [resolveLabel]
 * @param {{ abbreviate?: boolean }} [opts]
 */
export function formatFormulaReadable(formula, resolveLabel, _opts = {}) {
    const src = String(formula ?? "").trim();
    if (!src) return "";
    return src.replace(/\[([^\]]+)\]/g, (_all, inner) => formatBracketToken(inner, resolveLabel));
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
export function formatAppliesToText(appliesTo, _opts = {}) {
    const v = normalizeAppliesTo(appliesTo);
    if (v === "targets") return "";
    const meta = APPLIES_TO_OPTIONS.find((a) => a.value === v);
    return meta?.short || meta?.label || "";
}

/**
 * Suggestions : clé nue (lanceur) + clé:target (cible).
 * `[clé:caster]` est optionnel (= clé nue).
 *
 * @param {Array<{ id: string, name?: string|null, short_name?: string|null }>} baseSuggestions
 * @returns {Array<{ id: string, name: string, short_name?: string|null }>}
 */
export function buildActorSuffixedSuggestions(baseSuggestions) {
    const out = [];
    for (const s of baseSuggestions ?? []) {
        const id = String(s?.id ?? "").trim();
        if (!id || id.includes(":")) continue;
        const name = String(s?.name ?? s?.short_name ?? id);
        const short = s?.short_name ?? null;
        out.push({
            id,
            name: `${name} (lanceur)`,
            short_name: short ? `${short} (lanceur)` : null,
        });
        out.push({
            id: `${id}:target`,
            name: `${name} (cible)`,
            short_name: short ? `${short} (cible)` : null,
        });
        out.push({
            id: `${id}:caster`,
            name: `${name} (lanceur, explicite)`,
            short_name: short ? `${short} (lanceur)` : null,
        });
    }
    return out;
}

/** @deprecated Utiliser {@link buildActorSuffixedSuggestions}. */
export function buildActorPrefixedSuggestions(baseSuggestions) {
    return buildActorSuffixedSuggestions(baseSuggestions);
}

/**
 * @param {number} index
 */
export function emptyCondition(index = 0) {
    /** @type {{ left: string, op: string, right: string, join?: string }} */
    const c = { left: "", op: ">=", right: "" };
    if (index > 0) c.join = "AND";
    return c;
}

export function emptyValueRule() {
    return {
        conditions: [],
        action: "add",
        formula: "",
    };
}
