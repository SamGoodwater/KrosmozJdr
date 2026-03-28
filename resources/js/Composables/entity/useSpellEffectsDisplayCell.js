/**
 * Résout la cellule d’affichage des effets d’un sort (résumé API ou fallback `effect`).
 *
 * @description
 * Priorité : `effect_summary` → `spell_effects` (`SpellEffectChips`, degrés D1/D2+).
 * Sinon : `effect` → chips / texte via `buildCharacteristicEffectCell`.
 *
 * @param {Object|null} spell - Instance Spell (ou équivalent avec `toCell`)
 * @param {Object} [options] - Options `toCell` ; `maxEffectRows` fixe le `maxRows` du fallback `effect`
 * @returns {Object|null}
 */
export function resolveSpellEffectsDisplayCell(spell, options = {}) {
    if (!spell || typeof spell.toCell !== "function") {
        return null;
    }
    const { maxEffectRows, ...toCellOpts } = options;
    const summary = spell.toCell("effect_summary", toCellOpts);
    if (summary?.type === "spell_effects" && (summary.params?.items?.length ?? 0) > 0) {
        return summary;
    }
    const effectOpts = {
        ...toCellOpts,
        chipsLayout: {
            maxRows: maxEffectRows ?? 4,
            ...(toCellOpts.chipsLayout || {}),
        },
    };
    return spell.toCell("effect", effectOpts);
}

/**
 * @param {Object|null} cell - Résultat de `resolveSpellEffectsDisplayCell`
 * @returns {boolean}
 */
export function spellEffectsCellHasContent(cell) {
    if (!cell) return false;
    if (cell.type === "spell_effects") {
        return (cell.params?.items?.length ?? 0) > 0;
    }
    if (cell.type === "chips") {
        return (cell.params?.items?.length ?? 0) > 0;
    }
    if (cell.type === "text") {
        const v = cell.value;
        return v != null && String(v).trim() !== "" && v !== "—" && v !== "-";
    }
    return false;
}
