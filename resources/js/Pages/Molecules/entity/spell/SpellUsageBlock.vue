<script setup>
/**
 * Bloc usage unifié (Minimal / Line / bandeau Full) : méta PA-PO + résolution + chips d’effets.
 * Notes de règles sous la méta et/ou les effets (Soin, Invocation, piège, glyphe, vol de vie…).
 *
 * @example
 * <SpellUsageBlock :entity="spell" :descriptors="descriptors" :can-show-field="canShowField" />
 */
import { computed } from "vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import SpellMinimalUsageMetaRow from "@/Pages/Molecules/entity/spell/SpellMinimalUsageMetaRow.vue";
import SpellUsageAreaColumn from "@/Pages/Molecules/entity/spell/SpellUsageAreaColumn.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { buildSpellResolutionFormula } from "@/Utils/Entity/spellResolutionFormula";
import SpellResolutionFormula from "@/Pages/Molecules/entity/spell/SpellResolutionFormula.vue";
import { spellTypeRuleNotes } from "@/Utils/Entity/spellTypeRuleNotes";

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
    /** Notes de règles. Laisser vrai en Line / Full / Minimal. */
    showRuleNotes: { type: Boolean, default: true },
    notesClass: {
        type: String,
        default: "mt-1 text-xs leading-snug opacity-80",
    },
    /**
     * Affiche le mode de résolution en compact (popover) avant les effets.
     * La vue full ne doit pas l’utiliser : la résolution vit dans sa section.
     */
    showResolutionCompact: { type: Boolean, default: false },
    /** Commentaire libre (`spell.effect`) sous les effets structurés, sans titre. */
    freeEffectComment: { type: String, default: "" },
    /** Zone du degré actif (affichée en colonne à droite de la méta). */
    area: { type: String, default: "" },
    /** true = schéma de zone visible (page / modal). */
    showAreaSchema: { type: Boolean, default: false },
});

const showMeta = computed(() => props.parts === "all" || props.parts === "meta");
const showEffectsPart = computed(() => props.parts === "all" || props.parts === "effects");
const areaValue = computed(() => (props.area != null ? String(props.area).trim() : ""));
const showAreaColumn = computed(() => showMeta.value && areaValue.value !== "");

const effectDisplayCell = computed(() =>
    resolveSpellEffectsDisplayCell(props.entity, {
        size: "xs",
        context: "minimal",
        ctx: props.tableMeta,
        maxEffectRows: props.maxEffectRows,
    }),
);

const hasEffects = computed(() => spellEffectsCellHasContent(effectDisplayCell.value));
const resolutionUsage = computed(() => buildSpellResolutionFormula(props.entity));
const ruleNotes = computed(() => (props.showRuleNotes ? spellTypeRuleNotes(props.entity) : []));
const freeComment = computed(() => {
    const t = props.freeEffectComment != null ? String(props.freeEffectComment).trim() : "";
    return t;
});

const showCompactResolution = computed(
    () => props.showResolutionCompact && resolutionUsage.value.show,
);

const showEffectsBlock = computed(
    () =>
        showEffectsPart.value &&
        (hasEffects.value ||
            showCompactResolution.value ||
            freeComment.value !== "" ||
            props.showEmptyEffects),
);

const effectsClass = computed(() =>
    props.flushEffects ? "w-full min-w-0" : props.effectsWrapperClass,
);
</script>

<template>
    <div class="spell-usage-block min-w-0">
        <div
            v-if="showMeta"
            class="flex min-w-0 items-start gap-3"
            :class="{ 'justify-between': showAreaColumn }"
        >
            <div class="min-w-0 flex-1">
                <SpellMinimalUsageMetaRow
                    :entity="entity"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    :show-spell-types-cell="showSpellTypesCell"
                    :property-size="propertySize"
                    :row-class="rowClass"
                    :hover-inner-gap-class="hoverInnerGapClass"
                />
                <div
                    v-if="ruleNotes.length"
                    data-cy="spell-rule-notes"
                    class="spell-rule-notes"
                >
                    <p
                        v-for="note in ruleNotes"
                        :key="note"
                        :class="notesClass"
                    >
                        {{ note }}
                    </p>
                </div>
            </div>
            <SpellUsageAreaColumn
                v-if="showAreaColumn"
                :area="areaValue"
                :show-schema="showAreaSchema"
            />
        </div>
        <div
            v-else-if="ruleNotes.length"
            data-cy="spell-rule-notes"
            class="spell-rule-notes"
        >
            <p
                v-for="note in ruleNotes"
                :key="note"
                :class="notesClass"
            >
                {{ note }}
            </p>
        </div>
        <div v-if="showEffectsBlock" :class="effectsClass">
            <SpellResolutionFormula
                v-if="showCompactResolution"
                :entity="entity"
                variant="compact"
                :class="resolutionClass"
            />
            <CellRenderer
                v-if="hasEffects"
                :cell="effectDisplayCell"
                ui-color="primary"
                :class="cellClass"
            />
            <p
                v-if="freeComment"
                class="mt-1 text-xs leading-snug text-base-content/75 whitespace-pre-wrap"
                data-cy="spell-free-effect-comment"
            >
                {{ freeComment }}
            </p>
            <p
                v-else-if="showEmptyEffects && !hasEffects && !showCompactResolution"
                class="text-sm italic text-base-content/55"
            >
                {{ emptyEffectsText }}
            </p>
        </div>
    </div>
</template>
