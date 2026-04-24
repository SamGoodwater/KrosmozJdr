import { getRawData } from "@/Composables/store/useCharacteristicsStore";

const RESERVED_KEYS = [
    { id: "d", name: "Valeur Dofus (conversion)" },
    { id: "level", name: "Niveau JDR" },
];

/**
 * Agrège les clés caractéristiques exposées au frontend (Inertia share) pour autocomplétion / mentions.
 *
 * @returns {Array<{ id: string, name: string, short_name?: string|null }>}
 */
export function buildCharacteristicKeySuggestionsFromStore() {
    const raw = getRawData() || {};
    const seen = new Set();
    const out = [];

    for (const r of RESERVED_KEYS) {
        if (r.id && !seen.has(r.id)) {
            seen.add(r.id);
            out.push({ ...r });
        }
    }

    const addDef = (def) => {
        if (!def || typeof def !== "object") return;
        const id = def.key ?? def.db_column;
        if (!id || seen.has(id)) return;
        seen.add(id);
        out.push({
            id: String(id),
            name: def.name != null ? String(def.name) : String(id),
            short_name: def.short_name ?? null,
        });
    };

    const walkMap = (obj) => {
        if (!obj || typeof obj !== "object") return;
        for (const def of Object.values(obj)) {
            addDef(def);
        }
    };

    walkMap(raw.creature?.byDbColumn);
    walkMap(raw.creature?.byComputedKey);
    walkMap(raw.spell?.byDbColumn);
    walkMap(raw.capability?.byDbColumn);
    walkMap(raw.item?.byDbColumn);
    walkMap(raw.item?.byCharacteristicKey);
    walkMap(raw.consumable?.byDbColumn);
    walkMap(raw.consumable?.byCharacteristicKey);
    walkMap(raw.resource?.byDbColumn);
    walkMap(raw.resource?.byCharacteristicKey);
    walkMap(raw.panoply?.byDbColumn);
    walkMap(raw.panoply?.byCharacteristicKey);

    return out;
}
