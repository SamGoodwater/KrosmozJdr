import { getByDbColumnMap, getByComputedKeyMap } from "@/Composables/store/useCharacteristicsStore";
import {
    getDisplayValue,
    isPoCac,
    PO_CAC_ICON,
} from "@/Composables/entity/useCharacteristicDisplay";
import {
    CREATURE_CHARACTERISTIC_GROUPS,
    CREATURE_CHARACTERISTIC_SUMMARY_KEYS,
} from "@/Utils/Entity/creatureCharacteristicGroups.manifest";

/**
 * Construit les groupes de caractéristiques pour CharacteristicsCard à partir d'une créature.
 *
 * @param {Object|null} creature - Données de la créature (life, pa, pm, strong, etc.)
 * @param {Object} [options]
 * @param {'summary'|'full'} [options.mode='full'] - summary = 5 stats clés plates ; full = tous les groupes
 * @param {Object} [options.byDbColumn]
 * @param {Object} [options.byComputedKey]
 * @param {Object|null} [options.runtime] - resolved-stats : si présent, privilégie les valeurs mods/saves runtime
 * @returns {Array<{ title: string, characteristics: Array }>}
 */
export function buildCreatureCharacteristicGroups(creature, options = {}) {
    if (!creature || typeof creature !== "object") {
        return [];
    }

    const mode = options.mode === "summary" ? "summary" : "full";
    const byDb =
        options?.byDbColumn && typeof options.byDbColumn === "object"
            ? options.byDbColumn
            : getByDbColumnMap("creature");
    const byComp =
        options?.byComputedKey && typeof options.byComputedKey === "object"
            ? options.byComputedKey
            : getByComputedKeyMap("creature");
    const runtime = options.runtime && typeof options.runtime === "object" ? options.runtime : null;

    const getDef = (dbColumn) => byDb[dbColumn] || { key: dbColumn, name: dbColumn, short_name: dbColumn };
    const getCompDef = (key) => byComp[key] || { key, name: key, short_name: key };

    const makeFormula = (dbColumn) => {
        const def = getDef(dbColumn);
        const value = creature[dbColumn];
        if (value === null || value === undefined || value === "") return null;
        const displayValue = getDisplayValue(dbColumn, value, def);
        const poCac = dbColumn === "po" && isPoCac(value);
        const resolvedDef = poCac
            ? { ...def, key: def.key || dbColumn, icon: PO_CAC_ICON, helper: "Corps à corps" }
            : { ...def, key: def.key || dbColumn };
        return {
            type: "formula",
            def: resolvedDef,
            value: poCac ? "" : displayValue,
            formulaResolved: "",
            formulaRaw: "",
        };
    };

    const addFormulas = (dbColumns) => dbColumns.map(makeFormula).filter(Boolean);

    if (mode === "summary") {
        const items = addFormulas([...CREATURE_CHARACTERISTIC_SUMMARY_KEYS]);
        return items.length > 0 ? [{ title: "", characteristics: items }] : [];
    }

    const groups = [];

    for (const groupDef of CREATURE_CHARACTERISTIC_GROUPS) {
        let items = [];
        if (groupDef.kind === "db") {
            items = addFormulas([...(groupDef.dbColumns || [])]);
        } else if (groupDef.kind === "modifiers") {
            items = buildModifierItems(creature, getCompDef, runtime);
        } else if (groupDef.kind === "resistances") {
            items = buildResistanceItems(creature, getDef);
        } else if (groupDef.kind === "damages") {
            items = buildDamageItems(creature, makeFormula);
        } else if (groupDef.kind === "saves") {
            items = buildSaveItems(creature, byDb, runtime);
        }
        if (items.length > 0) {
            groups.push({ title: groupDef.title, characteristics: items });
        }
    }

    return groups;
}

/**
 * @param {Object|null} runtime
 * @param {string} key
 * @returns {string|number|null}
 */
function runtimeDisplayValue(runtime, key) {
    if (!runtime) return null;
    const levels = runtime.levels;
    if (Array.isArray(levels) && levels.length > 0) {
        const row = levels[0]?.characteristics?.[key];
        if (row && row.total != null && row.total !== "") return row.total;
        if (row && row.value != null && row.value !== "") return row.value;
    }
    const computed = runtime.computed;
    if (computed && typeof computed === "object") {
        const row = computed[key];
        if (row && row.total != null && row.total !== "") return row.total;
        if (row && row.value != null && row.value !== "") return row.value;
        if (typeof row === "number" || typeof row === "string") return row;
    }
    return null;
}

function buildModifierItems(creature, getCompDef, runtime) {
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
        const def = getCompDef(key);
        const fromRuntime = runtimeDisplayValue(runtime, key);
        let display;
        if (fromRuntime != null) {
            const n = Number(fromRuntime);
            display = Number.isFinite(n) && n >= 0 ? `+${n}` : String(fromRuntime);
        } else {
            const statVal = parseInt(creature[stat], 10) || 10;
            const rawMod = Math.floor((statVal - 10) / 2);
            const mod = Math.max(Math.min(rawMod, modMax), -2);
            display = mod >= 0 ? `+${mod}` : String(mod);
        }
        modifierItems.push({
            type: "formula",
            def: { ...def, key: def.key || key },
            value: display,
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    return modifierItems;
}

function buildResistanceItems(creature, getDef) {
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
    for (const percentDb of ["res_sagesse", "res_vitalite"]) {
        const percent = creature[percentDb];
        const hasPercent = percent !== null && percent !== undefined && String(percent) !== "";
        if (!hasPercent) continue;
        const def = getDef(percentDb);
        resItems.push({
            type: "formula",
            def: { ...def, key: def.key || percentDb },
            value: `${percent}%`,
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    return resItems;
}

function buildDamageItems(creature, makeFormula) {
    const els = ["neutre", "terre", "feu", "air", "eau"];
    const dmgItems = [];
    const touchItem = makeFormula("touch");
    if (touchItem) dmgItems.push(touchItem);
    for (const el of els) {
        const item = makeFormula(`do_fixe_${el}`);
        if (item) dmgItems.push(item);
    }
    for (const db of ["do_sagesse", "do_vitalite"]) {
        const item = makeFormula(db);
        if (item) dmgItems.push(item);
    }
    return dmgItems;
}

function buildSaveItems(creature, byDb, runtime) {
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
        const def = byDb[`${key}_bonus`] || byDb[defKey] || { key: defKey, name: `Sauv. ${label}`, short_name: label };
        const fromRuntime = runtimeDisplayValue(runtime, defKey) ?? runtimeDisplayValue(runtime, key);
        let display;
        if (fromRuntime != null) {
            display = String(fromRuntime);
        } else {
            const statVal = parseInt(creature[stat], 10) || 10;
            const rawMod = Math.floor((statVal - 10) / 2);
            const mod = Math.max(Math.min(rawMod, modMaxForSave), -2);
            const bonus = parseInt(creature[`${key}_bonus`], 10) || 0;
            const mastery = parseInt(creature[`${key}_mastery`], 10) || 0;
            const saveTotal = mod + masteryBonus * mastery + bonus;
            display = `${saveTotal} (mod ${mod >= 0 ? "+" : ""}${mod}${mastery ? ` +${masteryBonus} maît.` : ""}${bonus ? ` +${bonus} équip.` : ""})`;
        }
        modSaveItems.push({
            type: "formula",
            def: { ...def, key: def.key || defKey },
            value: display,
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    return modSaveItems;
}
