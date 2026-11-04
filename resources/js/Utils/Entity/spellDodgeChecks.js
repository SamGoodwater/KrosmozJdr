/**
 * Retraits PA / PM d’un sort : chacun se résout par un jet d’attaque vs Esquive PA / PM
 * (règle 3.3.2.3), y compris sur un sort à sauvegarde. Déduit des effets, jamais stocké.
 *
 * Source des effets, dans l’ordre : lignes du degré actif (`_active_degree_rows`),
 * premier degré natif (`spell_degrees`), chips API (`effect_usages_chips`, plus petit degré).
 *
 * @example
 * detectSpellDodgeChecks(spell) // { kinds: ["pm"], hasOtherEffects: true, hasEffects: true }
 */

/** Sous-effets qui retirent la caractéristique visée à la cible. */
const REMOVAL_SLUGS = new Set(["retirer", "voler-caracteristiques"]);

const PA_KEYS = new Set(["pa", "action_points", "action_points_creature"]);
const PM_KEYS = new Set(["pm", "movement_points", "movement_points_creature"]);

/**
 * Esquive opposée à chaque retrait (clé de caractéristique créature + libellé court).
 * @type {Readonly<Record<"pa"|"pm", { key: string, label: string }>>}
 */
export const SPELL_DODGE_TARGETS = Object.freeze({
    pa: { key: "dodge_action_points_creature", label: "Esquive PA" },
    pm: { key: "dodge_movement_points_creature", label: "Esquive PM" },
});

/**
 * @param {unknown} v
 * @returns {string}
 */
function norm(v) {
    return v != null ? String(v).trim().toLowerCase() : "";
}

/**
 * Retrait PA / PM porté par une ligne de degré ou un chip API.
 *
 * @param {object|null|undefined} node
 * @returns {"pa"|"pm"|null}
 */
export function spellRemovalKind(node) {
    if (!node || typeof node !== "object") return null;
    const slug = norm(node.sub_effect?.slug ?? node.action_slug ?? node.slug);
    if (!REMOVAL_SLUGS.has(slug)) return null;
    const characteristic = norm(node.params?.characteristic ?? node.characteristic);
    if (PA_KEYS.has(characteristic)) return "pa";
    if (PM_KEYS.has(characteristic)) return "pm";
    return null;
}

/**
 * @param {object} entity
 * @returns {Array<object>}
 */
function readEffectNodes(entity) {
    const data = entity?._data && typeof entity._data === "object" ? entity._data : entity;
    const activeRows = data?._active_degree_rows;
    if (Array.isArray(activeRows) && activeRows.length > 0) return activeRows;

    const degrees = (entity?.spellDegrees ?? data?.spell_degrees)?.degrees;
    if (Array.isArray(degrees) && degrees.length > 0) {
        const rows = degrees[0]?.rows;
        if (Array.isArray(rows) && rows.length > 0) return rows;
    }

    const chips = entity?.effectUsagesChips ?? data?.effect_usages_chips;
    if (!Array.isArray(chips) || chips.length === 0) return [];
    const levels = chips.map((c) => Number(c?.degree)).filter((n) => Number.isFinite(n));
    if (!levels.length) return chips;
    const first = Math.min(...levels);
    return chips.filter((c) => Number(c?.degree) === first);
}

/**
 * @param {object|null|undefined} entity - Spell (ou `usageEntity` du degré actif)
 * @returns {{ kinds: Array<"pa"|"pm">, hasOtherEffects: boolean, hasEffects: boolean }}
 */
export function detectSpellDodgeChecks(entity) {
    const nodes = entity ? readEffectNodes(entity) : [];
    const kinds = new Set();
    let hasOtherEffects = false;
    for (const node of nodes) {
        const kind = spellRemovalKind(node);
        if (kind) kinds.add(kind);
        else if (node && typeof node === "object") hasOtherEffects = true;
    }
    return {
        kinds: ["pa", "pm"].filter((k) => kinds.has(k)),
        hasOtherEffects,
        hasEffects: nodes.length > 0,
    };
}
