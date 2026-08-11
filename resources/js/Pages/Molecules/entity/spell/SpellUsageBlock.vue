<script setup>
/**
 * Bloc usage unifié (Minimal / Line / bandeau Full) : méta PA-PO + résolution + chips d’effets.
 *
 * @example
 * <SpellUsageBlock :entity="spell" :descriptors="descriptors" :can-show-field="canShowField" />
 */
import { computed } from "vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import SpellMinimalUsageMetaRow from "@/Pages/Molecules/entity/spell/SpellMinimalUsageMetaRow.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { buildResolutionSummary } from "@/Utils/Entity/spellMinimalUsageDisplay";

const props = defineProps({
    entity: { type: Object, required: true },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
    canShowField: { type: Function, required: true },
    showSpellTypesCell: { type: Boolean, default: false },
    propertySize: {
        type: String,
        default: "xs",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
    rowClass: { type: String, default: "gap-1.5 text-xs" },
    hoverInnerGapClass: { type: String, default: "gap-1.5" },
    /** Nombre max de lignes pour le fallback texte `effect`. */
    maxEffectRows: { type: Number, default: 3 },
    resolutionClass: { type: String, default: "mb-1 text-xs text-base-content/75" },
    effectsWrapperClass: {
        type: String,
        default: "w-full pt-1.5 mt-1 border-t border-base-300",
    },
    cellClass: {
        type: String,
        default: "text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap",
    },
    /** Affiche un empty state si aucun effet / résolution. */
    showEmptyEffects: { type: Boolean, default: false },
    emptyEffectsText: {
        type: String,
        default: "Aucun effet structuré pour ce sort.",
    },
    /**
     * `all` : méta + effets ; `meta` / `effects` pour découper Minimal/Line
     * (méta sous le titre, effets pleine largeur sous la rangée).
     */
    parts: {
        type: String,
        default: "all",
        validator: (v) => ["all", "meta", "effects"].includes(v),
    },
    /** Masque la bordure haute du bloc effets. */
    flushEffects: { type: Boolean, default: false },
});

const showMeta = computed(() => props.parts === "all" || props.parts === "meta");
const showEffectsPart = computed(() => props.parts === "all" || props.parts === "effects");

const effectDisplayCell = computed(() =>
    resolveSpellEffectsDisplayCell(props.entity, {
        size: "xs",
        context: "minimal",
        ctx: props.tableMeta,
        maxEffectRows: props.maxEffectRows,
    }),
);

const hasEffects = computed(() => spellEffectsCellHasContent(effectDisplayCell.value));
const resolutionUsage = computed(() => buildResolutionSummary(props.entity));

const showEffectsBlock = computed(
    () =>
        showEffectsPart.value &&
        (hasEffects.value || resolutionUsage.value.show || props.showEmptyEffects),
);

const effectsClass = computed(() =>
    props.flushEffects ? "w-full min-w-0" : props.effectsWrapperClass,
);
</script>

<template>
    <div class="spell-usage-block min-w-0">
        <SpellMinimalUsageMetaRow
            v-if="showMeta"
            :entity="entity"
            :descriptors="descriptors"
            :table-meta="tableMeta"
            :can-show-field="canShowField"
            :show-spell-types-cell="showSpellTypesCell"
            :property-size="propertySize"
            :row-class="rowClass"
            :hover-inner-gap-class="hoverInnerGapClass"
        />
        <div v-if="showEffectsBlock" :class="effectsClass">
            <p v-if="resolutionUsage.show" :class="resolutionClass">
                {{ resolutionUsage.text }}
            </p>
            <CellRenderer
                v-if="hasEffects"
                :cell="effectDisplayCell"
                ui-color="primary"
                :class="cellClass"
            />
            <p
                v-else-if="showEmptyEffects && !resolutionUsage.show"
                class="text-sm italic text-base-content/55"
            >
                {{ emptyEffectsText }}
            </p>
        </div>
    </div>
</template>
