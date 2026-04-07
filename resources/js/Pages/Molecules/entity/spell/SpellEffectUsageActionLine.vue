<script setup>
/**
 * Ligne d’effet structurée (chips API `effect_usages_chips` avec `action_slug`).
 *
 * @props {object} item - Item normalisé Spell._toEffectSummaryCell
 * @props {'minimal'|'line'} density - minimal = cartes ; line = liste dense
 */
import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import SpellSubEffectActionPresentation from "@/Pages/Molecules/entity/spell/SpellSubEffectActionPresentation.vue";
import SpellEffectChipTooltipContent from "@/Pages/Molecules/entity/spell/SpellEffectChipTooltipContent.vue";
import { buildUnifiedSubEffectModel } from "@/Composables/entity/useSpellSubEffectPresentation";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    density: {
        type: String,
        default: "minimal",
        validator: (v) => ["minimal", "line"].includes(v),
    },
});

const layout = computed(() => (props.density === "line" ? "line" : "minimal"));

const model = computed(() =>
    buildUnifiedSubEffectModel({
        source: "chip",
        chip: props.item,
        layout: layout.value,
    }),
);

const detailText = computed(() => {
    const t = props.item?.tooltip;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});
</script>

<template>
    <Tooltip class="inline-flex max-w-full min-w-0" placement="top" :glass="false">
        <SpellSubEffectActionPresentation :model="model" />
        <template #content>
            <SpellEffectChipTooltipContent :model="model" :detail-text="detailText" />
        </template>
    </Tooltip>
</template>
