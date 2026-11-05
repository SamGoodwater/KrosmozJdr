/**
 * Sort vu au degré actif : clone le sort avec `_data` surchargé par les propriétés du degré.
 *
 * Les getters du modèle `Spell` lisent `_active_degree_properties` avant le degré par
 * défaut : sans cette clé, tous les onglets afficheraient les valeurs du premier degré.
 *
 * @param {import('@/Models/Entity/Spell').Spell|object|null} base - Sort affiché
 * @param {{ properties?: object, rows?: Array<object> }|null} degree - Degré actif (`useSpellDisplayDegrees`)
 * @returns {import('@/Models/Entity/Spell').Spell|object|null}
 *
 * @example
 * const usageEntity = computed(() => buildSpellActiveDegreeEntity(spell.value, activeDegree.value));
 * usageEntity.value.castPerTurn // valeur du degré actif
 */
export function buildSpellActiveDegreeEntity(base, degree) {
    const propsDeg = degree?.properties;
    if (!base || !propsDeg) {
        return base;
    }
    const merged = { ...(base._data || {}) };
    for (const [k, v] of Object.entries(propsDeg)) {
        if (v !== null && v !== undefined) {
            merged[k] = v;
        }
    }
    merged._active_degree_properties = propsDeg;
    merged._active_degree_rows = degree?.rows ?? [];
    const Ctor = base.constructor;
    if (typeof Ctor === "function" && Ctor !== Object) {
        try {
            return new Ctor(merged);
        } catch {
            /* repli objet plat */
        }
    }
    return { ...base, _data: merged, ...merged };
}
