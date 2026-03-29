/**
 * Résout une clé courte d’édition (strong, agi…) vers une `characteristic_key` spell en BDD.
 *
 * @param {string|null|undefined} shortKey
 * @param {'attack'|'save'} mode
 * @returns {string|null}
 */
export function resolveSpellCharacteristicKey(shortKey, mode) {
    if (shortKey == null || String(shortKey).trim() === '') {
        return null;
    }
    const k = String(shortKey).trim();

    const attackMap = {
        strong: 'strong_spell',
        intel: 'intel_spell',
        chance: 'chance_spell',
        agi: 'agi_spell',
        sagesse: 'sagesse_spell',
        vitality: 'vitality_spell',
    };

    const saveMap = {
        strong: 'save_strength_spell',
        intel: 'save_intelligence_spell',
        chance: 'save_chance_spell',
        agi: 'save_agility_spell',
        sagesse: 'save_wisdom_spell',
        vitality: 'save_vitality_spell',
    };

    const map = mode === 'save' ? saveMap : attackMap;
    return map[k] ?? null;
}
