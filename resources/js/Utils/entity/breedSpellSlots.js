/**
 * Regroupe les sorts d'une classe par emplacement (pivot breed_spell).
 *
 * @param {object} breed - Données Breed (spells, spell_slots, _data)
 * @returns {Array<{ character_level: number, slot_index: number, spells: object[] }>}
 */
export function buildSpellSlotGroups(breed) {
    const direct = breed?.spell_slots ?? breed?._data?.spell_slots;
    if (Array.isArray(direct) && direct.length > 0) {
        return direct.map((g) => ({
            ...g,
            spells: Array.isArray(g.spells) ? [...g.spells] : [],
        }));
    }
    const raw = breed?.spells ?? breed?._data?.spells;
    const spells = Array.isArray(raw) ? raw : [];
    if (!spells.length) return [];

    const map = new Map();
    for (const s of spells) {
        const p = s.pivot || {};
        const level = Number(p.character_level ?? 1);
        const slot = Number(p.slot_index ?? 1);
        const key = `${level}|${slot}`;
        if (!map.has(key)) {
            map.set(key, { character_level: level, slot_index: slot, spells: [] });
        }
        map.get(key).spells.push(s);
    }
    const out = Array.from(map.values()).sort((a, b) =>
        a.character_level !== b.character_level
            ? a.character_level - b.character_level
            : a.slot_index - b.slot_index
    );
    for (const g of out) {
        g.spells.sort((a, b) => {
            const oa = Number(a.pivot?.choice_order ?? 0) || 0;
            const ob = Number(b.pivot?.choice_order ?? 0) || 0;
            if (oa !== ob) return oa - ob;
            return String(a.name || "").localeCompare(String(b.name || ""));
        });
    }
    return out;
}

/**
 * Grille d’emplacements « officielle » : 3 au niveau 1, puis 1 par niveau impair ≥ 3.
 *
 * @param {number} maxOddLevel - Dernier niveau impair (ex. 21 pour jusqu’au niv. 21)
 * @returns {Array<{ character_level: number, slot_index: number, label: string }>}
 */
export function getStandardBreedSlotDefinitions(maxOddLevel = 21) {
    const slots = [];
    for (let slot_index = 1; slot_index <= 3; slot_index++) {
        slots.push({
            character_level: 1,
            slot_index,
            label: `Niveau 1 · Choix ${slot_index}`,
        });
    }
    for (let L = 3; L <= maxOddLevel; L += 2) {
        slots.push({
            character_level: L,
            slot_index: 1,
            label: `Niveau ${L} · Sort de classe`,
        });
    }
    return slots;
}

/**
 * @param {number} characterLevel
 * @param {number} slotIndex
 * @param {ReturnType<typeof getStandardBreedSlotDefinitions>} standardSlots
 */
export function isStandardSlot(characterLevel, slotIndex, standardSlots) {
    return standardSlots.some(
        (s) => s.character_level === characterLevel && s.slot_index === slotIndex
    );
}
