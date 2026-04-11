/**
 * Présentation unifiée des sous-effets (journal + chips table).
 *
 * @see docs/30-UI/SPELL_SUB_EFFECTS_DISPLAY.md
 */

/** @typedef {'large'|'compact'|'line'|'minimal'} SubEffectLayout */

/**
 * Abréviation de portée (scope pivot) affichée en tête de ligne.
 *
 * @param {string|null|undefined} scope
 * @returns {string|null}
 */
export function subEffectScopeAbbrev(scope) {
    const s = scope != null ? String(scope).trim() : "";
    if (s === "combat") {
        return "cbt";
    }
    if (s === "out_of_combat") {
        return "hors-cbt";
    }
    return null;
}

/**
 * @param {SubEffectLayout} layout
 * @returns {boolean}
 */
export function subEffectCritShowsWord(layout) {
    return layout === "large" || layout === "compact";
}

/**
 * @param {string|null|undefined} slug
 * @returns {string}
 */
export function normalizeSubEffectSlug(slug) {
    return slug != null ? String(slug).trim().toLowerCase() : "";
}

/**
 * Slug d’action aligné sur {@link SpellSubEffectActionPresentation} (legacy damage/heal…).
 *
 * @param {string|null|undefined} slug
 * @returns {string}
 */
export function resolvePresentationActionSlug(slug) {
    const s = normalizeSubEffectSlug(slug);
    if (s === "damage") {
        return "frapper";
    }
    if (s === "heal" || s === "heal_percent") {
        return "soigner";
    }
    return s;
}

/**
 * Badge valeur (formule / nombre affiché).
 *
 * @param {string} text
 * @returns {string}
 */
export function valueBadgeText(text) {
    const t = text != null ? String(text).trim() : "";
    return t;
}

/**
 * Libellé durée à partir d’une formule pivot (définitions) ou chaîne déjà formatée (chips).
 *
 * @param {string|null|undefined} durationFormula
 * @param {string|null|undefined} durationLabel
 * @returns {string}
 */
export function subEffectDurationSegment(durationFormula, durationLabel) {
    const f = durationFormula != null ? String(durationFormula).trim() : "";
    if (f !== "") {
        return f;
    }
    const l = durationLabel != null ? String(durationLabel).trim() : "";
    if (l !== "" && l.toLowerCase() !== "immédiat") {
        return l;
    }
    return "";
}

/**
 * Modèle unique pour le rendu d’une ligne d’action (définition ou chip API).
 *
 * @param {{ source: 'row', row: object, layout: SubEffectLayout, degreeArea?: string|null } | { source: 'chip', chip: object, layout: SubEffectLayout }} input
 * @returns {object}
 */
export function buildUnifiedSubEffectModel(input) {
    const layout = input.layout ?? "large";
    if (input.source === "row") {
        const row = input.row && typeof input.row === "object" ? input.row : {};
        const params =
            row.params && typeof row.params === "object" ? row.params : {};
        const sub = row.sub_effect && typeof row.sub_effect === "object" ? row.sub_effect : {};
        const df = row.duration_formula;
        const cellsF =
            typeof params.cells_formula === "string" && params.cells_formula.trim() !== ""
                ? params.cells_formula.trim()
                : "";
        const valueDisplay = formatPivotRowValue(row);
        const slugNorm = normalizeSubEffectSlug(sub.slug);
        const moveCells =
            cellsF !== ""
                ? cellsF
                : slugNorm === "déplacer" && String(valueDisplay).trim() !== ""
                  ? String(valueDisplay).trim()
                  : "";
        return {
            layout,
            actionSlug: normalizeSubEffectSlug(sub.slug),
            critOnly: Boolean(row.crit_only),
            scope: row.scope ?? null,
            area:
                input.degreeArea != null && String(input.degreeArea).trim() !== ""
                    ? String(input.degreeArea).trim()
                    : null,
            valueDisplay,
            valueFormula:
                typeof params.value_formula === "string" && params.value_formula.trim() !== ""
                    ? params.value_formula.trim()
                    : null,
            valueFormulaCrit:
                typeof params.value_formula_crit === "string" &&
                params.value_formula_crit.trim() !== ""
                    ? params.value_formula_crit.trim()
                    : null,
            lifeStealFormula:
                typeof params.life_steal_formula === "string" &&
                params.life_steal_formula.trim() !== ""
                    ? params.life_steal_formula.trim()
                    : null,
            characteristic:
                typeof params.characteristic === "string" && params.characteristic.trim() !== ""
                    ? params.characteristic.trim()
                    : null,
            element: params.element,
            durationFormula: typeof df === "string" && df.trim() !== "" ? df.trim() : null,
            durationLabel: null,
            summonMonster:
                row.summon_monster &&
                typeof row.summon_monster === "object" &&
                row.summon_monster.id != null
                    ? row.summon_monster
                    : null,
            spellState:
                row.spell_state && typeof row.spell_state === "object" && row.spell_state.id != null
                    ? row.spell_state
                    : null,
            stateName:
                typeof params.state_name === "string" && params.state_name.trim() !== ""
                    ? params.state_name.trim()
                    : null,
            cellsFormula: cellsF !== "" ? cellsF : null,
            cellsDisplay: null,
            moveCellsDisplay: moveCells,
            teleport: Boolean(params.teleport),
            textFallback: null,
            rawTextValue: null,
        };
    }

    const chip = input.chip && typeof input.chip === "object" ? input.chip : {};
    const elRaw = chip.element;
    const elNum =
        elRaw != null && elRaw !== "" && Number.isFinite(Number(elRaw)) ? Number(elRaw) : null;
    const rawText =
        chip.value != null && String(chip.value).trim() !== "" ? String(chip.value).trim() : "";
    const vf =
        typeof chip.value_formula === "string" && chip.value_formula.trim() !== ""
            ? chip.value_formula.trim()
            : "";
    const cd =
        typeof chip.cells_display === "string" && chip.cells_display.trim() !== ""
            ? chip.cells_display.trim()
            : "";
    const valueDisplay = vf !== "" ? vf : rawText;
    const slugChip = normalizeSubEffectSlug(chip.action_slug);
    const moveCellsChip =
        cd !== ""
            ? cd
            : slugChip === "déplacer" && String(valueDisplay).trim() !== ""
              ? String(valueDisplay).trim()
              : "";
    return {
        layout,
        actionSlug: normalizeSubEffectSlug(chip.action_slug),
        critOnly: Boolean(chip.crit_only),
        scope: chip.scope ?? null,
        area: chip.area != null && String(chip.area).trim() !== "" ? String(chip.area).trim() : null,
        valueDisplay,
        valueFormula:
            typeof chip.value_formula === "string" && chip.value_formula.trim() !== ""
                ? chip.value_formula.trim()
                : null,
        valueFormulaCrit:
            typeof chip.value_formula_crit === "string" && chip.value_formula_crit.trim() !== ""
                ? chip.value_formula_crit.trim()
                : null,
        lifeStealFormula:
            typeof chip.life_steal_formula === "string" && chip.life_steal_formula.trim() !== ""
                ? chip.life_steal_formula.trim()
                : null,
        characteristic:
            chip.characteristic != null && String(chip.characteristic).trim() !== ""
                ? String(chip.characteristic).trim()
                : null,
        element: elNum,
        durationFormula:
            chip.duration_formula != null && String(chip.duration_formula).trim() !== ""
                ? String(chip.duration_formula).trim()
                : null,
        durationLabel:
            chip.duration_label != null && String(chip.duration_label).trim() !== ""
                ? String(chip.duration_label).trim()
                : null,
        summonMonster:
            chip.summon_monster &&
            typeof chip.summon_monster === "object" &&
            chip.summon_monster.id != null
                ? chip.summon_monster
                : null,
        spellState: null,
        stateName:
            typeof chip.state_name === "string" && chip.state_name.trim() !== ""
                ? chip.state_name.trim()
                : null,
        cellsFormula: null,
        cellsDisplay: cd !== "" ? cd : null,
        moveCellsDisplay: moveCellsChip,
        teleport: Boolean(chip.teleport),
        textFallback: rawText,
        rawTextValue: rawText,
    };
}

/**
 * Valeur affichée sur le pivot (formule, dés, min–max).
 *
 * @param {object} row
 * @returns {string}
 */
export function formatPivotRowValue(row) {
    const r = row && typeof row === "object" ? row : {};
    const p = r.params && typeof r.params === "object" ? r.params : {};
    const vf = p.value_formula;
    if (typeof vf === "string" && vf.trim() !== "") {
        return vf.trim();
    }
    const dn = r.dice_num;
    const ds = r.dice_side;
    if (dn != null && ds != null) {
        return `${dn}d${ds}`;
    }
    const vmin = r.value_min;
    const vmax = r.value_max;
    if (vmin != null && vmax != null) {
        if (Number(vmin) === Number(vmax)) {
            return String(vmin);
        }
        return `${vmin}–${vmax}`;
    }
    if (vmin != null) {
        return String(vmin);
    }
    return "";
}
