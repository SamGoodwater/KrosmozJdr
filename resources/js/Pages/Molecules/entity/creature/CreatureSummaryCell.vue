<script setup>
/**
 * CreatureSummaryCell — Molecule
 *
 * @description
 * Rendu réutilisable (Monster, NPC, Player) pour afficher des "chips" (icône + valeur)
 * en s'appuyant sur les caractéristiques issues de la BDD (meta.characteristics.*).
 */

import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    variant: {
        type: String,
        required: true,
        validator: (v) => ["resistance", "damage", "stats", "combat", "control"].includes(v),
    },
    creature: { type: Object, default: null },
    /**
     * Mapping : { [db_column]: { name, short_name, helper, icon, color, unit, ... } }
     */
    characteristicsByDbColumn: { type: Object, default: () => ({}) },
});

const getMeta = (dbColumn) => {
    const by = props.characteristicsByDbColumn || {};
    return by && typeof by === "object" ? by?.[dbColumn] || null : null;
};

const formatChipValue = ({ fixed, percent }) => {
    const hasFixed = fixed !== null && typeof fixed !== "undefined" && String(fixed) !== "";
    const hasPercent = percent !== null && typeof percent !== "undefined" && String(percent) !== "";
    if (hasFixed && hasPercent) return `${String(fixed)} (${String(percent)}%)`;
    if (hasFixed) return String(fixed);
    if (hasPercent) return `${String(percent)}%`;
    return null;
};

const buildChip = ({ dbColumn, value, tooltipValue }) => {
    if (value === null || typeof value === "undefined" || value === "") return null;
    const meta = getMeta(dbColumn);
    const label = meta?.short_name || meta?.name || dbColumn;
    const unit = meta?.unit ? ` ${meta.unit}` : "";
    const helper = meta?.helper ? ` — ${meta.helper}` : "";
    const tooltip = `${label}: ${String(tooltipValue ?? value)}${unit}${helper}`;
    return {
        icon: meta?.icon || null,
        color: meta?.color || null,
        value: String(value),
        tooltip,
    };
};

const items = computed(() => {
    const c = props.creature;
    if (!c || typeof c !== "object") return [];

    const out = [];
    const els = ["neutre", "terre", "feu", "air", "eau"];

    if (props.variant === "resistance") {
        for (const el of els) {
            const fixedDb = `res_fixe_${el}`;
            const percentDb = `res_${el}`;
            const val = formatChipValue({ fixed: c?.[fixedDb], percent: c?.[percentDb] });
            if (!val) continue;

            // On privilégie la meta de la résistance fixe (sinon %)
            const fixedMeta = getMeta(fixedDb);
            const percentMeta = getMeta(percentDb);
            const label = fixedMeta?.short_name || fixedMeta?.name || percentMeta?.short_name || percentMeta?.name || `Rés. ${el}`;
            const helper = fixedMeta?.helper || percentMeta?.helper || "";
            const tooltip = `${label}: ${val}${helper ? ` — ${helper}` : ""}`;
            out.push({
                icon: fixedMeta?.icon || percentMeta?.icon || null,
                color: fixedMeta?.color || percentMeta?.color || null,
                value: val,
                tooltip,
            });
        }
        return out;
    }

    if (props.variant === "damage") {
        const touch = buildChip({ dbColumn: "touch", value: c?.touch });
        if (touch) out.push(touch);
        for (const el of els) {
            const db = `do_fixe_${el}`;
            const chip = buildChip({ dbColumn: db, value: c?.[db] });
            if (chip) out.push(chip);
        }
        return out;
    }

    if (props.variant === "stats") {
        for (const db of ["strong", "intel", "agi", "chance", "vitality", "sagesse"]) {
            const chip = buildChip({ dbColumn: db, value: c?.[db] });
            if (chip) out.push(chip);
        }
        return out;
    }

    if (props.variant === "combat") {
        for (const db of ["pa", "pm", "po", "life", "ini", "invocation"]) {
            const chip = buildChip({ dbColumn: db, value: c?.[db] });
            if (chip) out.push(chip);
        }
        return out;
    }

    // control = CA + esquive PA/PM + fuite + tacle
    if (props.variant === "control") {
        for (const db of ["ca", "dodge_pa", "dodge_pm", "fuite", "tacle"]) {
            const chip = buildChip({ dbColumn: db, value: c?.[db] });
            if (chip) out.push(chip);
        }
        return out;
    }

    return out;
});
</script>

<template>
    <span class="inline-flex flex-wrap items-center gap-x-2 gap-y-0.5">
        <template v-for="(item, idx) in items" :key="idx">
            <Tooltip :content="item.tooltip" placement="top" class="inline-flex items-center gap-1">
                <Icon
                    v-if="item.icon"
                    :source="item.icon"
                    :alt="item.tooltip || ''"
                    size="xs"
                    class="shrink-0 opacity-80"
                    :style="item.color ? { color: item.color } : undefined"
                />
                <span class="text-xs" :style="item.color ? { color: item.color } : undefined">
                    {{ item.value }}
                </span>
            </Tooltip>
        </template>
        <span v-if="!items.length" class="text-base-content/40">—</span>
    </span>
</template>

