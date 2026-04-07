/**
 * useEntityPropertyDisplay — Composable pour l'affichage des propriétés d'entité
 *
 * @description
 * Centralise la résolution des métadonnées (icône, label, unité, couleur) et de la valeur
 * pour EntityPropertyDisplay. Utilise resolveEntityFieldUi et le store caractéristiques.
 *
 * @param {Object} options
 * @param {string} options.fieldKey - Clé du champ
 * @param {Object} options.entity - Entité (modèle ou objet brut)
 * @param {string} options.entityType - Type d'entité (resource, spell, monster, etc.)
 * @param {string} options.displayMode - minimal | compact | extended | detailed
 * @param {Object} [options.descriptors] - Descriptors du champ (getXFieldDescriptors())
 * @param {Object} [options.tableMeta] - Meta du tableau (caractéristiques)
 * @param {Object} [options.cellOptions] - Options pour entity.toCell (size, context)
 *
 * @returns {{ property, value, displayValue, unit, hasFormula, formulaResolved, formulaRaw, levelTable }}
 *
 * @example
 * const { property, value, displayValue } = useEntityPropertyDisplay({
 *   fieldKey: 'level',
 *   entity: spell,
 *   entityType: 'spell',
 *   displayMode: 'extended',
 *   descriptors,
 *   tableMeta,
 * });
 */
import { computed, toValue } from "vue";
import {
    resolveEntityFieldUi,
    getEntityCharacteristicsByDbColumn,
} from "@/Utils/Entity/entity-view-ui";

export function useEntityPropertyDisplay(options = {}) {
    const opts = computed(() => toValue(options));
    const fieldKey = computed(() => String(opts.value?.fieldKey || ""));
    const entity = computed(() => opts.value?.entity);
    const entityType = computed(() => String(opts.value?.entityType || ""));
    const descriptors = computed(() => opts.value?.descriptors || {});
    const tableMeta = computed(() => opts.value?.tableMeta || {});
    const cellOptions = computed(() => {
        const raw = opts.value?.cellOptions;
        if (raw && typeof raw === "object") {
            return {
                size: "md",
                context: "extended",
                ...raw,
            };
        }
        return { size: "md", context: "extended" };
    });

    const property = computed(() =>
        resolveEntityFieldUi({
            fieldKey: fieldKey.value,
            descriptors: descriptors.value,
            tableMeta: tableMeta.value,
            entityType: entityType.value,
        })
    );

    const rawValue = computed(() => {
        const ent = entity.value;
        if (!ent) return null;
        const data = ent._data ?? ent;
        return data[fieldKey.value];
    });

    const cell = computed(() => {
        const ent = entity.value;
        if (!ent || typeof ent.toCell !== "function") return null;
        try {
            return ent.toCell(fieldKey.value, cellOptions.value);
        } catch {
            return null;
        }
    });

    const value = computed(() => {
        const c = cell.value;
        if (c && c.value != null && c.value !== "") {
            return c.value;
        }
        if (c?.value === 0 || c?.value === false) return c.value;
        return rawValue.value;
    });

    const byDbColumn = computed(() =>
        getEntityCharacteristicsByDbColumn(tableMeta.value, entityType.value)
    );
    const characteristic = computed(
        () => byDbColumn.value?.[fieldKey.value] || property.value?.characteristic
    );
    const unit = computed(
        () =>
            characteristic.value?.unit ??
            property.value?.characteristic?.unit ??
            ""
    );

    const displayValue = computed(() => {
        const v = value.value;
        if (v === null || v === undefined || v === "") return "—";
        if (typeof v === "boolean") return v ? "Oui" : "Non";
        const u = unit.value;
        return u ? `${String(v)} ${u}` : String(v);
    });

    const hasFormula = computed(
        () =>
            !!(
                opts.value?.formulaResolved ||
                opts.value?.formulaRaw ||
                characteristic.value?.formula
            )
    );
    const formulaResolved = computed(
        () =>
            opts.value?.formulaResolved ??
            characteristic.value?.formula ??
            ""
    );
    const formulaRaw = computed(
        () =>
            opts.value?.formulaRaw ?? characteristic.value?.formula_raw ?? ""
    );
    const levelTable = computed(
        () =>
            opts.value?.levelTable ?? characteristic.value?.level_table ?? []
    );

    return {
        property,
        value,
        displayValue,
        unit,
        cell,
        characteristic,
        hasFormula,
        formulaResolved,
        formulaRaw,
        levelTable,
    };
}
