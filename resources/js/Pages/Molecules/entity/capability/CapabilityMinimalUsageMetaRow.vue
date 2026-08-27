<script setup>
/**
 * Ligne méta capacité : PA, PO + portée modifiable, incantation/rituel, durée / relance, élément & magie au survol.
 *
 * @description
 * Aligné sur {@link SpellMinimalUsageMetaRow} (icônes / couleurs BDD groupe spell+capability), sans ligne de vue ni lancers/tour sorts.
 */
import { computed } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import { buildSpellCastingRitualPresentation, readSpellField } from "@/Utils/Entity/spellMinimalUsageDisplay";
import { buildCapabilityDurationReusePresentation } from "@/Utils/Entity/capabilityMinimalUsageDisplay";
import SpellUsageCharacteristicTooltipPanel from "@/Pages/Molecules/entity/spell/SpellUsageCharacteristicTooltipPanel.vue";
import {
    resolveSpellUsageCharacteristicVisual,
    spellUsageIconBackdropStyle,
    spellUsageTextColorStyle,
} from "@/Utils/Entity/spellUsageCharacteristicVisual";
import { isPoCac, PO_CAC_ICON, PO_CAC_LABEL, trimTrailingPoSeparators } from "@/Composables/entity/useCharacteristicDisplay";

function tooltipAccentFromVisual(visual) {
    const c = visual?.color;
    return typeof c === "string" && c.startsWith("#") ? { "--color": c } : {};
}

const props = defineProps({
    entity: { type: Object, required: true },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
    canShowField: { type: Function, required: true },
    propertySize: {
        type: String,
        default: "xs",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
    rowClass: { type: String, default: "text-xs gap-1.5" },
    hoverInnerGapClass: { type: String, default: "gap-1.5" },
});

const USAGE_IMG_PX = "14";

const castingRitual = computed(() => buildSpellCastingRitualPresentation(props.entity));

const durationReuse = computed(() => buildCapabilityDurationReusePresentation(props.entity));

const castingRitualTooltipAccent = computed(() => {
    const c = castingRitual.value.showCasting
        ? castingRitual.value.castingVisual?.color
        : castingRitual.value.ritualVisual?.color;
    return typeof c === "string" && c.startsWith("#") ? { "--color": c } : {};
});

const poDisplayForCac = computed(() => {
    const raw = readSpellField(props.entity, "po", "po");
    return trimTrailingPoSeparators(raw != null ? String(raw) : null) ?? "";
});

const spellPoIsCac = computed(() => isPoCac(poDisplayForCac.value));

const isMagicRaw = computed(() => readSpellField(props.entity, "isMagic", "is_magic"));
const isMagicDefined = computed(
    () => props.entity != null && isMagicRaw.value !== null && isMagicRaw.value !== undefined,
);
const isMagicOn = computed(() => Boolean(isMagicRaw.value));
const magicUsageVisual = computed(() =>
    isMagicDefined.value
        ? resolveSpellUsageCharacteristicVisual("is_magic", isMagicOn.value)
        : resolveSpellUsageCharacteristicVisual("is_magic"),
);
const magicKindLabel = computed(() => (isMagicOn.value ? "Wakfu" : "Physique"));

const magicStatusLabel = computed(() =>
    !isMagicDefined.value
        ? ""
        : isMagicOn.value
          ? "Capacité Wakfu : puise dans le Wakfu (équivalent du magique côté Dofus)."
          : "Capacité physique : puise dans la force physique.",
);

const magicAffixMuted = computed(
    () =>
        isMagicDefined.value &&
        !isMagicOn.value &&
        magicUsageVisual.value.hasIcon &&
        !magicUsageVisual.value.hasDistinctFalseIcon,
);
</script>

<template>
    <div class="flex flex-wrap items-center" :class="rowClass">
        <EntityPropertyDisplay
            v-if="canShowField('is_passive')"
            field-key="is_passive"
            :entity="entity"
            entity-type="capability"
            :display-mode="PROPERTY_DISPLAY_MODES.compact"
            :descriptors="descriptors"
            :table-meta="tableMeta"
            :size="propertySize"
            variant="icon"
            hide-field-label
            class="min-w-0 shrink-0"
        />
        <EntityPropertyDisplay
            v-if="canShowField('pa')"
            field-key="pa"
            :entity="entity"
            entity-type="capability"
            :display-mode="PROPERTY_DISPLAY_MODES.compact"
            :descriptors="descriptors"
            :table-meta="tableMeta"
            :size="propertySize"
            class="min-w-0"
        />
        <div
            v-if="canShowField('po')"
            class="inline-flex min-w-0 max-w-full flex-wrap items-center gap-0.5"
        >
            <EntityPropertyDisplay
                field-key="po"
                :entity="entity"
                entity-type="capability"
                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                :descriptors="descriptors"
                :table-meta="tableMeta"
                :size="propertySize"
                :hide-characteristic-icon="spellPoIsCac"
                :characteristic-label-image-source="spellPoIsCac ? PO_CAC_ICON : ''"
                :characteristic-label-image-alt="PO_CAC_LABEL"
                :characteristic-value-text-class="spellPoIsCac ? 'text-red-600' : ''"
                class="min-w-0"
            />
        </div>

        <Tooltip
            v-if="castingRitual.show"
            placement="top"
            color="neutral"
            :glass="false"
            :accent-style="castingRitualTooltipAccent"
        >
            <template #content>
                <div class="flex max-w-xs flex-col gap-2 text-xs">
                    <SpellUsageCharacteristicTooltipPanel
                        v-if="castingRitual.showCasting"
                        :visual="castingRitual.castingVisual"
                        :status-text="castingRitual.castingText"
                    />
                    <SpellUsageCharacteristicTooltipPanel
                        v-if="castingRitual.showRitual && castingRitual.ritualVisual"
                        :visual="castingRitual.ritualVisual"
                        status-text="Rituel disponible"
                        :show-characteristic-name="false"
                        show-boolean-glyph
                        :boolean-on="true"
                    />
                </div>
            </template>
            <span
                class="inline-flex cursor-help flex-wrap items-center gap-x-1 gap-y-0.5 border-b border-dotted border-base-content/30"
                tabindex="0"
            >
                <span
                    v-if="castingRitual.showCasting"
                    class="inline-flex items-center gap-0.5"
                >
                    <span
                        v-if="castingRitual.castingVisual.hasIcon"
                        class="inline-flex shrink-0 items-center justify-center p-px"
                        :style="spellUsageIconBackdropStyle(castingRitual.castingVisual.color)"
                    >
                        <Image
                            :source="castingRitual.castingVisual.source"
                            alt=""
                            :width="USAGE_IMG_PX"
                            :height="USAGE_IMG_PX"
                            fit="contain"
                            class="block shrink-0"
                        />
                    </span>
                    <span
                        class="text-base-content/90"
                        :style="spellUsageTextColorStyle(castingRitual.castingVisual.color)"
                    >{{ castingRitual.castingText }}</span>
                </span>
                <span
                    v-if="castingRitual.showRitual && castingRitual.ritualVisual"
                    class="inline-flex items-center gap-0.5"
                >
                    <span
                        v-if="castingRitual.ritualVisual.hasIcon"
                        class="inline-flex shrink-0 cursor-default items-center justify-center p-px"
                        :style="spellUsageIconBackdropStyle(castingRitual.ritualVisual.color)"
                    >
                        <Image
                            :source="castingRitual.ritualVisual.source"
                            alt="Rituel disponible"
                            :width="USAGE_IMG_PX"
                            :height="USAGE_IMG_PX"
                            fit="contain"
                            class="block shrink-0"
                        />
                    </span>
                </span>
            </span>
        </Tooltip>

        <Tooltip
            v-if="durationReuse.duration.show"
            placement="top"
            color="neutral"
            :accent-style="tooltipAccentFromVisual(durationReuse.duration.visual)"
        >
            <template #content>
                <div class="max-w-xs whitespace-pre-line text-xs">
                    {{ durationReuse.duration.tooltip }}
                </div>
            </template>
            <span
                class="inline-flex cursor-help items-center gap-0.5 border-b border-dotted border-base-content/30"
                tabindex="0"
            >
                <span
                    v-if="durationReuse.duration.visual.hasIcon"
                    class="inline-flex shrink-0 items-center justify-center p-px"
                    :style="spellUsageIconBackdropStyle(durationReuse.duration.visual.color)"
                >
                    <Image
                        :source="durationReuse.duration.visual.source"
                        alt=""
                        :width="USAGE_IMG_PX"
                        :height="USAGE_IMG_PX"
                        fit="contain"
                        class="block shrink-0"
                    />
                </span>
                <span
                    class="text-base-content/90"
                    :style="spellUsageTextColorStyle(durationReuse.duration.visual.color)"
                >{{ durationReuse.duration.text }}</span>
            </span>
        </Tooltip>

        <Tooltip
            v-if="durationReuse.reuseDelay.show"
            placement="top"
            color="neutral"
            :accent-style="tooltipAccentFromVisual(durationReuse.reuseDelay.visual)"
        >
            <template #content>
                <div class="max-w-xs whitespace-pre-line text-xs">
                    {{ durationReuse.reuseDelay.tooltip }}
                </div>
            </template>
            <span
                class="inline-flex cursor-help items-center gap-0.5 border-b border-dotted border-base-content/30"
                tabindex="0"
            >
                <span
                    v-if="durationReuse.reuseDelay.visual.hasIcon"
                    class="inline-flex shrink-0 items-center justify-center p-px"
                    :style="spellUsageIconBackdropStyle(durationReuse.reuseDelay.visual.color)"
                >
                    <Image
                        :source="durationReuse.reuseDelay.visual.source"
                        alt=""
                        :width="USAGE_IMG_PX"
                        :height="USAGE_IMG_PX"
                        fit="contain"
                        class="block shrink-0"
                    />
                </span>
                <span
                    class="text-base-content/90"
                    :style="spellUsageTextColorStyle(durationReuse.reuseDelay.visual.color)"
                >{{ durationReuse.reuseDelay.text }}</span>
            </span>
        </Tooltip>

        <div
            v-if="canShowField('element')"
            class="grid max-w-full grid-rows-[0fr] transition-[grid-template-rows] duration-200 ease-out group-hover:grid-rows-[1fr]"
        >
            <div class="min-h-0 overflow-hidden group-hover:overflow-visible">
                <EntityPropertyDisplay
                    field-key="element"
                    :entity="entity"
                    entity-type="capability"
                    :display-mode="PROPERTY_DISPLAY_MODES.compact"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :size="propertySize"
                    class="min-w-0"
                />
            </div>
        </div>

        <div
            v-if="canShowField('is_magic') && isMagicDefined"
            class="grid max-w-full grid-rows-[0fr] transition-[grid-template-rows] duration-200 ease-out group-hover:grid-rows-[1fr]"
        >
            <div class="min-h-0 overflow-hidden group-hover:overflow-visible">
                <div
                    class="inline-flex max-w-full flex-wrap items-center"
                    :class="hoverInnerGapClass"
                >
                    <Tooltip
                        placement="top"
                        color="neutral"
                        :glass="false"
                        :accent-style="tooltipAccentFromVisual(magicUsageVisual)"
                    >
                        <template #content>
                            <SpellUsageCharacteristicTooltipPanel
                                :visual="magicUsageVisual"
                                :status-text="magicStatusLabel"
                                :show-boolean-glyph="isMagicDefined"
                                :boolean-on="isMagicOn"
                            />
                        </template>
                        <span
                            class="inline-flex shrink-0 cursor-default items-center gap-0.5"
                            tabindex="0"
                        >
                            <span
                                v-if="magicUsageVisual.hasIcon"
                                class="inline-flex shrink-0 items-center justify-center p-px"
                                :class="{ 'opacity-[0.42]': magicAffixMuted }"
                                :style="spellUsageIconBackdropStyle(magicUsageVisual.color)"
                            >
                                <Image
                                    :source="magicUsageVisual.source"
                                    :alt="magicKindLabel"
                                    :width="USAGE_IMG_PX"
                                    :height="USAGE_IMG_PX"
                                    fit="contain"
                                    class="block shrink-0"
                                />
                            </span>
                            <span
                                class="text-base-content/80"
                                :style="spellUsageTextColorStyle(magicUsageVisual.color)"
                            >{{ magicKindLabel }}</span>
                        </span>
                    </Tooltip>
                </div>
            </div>
        </div>
    </div>
</template>
