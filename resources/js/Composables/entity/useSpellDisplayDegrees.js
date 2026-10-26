/**
 * Unifie `spell_degrees` (natif) et `effects_definitions` (legacy) pour les onglets de niveau.
 *
 * @example
 * const { degrees, hasDegrees } = useSpellDisplayDegrees(computed(() => spell));
 */
import { computed, unref } from "vue";

/**
 * Libellé d’onglet : « Niv {required_level} » (niveaux lus sur les degrés du sort).
 *
 * @param {object|null|undefined} deg
 * @param {number} index
 * @returns {string}
 */
export function spellDegreeTabLabel(deg, index = 0) {
    const lvl = Number(deg?.required_level);
    if (!Number.isNaN(lvl) && lvl > 0) {
        return `Niv ${lvl}`;
    }
    const pos = Number(deg?.position);
    if (!Number.isNaN(pos) && pos > 0) {
        return `Niv ${pos}`;
    }
    return `Niv ${index + 1}`;
}

/**
 * @param {object|null|undefined} spellDegrees - `{ degrees, default_degree_id }`
 * @param {Array|null|undefined} effectsDefinitions
 * @returns {Array<object>}
 */
export function buildSpellDisplayDegrees(spellDegrees, effectsDefinitions) {
    const native = Array.isArray(spellDegrees?.degrees) ? spellDegrees.degrees : [];
    if (native.length > 0) {
        // Toujours exposer les degrés natifs (onglets Utilisation) même sans lignes d’effets.
        return native.map((d) => ({
            id: d.id,
            required_level: d.required_level,
            position: d.position,
            area: d.area ?? d.properties?.area ?? null,
            properties: d.properties || d.resolved_properties || {},
            rows: Array.isArray(d.rows) ? d.rows : [],
        }));
    }

    const defs = Array.isArray(effectsDefinitions) ? effectsDefinitions : [];
    if (!defs.length) return [];
    const def = defs[0];
    return (def.degrees || []).map((deg) => ({
        id: deg.id,
        required_level: deg.required_creature_level,
        position: deg.degree,
        area: deg.area,
        properties: { area: deg.area },
        rows: Array.isArray(deg.rows) ? deg.rows : [],
    }));
}

/**
 * Payload `{ degrees, default_degree_id }` quelle que soit la forme (modèle ou objet brut).
 *
 * @param {object|null|undefined} spell
 * @returns {{ degrees: Array<object>, default_degree_id: number|null }}
 *
 * @example
 * readSpellDegreesPayload(spell).degrees.length
 */
export function readSpellDegreesPayload(spell) {
    const native = spell?.spellDegrees ?? spell?._data?.spell_degrees ?? spell?.spell_degrees ?? null;
    if (native && typeof native === "object" && Array.isArray(native.degrees)) {
        return native;
    }
    return { degrees: [], default_degree_id: null };
}

/**
 * Vrai si le sort porte déjà des degrés natifs ou des définitions legacy.
 *
 * @param {object|null|undefined} spell
 * @returns {boolean}
 *
 * @example
 * spellHasDegreePayload(spell) // false → hydrater via api.tables.spells
 */
export function spellHasDegreePayload(spell) {
    if (!spell) return false;
    if (readSpellDegreesPayload(spell).degrees.length > 0) return true;
    const defs = spell.effectsDefinitions ?? spell._data?.effects_definitions ?? spell.effects_definitions;
    return Array.isArray(defs) && defs.length > 0;
}

/**
 * @param {import('vue').Ref|import('vue').ComputedRef|object|null} spellSource
 */
export function useSpellDisplayDegrees(spellSource) {
    const degrees = computed(() => {
        const spell = unref(spellSource);
        if (!spell) return [];
        const effectsDefinitions =
            spell.effectsDefinitions ?? spell._data?.effects_definitions ?? spell.effects_definitions ?? [];
        return buildSpellDisplayDegrees(readSpellDegreesPayload(spell), effectsDefinitions);
    });

    const hasDegrees = computed(() => degrees.value.length > 0);

    return { degrees, hasDegrees };
}
