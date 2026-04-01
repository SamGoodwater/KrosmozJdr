/**
 * useCharacteristicViewModel — Vue unifiée pour afficher une caractéristique / propriété.
 *
 * @description
 * Assemble définition BDD (via useCharacteristicsStore + resolveDef), métadonnées
 * descriptor/table (via useEntityPropertyDisplay) et optionnellement un payload runtime
 * (ex. GET /entities/creatures/{id}/resolved-stats → computed[key]).
 *
 * @see docs/30-UI/PROPERTY_DISPLAY_SYSTEM.md
 */

import { computed, toValue } from "vue";
import { useEntityPropertyDisplay } from "@/Composables/entity/useEntityPropertyDisplay";
import { resolveDef } from "@/Composables/entity/useCharacteristicDisplay";

/**
 * Groupes de caractéristiques à interroger selon le type d'entité (aligné resolveDef).
 *
 * @param {string} entityType
 * @returns {string[]}
 */
export function getSourceGroupsForEntityType(entityType) {
    const t = String(entityType || "").toLowerCase();
    const map = {
        monster: ["creature"],
        creature: ["creature"],
        class: ["creature"],
        npc: ["creature"],
        spell: ["spell", "capability"],
        capability: ["spell", "capability"],
        item: ["item", "resource"],
        resource: ["resource", "item"],
        consumable: ["consumable", "resource"],
        panoply: ["panoply", "item"],
    };
    return map[t] ?? ["creature", "item", "resource", "spell", "capability"];
}

/**
 * Fusionne une entrée runtime `computed` (backend) si présente pour la clé donnée.
 *
 * @param {Record<string, object>|undefined|null} computedMap
 * @param {string} key
 * @returns {object|null}
 */
function pickRuntimeComputed(computedMap, key) {
    if (!computedMap || typeof computedMap !== "object" || !key) {
        return null;
    }
    return computedMap[key] ?? null;
}

/**
 * @param {object} options
 * @param {import('vue').Ref<object>|function|object} options
 * @returns {{
 *   viewModel: import('vue').ComputedRef<object>,
 *   property: import('vue').ComputedRef<object>,
 *   value: import('vue').ComputedRef,
 *   displayValue: import('vue').ComputedRef<string>,
 *   unit: import('vue').ComputedRef<string>,
 *   characteristic: import('vue').ComputedRef<object|null>,
 * }}
 */
export function useCharacteristicViewModel(options) {
    const ep = useEntityPropertyDisplay(options);

    const viewModel = computed(() => {
        const o = toValue(options);
        const fieldKey = String(o?.fieldKey || "");
        const entityType = String(o?.entityType || "");
        const sourceGroups = getSourceGroupsForEntityType(entityType);
        const val = ep.value.value;
        const def = resolveDef(fieldKey, val, { sourceGroups }) || {};
        const prop = ep.property.value || {};
        const char = ep.characteristic.value || {};

        const key = def.key || fieldKey;
        const runtimeRoot = o?.runtime && typeof o.runtime === "object" ? o.runtime : {};
        const computedMap = runtimeRoot.computed;
        const rc =
            pickRuntimeComputed(computedMap, key) ||
            pickRuntimeComputed(computedMap, fieldKey);

        const formulaBdd = char.formula ?? def.formula ?? "";
        const formulaDisplay = char.formula_display ?? def.formula_display ?? "";

        const substituted = rc?.substituted != null ? String(rc.substituted) : "";
        const placeholders = Array.isArray(rc?.placeholders) ? rc.placeholders : [];

        const name = def.name ?? prop.label ?? key;
        const shortName = def.short_name ?? prop.shortLabel ?? name;

        const levelTable =
            Array.isArray(o?.levelTable) && o.levelTable.length > 0
                ? o.levelTable
                : Array.isArray(ep.levelTable.value)
                  ? ep.levelTable.value
                  : [];

        return {
            key,
            name,
            shortName,
            icon: def._resolvedIcon ?? def.icon ?? prop.icon ?? "",
            color: def._resolvedColor ?? def.color ?? prop.color ?? "",
            helper: def.helper ?? "",
            descriptions: def.descriptions ?? "",
            unit: ep.unit.value || def.unit || "",
            displayValue: ep.displayValue.value,
            rawValue: val,
            formulaBdd: String(formulaBdd || ""),
            formulaDisplay: String(formulaDisplay || ""),
            /** Formule / substitution issue des métadonnées entité (useEntityPropertyDisplay) */
            formulaMetaResolved: String(ep.formulaResolved.value || ""),
            formulaMetaRaw: String(ep.formulaRaw.value || ""),
            /** Runtime backend (ex. resolved-stats) */
            runtimeFormula: rc?.formula != null ? String(rc.formula) : "",
            substituted,
            placeholders,
            levelTable,
            tooltipLine: prop.tooltip ?? def.helper ?? "",
        };
    });

    return {
        ...ep,
        viewModel,
    };
}

/**
 * Enrichit un view model (ex. item formula) avec une entrée `computed` du payload resolved-stats.
 *
 * @param {object} vm
 * @param {object|null|undefined} rc
 * @returns {object}
 */
export function mergeRuntimeIntoViewModel(vm, rc) {
    if (!vm || typeof vm !== "object") {
        return vm;
    }
    if (!rc || typeof rc !== "object") {
        return vm;
    }
    const next = { ...vm };
    if (rc.formula != null) {
        next.runtimeFormula = String(rc.formula);
    }
    if (rc.substituted != null) {
        next.substituted = String(rc.substituted);
    }
    if (Array.isArray(rc.placeholders) && rc.placeholders.length > 0) {
        next.placeholders = rc.placeholders;
    }
    if (rc.formula_display != null && String(rc.formula_display).trim() !== "") {
        next.formulaDisplay = String(rc.formula_display);
    }
    return next;
}

/**
 * Construit un view model à partir d'un item `type: 'formula'` (CharacteristicsCard / groupes créature).
 *
 * @param {object} item
 * @returns {object}
 */
export function viewModelFromFormulaGroupItem(item) {
    const def = item?.def && typeof item.def === "object" ? item.def : {};
    const unit = item?.unit || def.unit || "";
    const v = item?.value;
    const valStr = v === null || v === undefined || v === "" ? "—" : String(v);
    const displayValue = unit ? `${valStr} ${unit}`.trim() : valStr;
    return {
        key: def.key || "unknown",
        name: def.name || def.key || "—",
        shortName: def.short_name || def.name || def.key || "—",
        icon: def.icon || "",
        color: def.color || "",
        helper: def.helper || "",
        descriptions: def.descriptions || "",
        unit,
        displayValue,
        rawValue: v,
        formulaBdd: String(def.formula || ""),
        formulaDisplay: String(def.formula_display || ""),
        formulaMetaResolved: String(item?.formulaResolved || ""),
        formulaMetaRaw: String(item?.formulaRaw || ""),
        runtimeFormula: "",
        substituted: "",
        placeholders: [],
        levelTable: Array.isArray(item?.levelTable) ? item.levelTable : [],
        tooltipLine: def.helper || def.descriptions || "",
    };
}

/**
 * View model pour les chips inline (CharacteristicChip / tableaux).
 *
 * @param {object} item
 * @returns {object}
 */
export function viewModelFromChipItem(item) {
    const i = item && typeof item === "object" ? item : {};
    const unit = i.unit || "";
    const raw = i.value;
    const v =
        raw === null || raw === undefined || raw === ""
            ? "—"
            : typeof raw === "boolean"
              ? raw
                  ? "Oui"
                  : "Non"
              : String(raw);
    const displayValue = unit && v !== "—" ? `${v} ${unit}`.trim() : v;
    return {
        key: i.key || "chip",
        name: i.name || i.label || "",
        shortName: i.shortLabel || i.short_name || i.name || i.label || "",
        icon: i.icon || "",
        color: i.color || "",
        helper: i.tooltip || "",
        descriptions: "",
        unit,
        displayValue,
        rawValue: raw,
        formulaBdd: "",
        formulaDisplay: "",
        formulaMetaResolved: "",
        formulaMetaRaw: "",
        runtimeFormula: "",
        substituted: "",
        placeholders: [],
        levelTable: [],
        tooltipLine: i.tooltip || "",
    };
}

export function viewModelFromLegacyProperty(property, value) {
    const p = property && typeof property === "object" ? property : {};
    let display = "—";
    if (value !== null && value !== undefined && value !== "") {
        if (typeof value === "boolean") {
            display = value ? "Oui" : "Non";
        } else {
            display = String(value);
        }
    }
    return {
        key: "legacy",
        name: p.label || "",
        shortName: p.shortLabel || p.label || "",
        icon: p.icon || "",
        color: p.color || "",
        helper: p.tooltip || "",
        descriptions: "",
        unit: "",
        displayValue: display,
        rawValue: value,
        formulaBdd: "",
        formulaDisplay: "",
        formulaMetaResolved: "",
        formulaMetaRaw: "",
        runtimeFormula: "",
        substituted: "",
        placeholders: [],
        levelTable: [],
        tooltipLine: p.tooltip || p.label || "",
    };
}
