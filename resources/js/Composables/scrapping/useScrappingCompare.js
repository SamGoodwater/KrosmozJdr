/**
 * Composable : helpers comparaison (existant / converti / brut), triples et comparisonRows.
 * Dérive les valeurs d'affichage pour une ligne ; fonctions pures à partir des refs.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

/**
 * @param {{
 *   convertedByItemIdRef: import('vue').Ref<Record<number, { raw?: any, converted?: any, existing?: { record?: any } }>>,
 *   configRef: import('vue').Ref<Record<string, { comparisonKeys?: string[] }>>,
 *   entityTypeRef: import('vue').Ref<string>
 * }} options
 */
export function useScrappingCompare(options) {
    const { convertedByItemIdRef, configRef, entityTypeRef } = options;
    const byId = () => convertedByItemIdRef.value;
    const entityType = () => entityTypeRef.value ?? "";
    const config = () => configRef.value ?? {};

    function existingRecord(item, dataEntry) {
        if (dataEntry?.existing?.record != null) return dataEntry.existing.record;
        const id = Number(item?.id);
        if (!Number.isFinite(id)) return null;
        return byId()[id]?.existing?.record ?? null;
    }

    function cellTriple(item, getExisting, getConverted, getRaw, dataEntry) {
        const existing = existingRecord(item, dataEntry);
        const data = dataEntry ?? byId()[Number(item?.id)];
        const rawSource = data?.raw ?? item;
        return {
            existant: getExisting(existing),
            converti: getConverted(data?.converted),
            brut: getRaw(rawSource),
        };
    }

    /** Prend le premier élément d'un tableau ou l'objet lui-même (API renvoie parfois un objet unique par bloc). */
    function firstOrBlock(val) {
        if (Array.isArray(val) && val.length) return val[0];
        return val && typeof val === "object" ? val : null;
    }

    function convertedName(converted, entityTypeStr) {
        if (!converted || typeof converted !== "object") return null;
        const from = firstOrBlock(converted.creatures) ?? firstOrBlock(converted.monsters)
            ?? firstOrBlock(converted.spells) ?? firstOrBlock(converted.breeds) ?? firstOrBlock(converted.classes)
            ?? firstOrBlock(converted.resources) ?? firstOrBlock(converted.consumables) ?? firstOrBlock(converted.items)
            ?? firstOrBlock(converted.panoplies);
        if (from && typeof from.name !== "undefined") return from.name;
        if (from && typeof from === "object" && from.name) return from.name;
        return null;
    }

    function convertedLevel(converted) {
        if (!converted || typeof converted !== "object") return null;
        const from = firstOrBlock(converted.creatures) ?? firstOrBlock(converted.monsters) ?? firstOrBlock(converted.spells)
            ?? firstOrBlock(converted.breeds) ?? firstOrBlock(converted.classes);
        if (from && typeof from.level !== "undefined") return from.level;
        return null;
    }

    function extractFirstBlock(converted) {
        if (!converted || typeof converted !== "object") return null;
        return firstOrBlock(converted.creatures) ?? firstOrBlock(converted.monsters) ?? firstOrBlock(converted.spells)
            ?? firstOrBlock(converted.breeds) ?? firstOrBlock(converted.classes)
            ?? firstOrBlock(converted.resources) ?? firstOrBlock(converted.consumables) ?? firstOrBlock(converted.items)
            ?? firstOrBlock(converted.panoplies) ?? null;
    }

    function flattenForCompareShallow(obj, prefix = "") {
        if (!obj || typeof obj !== "object") return {};
        const out = {};
        for (const key of Object.keys(obj)) {
            const val = obj[key];
            const fullKey = prefix ? `${prefix}.${key}` : key;
            if (val !== null && typeof val === "object" && !Array.isArray(val)) {
                for (const k2 of Object.keys(val)) {
                    const v2 = val[k2];
                    const key2 = `${fullKey}.${k2}`;
                    if (v2 !== null && typeof v2 === "object" && (Array.isArray(v2) || typeof v2 === "object")) {
                        out[key2] = Array.isArray(v2) ? `[${v2.length} élément(s)]` : `{${Object.keys(v2).length} clé(s)}`;
                    } else {
                        out[key2] = v2;
                    }
                }
            } else if (Array.isArray(val)) {
                out[fullKey] = `[${val.length} élément(s)]`;
            } else {
                out[fullKey] = val;
            }
        }
        return out;
    }

    function flattenRawForCompare(obj, prefix = "") {
        if (!obj || typeof obj !== "object") return {};
        const out = {};
        for (const key of Object.keys(obj)) {
            const val = obj[key];
            const fullKey = prefix ? `${prefix}.${key}` : key;
            if (Array.isArray(val) && val.length > 0 && val[0] !== null && typeof val[0] === "object") {
                for (const k2 of Object.keys(val[0])) {
                    const v2 = val[0][k2];
                    const key2 = `${fullKey}.0.${k2}`;
                    if (v2 !== null && typeof v2 === "object" && (Array.isArray(v2) || (typeof v2 === "object" && Object.keys(v2).length > 0))) {
                        out[key2] = Array.isArray(v2) ? `[${v2.length} élément(s)]` : `{${Object.keys(v2).length} clé(s)}`;
                    } else {
                        out[key2] = v2;
                    }
                }
            } else if (val !== null && typeof val === "object" && !Array.isArray(val)) {
                for (const k2 of Object.keys(val)) {
                    const v2 = val[k2];
                    const key2 = `${fullKey}.${k2}`;
                    if (v2 !== null && typeof v2 === "object" && (Array.isArray(v2) || typeof v2 === "object")) {
                        out[key2] = Array.isArray(v2) ? `[${v2.length} élément(s)]` : `{${Object.keys(v2).length} clé(s)}`;
                    } else {
                        out[key2] = v2;
                    }
                }
            } else if (Array.isArray(val)) {
                out[fullKey] = `[${val.length} élément(s)]`;
            } else {
                out[fullKey] = val;
            }
        }
        return out;
    }

    function findInFlat(flat, modelKey) {
        if (flat[modelKey] !== undefined) return flat[modelKey];
        const suffix = `.${modelKey}`;
        const found = Object.keys(flat).find((k) => k === modelKey || k.endsWith(suffix));
        if (found !== undefined) return flat[found];
        // Brut DofusDB : clés imbriquées (ex. "name.fr") alors que le converti a "name"
        const prefix = `${modelKey}.`;
        const prefixKey = Object.keys(flat).find((k) => k.startsWith(prefix));
        return prefixKey !== undefined ? flat[prefixKey] : undefined;
    }

    function isAllowedComparisonKey(key, entityTypeStr) {
        if (!key || typeof key !== "string") return false;
        const comparisonKeys = config()[String(entityTypeStr || "")]?.comparisonKeys;
        if (Array.isArray(comparisonKeys) && comparisonKeys.length > 0) {
            if (comparisonKeys.includes(key)) return true;
            if (comparisonKeys.some((k) => key === k || key.endsWith("." + k))) return true;
            return false;
        }
        return true;
    }

    function filterAllowedComparisonKeys(keys, entityTypeStr) {
        return keys.filter((k) => isAllowedComparisonKey(k, entityTypeStr));
    }

    function hasComparisonKeysConfig(entityTypeStr) {
        const comparisonKeys = config()[String(entityTypeStr || "")]?.comparisonKeys;
        return Array.isArray(comparisonKeys) && comparisonKeys.length > 0;
    }

    function comparisonRows(item, dataEntry, entityTypeOverride) {
        const existing = existingRecord(item, dataEntry);
        const data = dataEntry ?? byId()[Number(item?.id)];
        const raw = data?.raw ?? item ?? {};
        const existingFlat = flattenForCompareShallow(existing ?? {});
        const convertedFlat = flattenForCompareShallow(data?.converted ?? {});
        const rawFlat = flattenRawForCompare(raw);
        let modelKeys = Object.keys(existingFlat).length > 0 ? Object.keys(existingFlat) : Object.keys(convertedFlat);
        if (modelKeys.length === 0) modelKeys = Object.keys(rawFlat);
        // Inclure les clés du brut pour afficher les données DofusDB (ex. relations où seul le converti était affiché)
        modelKeys = [...new Set([...modelKeys, ...Object.keys(rawFlat)])];
        const typeForFilter = entityTypeOverride ?? entityType();
        // Toujours filtrer par comparisonKeys quand la config en définit : n'afficher que les propriétés "intéressantes" (mapping)
        if (modelKeys.length > 0 && hasComparisonKeysConfig(typeForFilter)) {
            modelKeys = filterAllowedComparisonKeys(modelKeys, typeForFilter);
        }
        return modelKeys.sort().map((key) => {
            const brut = findInFlat(rawFlat, key) ?? findInFlat(rawFlat, key.split(".").pop());
            const converti = findInFlat(convertedFlat, key) ?? findInFlat(convertedFlat, key.split(".").pop());
            const existant = existingFlat[key];
            const differs = converti !== existant;
            return { key, brut, converti, existant, differs };
        });
    }

    function formatName(name) {
        if (!name) return "—";
        if (typeof name === "string") return name;
        if (typeof name === "object") return name.fr || name.en || name.de || name.es || name.pt || "—";
        return "—";
    }

    function tripleName(item, dataEntry) {
        return cellTriple(
            item,
            (r) => (r?.name != null ? String(r.name) : null),
            (c) => convertedName(c, entityType()),
            (r) => formatName(r?.name),
            dataEntry
        );
    }

    function tripleLevel(item, dataEntry) {
        const rawLevel = (r) => {
            if (r?.level != null) return String(r.level);
            const g0 = r?.grades?.[0];
            if (g0?.level != null) return String(g0.level);
            return null;
        };
        return cellTriple(
            item,
            (r) => (r?.level != null ? String(r.level) : null),
            (c) => (convertedLevel(c) != null ? String(convertedLevel(c)) : null),
            rawLevel,
            dataEntry
        );
    }

    function tripleType(item, dataEntry) {
        const rawType = (r) => {
            const name = r?.typeName ?? r?.type?.name;
            if (name != null) return String(name);
            const id = r?.typeId ?? r?.type?.id ?? r?.item_type_id ?? r?.resource_type_id ?? r?.consumable_type_id ?? r?.type_id;
            if (id != null) return `#${id}`;
            return null;
        };
        return cellTriple(
            item,
            (r) => (r?.item_type_id ?? r?.resource_type_id ?? r?.consumable_type_id ?? r?.type_id != null ? String(r.item_type_id ?? r.resource_type_id ?? r.consumable_type_id ?? r.type_id) : null),
            (c) => {
                const block = extractFirstBlock(c);
                return block?.type_id != null ? String(block.type_id) : null;
            },
            rawType,
            dataEntry
        );
    }

    function formatCompareVal(val) {
        if (val == null || val === "") return "—";
        if (typeof val === "object") return JSON.stringify(val);
        return String(val);
    }

    return {
        existingRecord,
        cellTriple,
        tripleName,
        tripleLevel,
        tripleType,
        comparisonRows,
        flattenForCompareShallow,
        flattenRawForCompare,
        findInFlat,
        convertedName,
        convertedLevel,
        extractFirstBlock,
        formatCompareVal,
    };
}
