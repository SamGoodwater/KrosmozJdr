/**
 * Construit les groupes de caractéristiques pour CharacteristicsCard à partir d'une créature
 * et du mapping characteristicsByDbColumn (meta.characteristics.creature.byDbColumn).
 *
 * @param {Object|null} creature - Données de la créature (life, pa, pm, strong, etc.)
 * @param {Object} byDbColumn - Map db_column → { key, name, short_name, icon, color, unit, type, descriptions, ... }
 * @returns {Array<{ title: string, characteristics: Array }>} Groupes au format attendu par CharacteristicsCard
 */
export function buildCreatureCharacteristicGroups(creature, byDbColumn = {}) {
    if (!creature || typeof creature !== "object") {
        return [];
    }

    const byDb = byDbColumn && typeof byDbColumn === "object" ? byDbColumn : {};
    const getDef = (dbColumn) => byDb[dbColumn] || { key: dbColumn, name: dbColumn, short_name: dbColumn };

    const makeFormula = (dbColumn) => {
        const def = getDef(dbColumn);
        const value = creature[dbColumn];
        if (value === null || value === undefined || value === "") return null;
        return {
            type: "formula",
            def: { ...def, key: def.key || dbColumn },
            value: String(value),
            formulaResolved: "",
            formulaRaw: "",
        };
    };

    const addFormulas = (dbColumns) =>
        dbColumns.map(makeFormula).filter(Boolean);

    const groups = [];

    // Combat : PA, PM, PO, PV, Initiative, Invocation
    const combatItems = addFormulas(["pa", "pm", "po", "life", "ini", "invocation"]);
    if (combatItems.length > 0) {
        groups.push({ title: "Combat", characteristics: combatItems });
    }

    // Stats : Force, Intel, Agi, Chance, Vitalité, Sagesse
    const statsItems = addFormulas(["strong", "intel", "agi", "chance", "vitality", "sagesse"]);
    if (statsItems.length > 0) {
        groups.push({ title: "Stats", characteristics: statsItems });
    }

    // Résistances (fixe + % par élément)
    const els = ["neutre", "terre", "feu", "air", "eau"];
    const resItems = [];
    for (const el of els) {
        const fixedDb = `res_fixe_${el}`;
        const percentDb = `res_${el}`;
        const fixed = creature[fixedDb];
        const percent = creature[percentDb];
        const hasFixed = fixed !== null && fixed !== undefined && String(fixed) !== "";
        const hasPercent = percent !== null && percent !== undefined && String(percent) !== "";
        if (!hasFixed && !hasPercent) continue;
        let display = "";
        if (hasFixed && hasPercent) display = `${fixed} (${percent}%)`;
        else if (hasFixed) display = String(fixed);
        else display = `${percent}%`;
        const def = getDef(fixedDb) || getDef(percentDb);
        resItems.push({
            type: "formula",
            def: { ...def, key: def.key || percentDb },
            value: display,
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    if (resItems.length > 0) {
        groups.push({ title: "Résistances", characteristics: resItems });
    }

    // Dégâts : touche + do_fixe par élément
    const dmgItems = [];
    const touchItem = makeFormula("touch");
    if (touchItem) dmgItems.push(touchItem);
    for (const el of els) {
        const item = makeFormula(`do_fixe_${el}`);
        if (item) dmgItems.push(item);
    }
    if (dmgItems.length > 0) {
        groups.push({ title: "Dommages", characteristics: dmgItems });
    }

    // Contrôle : CA, esquive PA/PM, fuite, tacle
    const ctrlItems = addFormulas(["ca", "dodge_pa", "dodge_pm", "fuite", "tacle"]);
    if (ctrlItems.length > 0) {
        groups.push({ title: "Contrôle", characteristics: ctrlItems });
    }

    return groups;
}
