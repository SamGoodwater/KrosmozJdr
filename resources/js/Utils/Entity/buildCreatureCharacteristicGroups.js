/**
 * Construit les groupes de caractéristiques pour CharacteristicsCard à partir d'une créature
 * et des mappings meta (byDbColumn, byComputedKey).
 *
 * @param {Object|null} creature - Données de la créature (life, pa, pm, strong, etc.)
 * @param {Object} byDbColumn - Map db_column → { key, name, short_name, icon, color, unit, type, descriptions, ... }
 * @param {Object} [byComputedKey] - Map characteristic_key → définition (modificateurs, sauvegardes calculés)
 * @returns {Array<{ title: string, characteristics: Array }>} Groupes au format attendu par CharacteristicsCard
 */
export function buildCreatureCharacteristicGroups(creature, byDbColumn = {}, byComputedKey = {}) {
    if (!creature || typeof creature !== "object") {
        return [];
    }

    const byDb = byDbColumn && typeof byDbColumn === "object" ? byDbColumn : {};
    const byComp = byComputedKey && typeof byComputedKey === "object" ? byComputedKey : {};
    const getDef = (dbColumn) => byDb[dbColumn] || { key: dbColumn, name: dbColumn, short_name: dbColumn };
    const getCompDef = (key) => byComp[key] || { key, name: key, short_name: key };

    const makeFormula = (dbColumn) => {
        const def = getDef(dbColumn);
        const value = creature[dbColumn];
        if (value === null || value === undefined || value === "") return null;
        // Affichage spécial : bonus critique 0-3 → seuil (0=Nat 20, 1=Dès 19, 2=Dès 18, 3=Dès 17)
        let displayValue = String(value);
        if (dbColumn === "critical_hit") {
            const v = parseInt(value, 10);
            displayValue = v === 0 ? "Nat 20" : `Dès ${20 - v}`;
        }
        return {
            type: "formula",
            def: { ...def, key: def.key || dbColumn },
            value: displayValue,
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

    // Modificateurs : calculés (carac-10)/2, limités par min(floor(niv/2)+1, 7), min -2
    const modifierItems = [];
    const level = parseInt(creature.level, 10) || 1;
    const modMax = Math.min(Math.floor(level / 2) + 1, 7);
    const statToModKey = [
        { stat: "vitality", key: "modifier_vitality_creature" },
        { stat: "sagesse", key: "modifier_wisdom_creature" },
        { stat: "strong", key: "modifier_strength_creature" },
        { stat: "intel", key: "modifier_intelligence_creature" },
        { stat: "chance", key: "modifier_chance_creature" },
        { stat: "agi", key: "modifier_agility_creature" },
    ];
    for (const { stat, key } of statToModKey) {
        const statVal = parseInt(creature[stat], 10) || 10;
        const rawMod = Math.floor((statVal - 10) / 2);
        const mod = Math.max(Math.min(rawMod, modMax), -2);
        const def = getCompDef(key);
        modifierItems.push({
            type: "formula",
            def: { ...def, key: def.key || key },
            value: mod >= 0 ? `+${mod}` : String(mod),
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    if (modifierItems.length > 0) {
        groups.push({ title: "Modificateurs", characteristics: modifierItems });
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

    // Contrôle : CA, esquive PA/PM, fuite, tacle, retrait PA/PM, bonus critique, bonus soin
    const ctrlItems = addFormulas(["ca", "dodge_pa", "dodge_pm", "fuite", "tacle", "critical_hit", "heal_bonus"]);
    if (ctrlItems.length > 0) {
        groups.push({ title: "Contrôle", characteristics: ctrlItems });
    }

    // Jets de sauvegarde (formule : mod + maîtrise + équip, max 16)
    const modSaveItems = [];
    const levelForSave = parseInt(creature.level, 10) || 1;
    const masteryBonus = Math.min(1 + Math.floor(levelForSave / 4), 6);
    const statToKey = [
        { stat: "vitality", key: "save_vitality", label: "Vit", defKey: "save_vitality_creature" },
        { stat: "sagesse", key: "save_wisdom", label: "Sag", defKey: "save_wisdom_creature" },
        { stat: "strong", key: "save_strength", label: "For", defKey: "save_strength_creature" },
        { stat: "intel", key: "save_intelligence", label: "Int", defKey: "save_intelligence_creature" },
        { stat: "chance", key: "save_chance", label: "Cha", defKey: "save_chance_creature" },
        { stat: "agi", key: "save_agility", label: "Agi", defKey: "save_agility_creature" },
    ];
    const modMaxForSave = Math.min(Math.floor(levelForSave / 2) + 1, 7);
    for (const { stat, key, label, defKey } of statToKey) {
        const statVal = parseInt(creature[stat], 10) || 10;
        const rawMod = Math.floor((statVal - 10) / 2);
        const mod = Math.max(Math.min(rawMod, modMaxForSave), -2);
        const bonus = parseInt(creature[`${key}_bonus`], 10) || 0;
        const mastery = parseInt(creature[`${key}_mastery`], 10) || 0;
        const saveTotal = mod + masteryBonus * mastery + bonus;
        const def = byDb[`${key}_bonus`] || byDb[defKey] || { key: defKey, name: `Sauv. ${label}`, short_name: label };
        modSaveItems.push({
            type: "formula",
            def: { ...def, key: def.key || defKey },
            value: `${saveTotal} (mod ${mod >= 0 ? "+" : ""}${mod}${mastery ? ` +${masteryBonus} maît.` : ""}${bonus ? ` +${bonus} équip.` : ""})`,
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    if (modSaveItems.length > 0) {
        groups.push({ title: "Sauvegardes", characteristics: modSaveItems });
    }

    return groups;
}
