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

    function convertedName(converted) {
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
        const maxDepth = 3;
        function walk(value, keyPrefix, depth) {
            if (value === null || typeof value !== "object") {
                out[keyPrefix] = value;
                return;
            }
            if (Array.isArray(value)) {
                out[keyPrefix] = `[${value.length} élément(s)]`;
                if (value.length > 0 && value[0] !== null && typeof value[0] === "object" && depth < maxDepth) {
                    walk(value[0], `${keyPrefix}.0`, depth + 1);
                }
                return;
            }
            if (depth >= maxDepth) {
                out[keyPrefix] = `{${Object.keys(value).length} clé(s)}`;
                return;
            }
            const keys = Object.keys(value);
            if (!keys.length) {
                out[keyPrefix] = "{}";
                return;
            }
            for (const k of keys) {
                walk(value[k], `${keyPrefix}.${k}`, depth + 1);
            }
        }
        for (const key of Object.keys(obj)) {
            const val = obj[key];
            const fullKey = prefix ? `${prefix}.${key}` : key;
            walk(val, fullKey, 0);
        }
        return out;
    }

    function findInFlat(flat, modelKey) {
        if (flat[modelKey] !== undefined) return flat[modelKey];
        const suffix = `.${modelKey}`;
        const found = Object.keys(flat).find((k) => k === modelKey || k.endsWith(suffix));
        if (found !== undefined) return flat[found];
        const langSuffixCandidates = [".fr", ".en", ".de", ".es", ".pt"];
        const langExact = Object.keys(flat).find((k) => langSuffixCandidates.some((s) => k === `${modelKey}${s}`));
        if (langExact !== undefined) return flat[langExact];
        const langBySuffix = Object.keys(flat).find((k) => langSuffixCandidates.some((s) => k.endsWith(`${suffix}${s}`)));
        if (langBySuffix !== undefined) return flat[langBySuffix];
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
            if (comparisonKeys.some((k) => key.startsWith(k + "."))) return true;
            if (comparisonKeys.some((k) => key.includes("." + k + "."))) return true;
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

    function summarizeSpellEffects(effects, maxEffects = 4, maxSubs = 4) {
        if (!Array.isArray(effects) || effects.length === 0) return "—";
        const chunks = [];
        for (const ef of effects.slice(0, maxEffects)) {
            if (!ef || typeof ef !== "object") continue;
            const degree = ef.degree != null ? `D${ef.degree}` : "D?";
            const subs = Array.isArray(ef.sub_effects) ? ef.sub_effects : [];
            const area = ef.area != null ? `zone=${ef.area}` : null;
            const targetType = ef.target_type != null ? `target=${ef.target_type}` : null;
            const subLabel = subs
                .slice(0, maxSubs)
                .map((s) => {
                    const slug = s?.sub_effect_slug ?? "?";
                    const order = s?.order != null ? `o${s.order}` : null;
                    const formula = s?.params?.value_formula ?? null;
                    const formulaCrit = s?.params?.value_formula_crit ?? null;
                    const converted = s?.params?.value_converted ?? null;
                    const characteristic = s?.params?.characteristic ?? null;
                    const duration = s?.params?.duration ?? null;
                    const diceFormula = s?.params?.dice_formula ?? null;
                    const critOnly = s?.crit_only === true ? "crit-only" : null;
                    const details = [
                        order,
                        formula != null ? String(formula) : null,
                        formulaCrit != null ? `crit:${formulaCrit}` : null,
                        converted != null ? `=>${converted}` : null,
                        diceFormula != null ? `dice:${diceFormula}` : null,
                        characteristic != null ? `(${characteristic})` : null,
                        duration != null ? `dur:${duration}` : null,
                        critOnly,
                    ].filter(Boolean).join(" ");
                    return details ? `${slug} ${details}` : String(slug);
                })
                .join(" ; ");
            const suffix = subs.length > maxSubs ? ` ; +${subs.length - maxSubs} autre(s)` : "";
            const effectMeta = [area, targetType].filter(Boolean).join(", ");
            const effectHead = effectMeta ? `${degree} [${effectMeta}]` : degree;
            chunks.push(`${effectHead}: ${subLabel || "—"}${suffix}`);
        }
        if (effects.length > maxEffects) {
            chunks.push(`+${effects.length - maxEffects} degré(s)`);
        }
        return chunks.join(" | ");
    }

    function summarizeRawSpellEffects(raw) {
        const levels = Array.isArray(raw?.levels) ? raw.levels : [];
        if (!levels.length) return "—";
        const chunks = [];
        for (const lvl of levels.slice(0, 4)) {
            if (!lvl || typeof lvl !== "object") continue;
            const grade = lvl.grade != null ? `D${lvl.grade}` : "D?";
            const effects = Array.isArray(lvl.effects) ? lvl.effects : [];
            const effLabel = effects
                .slice(0, 3)
                .map((e) => {
                    const effectId = e?.effectId ?? "?";
                    const diceNum = e?.diceNum ?? null;
                    const diceSide = e?.diceSide ?? null;
                    const formula = Number.isFinite(Number(diceNum)) && Number.isFinite(Number(diceSide)) && Number(diceNum) > 0
                        ? (Number(diceSide) > 0 ? `${diceNum}d${diceSide}` : String(diceNum))
                        : null;
                    return formula ? `#${effectId} ${formula}` : `#${effectId}`;
                })
                .join(" ; ");
            const suffix = effects.length > 3 ? ` ; +${effects.length - 3} autre(s)` : "";
            chunks.push(`${grade}: ${effLabel || "—"}${suffix}`);
        }
        if (levels.length > 4) {
            chunks.push(`+${levels.length - 4} degré(s)`);
        }
        return chunks.join(" | ");
    }

    function comparisonRows(item, dataEntry, entityTypeOverride) {
        const existing = existingRecord(item, dataEntry);
        const data = dataEntry ?? byId()[Number(item?.id)];
        const raw = data?.raw ?? item ?? {};
        const existingFlat = flattenForCompareShallow(existing ?? {});
        const convertedFlat = flattenForCompareShallow(data?.converted ?? {});
        const rawFlat = flattenRawForCompare(raw);
        const typeForFilter = entityTypeOverride ?? entityType();

        if (typeForFilter === "spell") {
            const convertedEffects = data?.converted?.spell_effects?.effects ?? null;
            if (Array.isArray(convertedEffects) && convertedEffects.length) {
                convertedFlat["spell_effects.summary"] = summarizeSpellEffects(convertedEffects);
            }
            const rawEffectsSummary = summarizeRawSpellEffects(raw);
            if (rawEffectsSummary !== "—") {
                rawFlat["spell_effects.summary"] = rawEffectsSummary;
            }
            const zone = raw?.spell_global?.area ?? raw?.levels?.[0]?.effects?.[0]?.zoneDescr ?? null;
            if (zone && typeof zone === "object") {
                if (zone.shape !== undefined) rawFlat["spell_global.area.shape"] = zone.shape;
                if (zone.param1 !== undefined) rawFlat["spell_global.area.param1"] = zone.param1;
                if (zone.param2 !== undefined) rawFlat["spell_global.area.param2"] = zone.param2;
            }
        }

        let modelKeys = Object.keys(existingFlat).length > 0 ? Object.keys(existingFlat) : Object.keys(convertedFlat);
        if (modelKeys.length === 0) modelKeys = Object.keys(rawFlat);
        // Inclure les clés du brut pour afficher les données DofusDB (ex. relations où seul le converti était affiché)
        modelKeys = [...new Set([...modelKeys, ...Object.keys(rawFlat)])];
        // Toujours filtrer par comparisonKeys quand la config en définit : n'afficher que les propriétés "intéressantes" (mapping)
        if (modelKeys.length > 0 && hasComparisonKeysConfig(typeForFilter)) {
            modelKeys = filterAllowedComparisonKeys(modelKeys, typeForFilter);
        }
        if (typeForFilter === "spell") {
            const forcedSpellKeys = [
                "spell_effects.summary",
                "spell_global.area.shape",
                "spell_global.area.param1",
                "spell_global.area.param2",
                "spell_global.description.fr",
            ];
            for (const forcedKey of forcedSpellKeys) {
                if (convertedFlat[forcedKey] !== undefined || rawFlat[forcedKey] !== undefined || existingFlat[forcedKey] !== undefined) {
                    if (!modelKeys.includes(forcedKey)) modelKeys.push(forcedKey);
                }
            }
        }
        // Item (resource/consumable/equipment) : une seule ligne par propriété. Le brut a level/price/name à la racine,
        // le converti a resources.* / consumables.* / items.*. On garde uniquement la clé préfixée pour éviter les doublons.
        const itemLikeEntities = new Set(["item", "resource", "consumable", "equipment"]);
        if (itemLikeEntities.has(typeForFilter)) {
            const prefixes = ["resources.", "consumables.", "items."];
            const prefixedKeys = new Set(modelKeys.filter((k) => prefixes.some((p) => k.startsWith(p))));
            modelKeys = modelKeys.filter((key) => {
                if (prefixedKeys.has(key)) return true;
                const short = key.includes(".") ? key.split(".").pop() : key;
                const hasPrefixed = prefixes.some((p) => prefixedKeys.has(p + short));
                return !hasPrefixed;
            });
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
            (c) => convertedName(c),
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
