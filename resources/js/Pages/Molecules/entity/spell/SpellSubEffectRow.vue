<script setup>
/**
 * Ligne de sous-effet (pivot) — présentation unifiée selon l’action.
 *
 * @props {object} row - Sérialisation SpellResource (degré.rows[])
 * @props {'large'|'compact'|'line'|'minimal'} layout - Journal large/compact ; table line/minimal via chips
 * @props {string|null} degreeArea - Zone du degré (affichée en fin de ligne quand présente)
 */
import { computed } from "vue";
import SpellSubEffectActionPresentation from "@/Pages/Molecules/entity/spell/SpellSubEffectActionPresentation.vue";
import { buildUnifiedSubEffectModel } from "@/Composables/entity/useSpellSubEffectPresentation";

const props = defineProps({
    row: {
        type: Object,
        required: true,
    },
    layout: {
        type: String,
        default: "large",
        validator: (v) => ["large", "compact", "line", "minimal"].includes(v),
    },
    degreeArea: {
        type: [String, null],
        default: null,
    },
});

const model = computed(() =>
    buildUnifiedSubEffectModel({
        source: "row",
        row: props.row,
        layout: props.layout,
        degreeArea: props.degreeArea,
    }),
);
</script>

<template>
    <div class="overflow-visible border-b border-base-300/50 py-2 last:border-b-0">
        <SpellSubEffectActionPresentation :model="model" />
    </div>
</template>
