<script setup>
/**
 * Faits du degré actif pour la carte minimale : coût puis incantation.
 *
 * Ligne 1 : PA, PO (+ icônes), réaction si vrai, zone.
 * Ligne 2 : lancers, délai si ≥ 2 tours, durée si hors défaut.
 * Pas d’intertitre : l’espacement sépare les groupes.
 *
 * @example
 * <SpellMinimalDegreeFacts :entity="usageEntity" :area="area" … />
 */
import { computed } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import SpellUsageAreaColumn from "@/Pages/Molecules/entity/spell/SpellUsageAreaColumn.vue";
import SpellUsageCharacteristicTooltipPanel from "@/Pages/Molecules/entity/spell/SpellUsageCharacteristicTooltipPanel.vue";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import {
    buildSpellCastingRitualPresentation,
    buildSpellCastUsagePresentation,
    readSpellField,
} from "@/Utils/Entity/spellMinimalUsageDisplay";
import {
    resolveSpellUsageCharacteristicVisual,
    spellUsageIconBackdropStyle,
    spellUsageTextColorStyle,
} from "@/Utils/Entity/spellUsageCharacteristicVisual";
import {
    formatPoRangeDisplay,
    isPoCac,
    PO_CAC_ICON,
    PO_CAC_LABEL,
    trimTrailingPoSeparators,
} from "@/Composables/entity/useCharacteristicDisplay";

/** Accent tooltip Daisy quand la carac expose un hex. */
function tooltipAccentFromVisual(visual) {
    const c = visual?.color;
    return typeof c === "string" && c.startsWith("#") ? { "--color": c } : {};
}

const props = defineProps({
    entity: { type: Object, required: true },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
    canShowField: { type: Function, required: true },
    area: { type: String, default: "" },
    propertySize: {
        type: String,
        default: "xs",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const USAGE_IMG_PX = "14";

const poEditableRaw = computed(() => readSpellField(props.entity, "poEditable", "po_editable"));
const poEditableOn = computed(() => Boolean(poEditableRaw.value));
const poEditableDefined = computed(
    () => poEditableRaw.value !== null && poEditableRaw.value !== undefined,
);

const sightLineRaw = computed(() => readSpellField(props.entity, "sightLine", "sight_line"));
const sightLineOn = computed(() => Boolean(sightLineRaw.value));
const sightLineDefined = computed(
    () => sightLineRaw.value !== null && sightLineRaw.value !== undefined,
);

const poUsageVisual = computed(() =>
    resolveSpellUsageCharacteristicVisual("po_editable", poEditableDefined.value ? poEditableOn.value : undefined),
);
const sightUsageVisual = computed(() =>
    resolveSpellUsageCharacteristicVisual("sight_line", sightLineDefined.value ? sightLineOn.value : undefined),
);

const castUsage = computed(() => buildSpellCastUsagePresentation(props.entity));
const castingRitual = computed(() => buildSpellCastingRitualPresentation(props.entity));

const allowsReactionRaw = computed(() =>
    readSpellField(props.entity, "allowsReaction", "allows_reaction"),
);
const showReaction = computed(() => Boolean(allowsReactionRaw.value));

const poEditableStatusLabel = computed(() =>
    !poEditableDefined.value
        ? ""
        : poEditableOn.value
          ? "La portée peut être modifiée en jeu selon le PO du lanceur ou de la lanceuse."
          : "La portée n'est pas modifiable en jeu selon le PO du lanceur ou de la lanceuse.",
);

const sightLineStatusLabel = computed(() =>
    !sightLineDefined.value
        ? ""
        : sightLineOn.value
          ? "Une ligne de vue vers la zone cible est obligatoire."
          : "Aucune ligne de vue vers la cible n'est requise.",
);

const poAffixMuted = computed(
    () =>
        poEditableDefined.value &&
        !poEditableOn.value &&
        poUsageVisual.value.hasIcon &&
        !poUsageVisual.value.hasDistinctFalseIcon,
);
const sightAffixMuted = computed(
    () =>
        sightLineDefined.value &&
        !sightLineOn.value &&
        sightUsageVisual.value.hasIcon &&
        !sightUsageVisual.value.hasDistinctFalseIcon,
);

const castingRitualTooltipAccent = computed(() => {
    const c = castingRitual.value.showCasting
        ? castingRitual.value.castingVisual?.color
        : castingRitual.value.ritualVisual?.color;
    return typeof c === "string" && c.startsWith("#") ? { "--color": c } : {};
});

const poDisplayForCac = computed(() => {
    const min = readSpellField(props.entity, "poMin", "po_min");
    const max = readSpellField(props.entity, "poMax", "po_max");
    const fromParts = formatPoRangeDisplay(min, max);
    if (fromParts != null) {
        return fromParts;
    }
    const raw = readSpellField(props.entity, "po", "po");
    return trimTrailingPoSeparators(raw != null ? String(raw) : null) ?? "";
});

const spellPoIsCac = computed(() => isPoCac(poDisplayForCac.value));

const areaValue = computed(() => (props.area != null ? String(props.area).trim() : ""));

const showCastLine = computed(
    () =>
        (castUsage.value.show && castUsage.value.metas) ||
        castingRitual.value.show,
);
</script>

<template>
    <div class="spell-minimal-degree-facts flex min-w-0 flex-col gap-1.5" data-cy="spell-minimal-degree-facts">
        <!-- Coût : PA · PO · réaction · zone -->
        <div class="flex min-w-0 flex-wrap items-center gap-x-1.5 gap-y-0.5 text-xs">
            <EntityPropertyDisplay
                v-if="canShowField('pa')"
                field-key="pa"
                :entity="entity"
                entity-type="spell"
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
                <Tooltip
                    v-if="poUsageVisual.hasIcon"
                    placement="top"
                    color="neutral"
                    :accent-style="tooltipAccentFromVisual(poUsageVisual)"
                >
                    <template #content>
                        <SpellUsageCharacteristicTooltipPanel
                            :visual="poUsageVisual"
                            :status-text="poEditableStatusLabel"
                            :show-boolean-glyph="poEditableDefined"
                            :boolean-on="poEditableOn"
                        />
                    </template>
                    <span
                        class="inline-flex shrink-0 cursor-default items-center justify-center p-px"
                        :class="{ 'opacity-[0.42]': poAffixMuted }"
                        :style="spellUsageIconBackdropStyle(poUsageVisual.color)"
                        tabindex="0"
                    >
                        <Image
                            :source="poUsageVisual.source"
                            :alt="
                                poEditableOn
                                    ? 'Portée modulable en jeu selon le PO'
                                    : 'Portée fixe, non modulable en jeu selon le PO'
                            "
                            :width="USAGE_IMG_PX"
                            :height="USAGE_IMG_PX"
                            fit="contain"
                            class="block shrink-0"
                        />
                    </span>
                </Tooltip>
                <EntityPropertyDisplay
                    field-key="po"
                    :entity="entity"
                    entity-type="spell"
                    :display-mode="PROPERTY_DISPLAY_MODES.compact"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :size="propertySize"
                    :hide-characteristic-icon="poUsageVisual.hasIcon || spellPoIsCac"
                    :characteristic-label-image-source="spellPoIsCac ? PO_CAC_ICON : ''"
                    :characteristic-label-image-alt="PO_CAC_LABEL"
                    :characteristic-value-text-class="spellPoIsCac ? 'text-red-600' : ''"
                    class="min-w-0"
                />
                <template v-if="sightUsageVisual.hasIcon">
                    <Tooltip
                        placement="top"
                        color="neutral"
                        :accent-style="tooltipAccentFromVisual(sightUsageVisual)"
                    >
                        <template #content>
                            <SpellUsageCharacteristicTooltipPanel
                                :visual="sightUsageVisual"
                                :status-text="sightLineStatusLabel"
                                :show-boolean-glyph="sightLineDefined"
                                :boolean-on="sightLineOn"
                            />
                        </template>
                        <span
                            class="inline-flex shrink-0 cursor-default items-center justify-center p-px"
                            :class="{ 'opacity-[0.42]': sightAffixMuted }"
                            :style="spellUsageIconBackdropStyle(sightUsageVisual.color)"
                            tabindex="0"
                        >
                            <Image
                                :source="sightUsageVisual.source"
                                :alt="
                                    sightLineOn
                                        ? 'Ligne de vue obligatoire vers la zone cible'
                                        : 'Ligne de vue vers la cible non requise'
                                "
                                :width="USAGE_IMG_PX"
                                :height="USAGE_IMG_PX"
                                fit="contain"
                                class="block shrink-0"
                            />
                        </span>
                    </Tooltip>
                </template>
            </div>
            <span
                v-if="showReaction && canShowField('allows_reaction')"
                class="badge badge-xs badge-warning font-semibold"
                data-cy="spell-minimal-reaction"
            >
                Réaction
            </span>
            <SpellUsageAreaColumn
                v-if="areaValue"
                :area="areaValue"
                :show-schema="false"
                class="!self-center !items-start"
            />
        </div>

        <!-- Incantation : lancers · délai · durée · rituel -->
        <div
            v-if="showCastLine"
            class="flex min-w-0 flex-wrap items-center gap-x-1.5 gap-y-0.5 text-xs"
            data-cy="spell-minimal-incantation"
        >
            <Tooltip v-if="castUsage.show && castUsage.metas" placement="top" color="neutral">
                <template #content>
                    <div class="max-w-xs whitespace-pre-line text-xs">
                        {{ castUsage.tooltip }}
                    </div>
                </template>
                <span
                    class="inline-flex cursor-help flex-wrap items-center gap-x-1 gap-y-0.5 border-b border-dotted border-base-content/30"
                    tabindex="0"
                >
                    <span class="inline-flex items-center gap-0.5">
                        <span
                            v-if="castUsage.metas.perTurn.hasIcon"
                            class="inline-flex shrink-0 items-center justify-center p-px"
                            :style="spellUsageIconBackdropStyle(castUsage.metas.perTurn.color)"
                        >
                            <Image
                                :source="castUsage.metas.perTurn.source"
                                alt=""
                                :width="USAGE_IMG_PX"
                                :height="USAGE_IMG_PX"
                                fit="contain"
                                class="block shrink-0"
                            />
                        </span>
                        <span
                            class="font-medium tabular-nums"
                            :style="spellUsageTextColorStyle(castUsage.metas.perTurn.color)"
                        >{{ castUsage.n }}</span>
                        <span class="text-base-content/85">{{ castUsage.lancerWord }}</span>
                    </span>
                    <template v-if="castUsage.showPerTarget">
                        <span class="text-base-content/55">(max cible</span>
                        <span class="inline-flex items-center gap-0.5">
                            <span
                                v-if="castUsage.metas.perTarget.hasIcon"
                                class="inline-flex shrink-0 items-center justify-center p-px"
                                :style="spellUsageIconBackdropStyle(castUsage.metas.perTarget.color)"
                            >
                                <Image
                                    :source="castUsage.metas.perTarget.source"
                                    alt=""
                                    :width="USAGE_IMG_PX"
                                    :height="USAGE_IMG_PX"
                                    fit="contain"
                                    class="block shrink-0"
                                />
                            </span>
                            <span
                                class="font-medium tabular-nums"
                                :style="spellUsageTextColorStyle(castUsage.metas.perTarget.color)"
                            >{{ castUsage.c }}</span>
                        </span>
                        <span class="text-base-content/55">)</span>
                    </template>
                    <template v-if="castUsage.showCooldownSegment">
                        <span class="text-base-content/45" aria-hidden="true">/</span>
                        <span class="inline-flex items-center gap-0.5">
                            <span
                                v-if="castUsage.metas.cooldown.hasIcon"
                                class="inline-flex shrink-0 items-center justify-center p-px"
                                :style="spellUsageIconBackdropStyle(castUsage.metas.cooldown.color)"
                            >
                                <Image
                                    :source="castUsage.metas.cooldown.source"
                                    alt=""
                                    :width="USAGE_IMG_PX"
                                    :height="USAGE_IMG_PX"
                                    fit="contain"
                                    class="block shrink-0"
                                />
                            </span>
                            <span
                                v-if="castUsage.cooldownShowNumeric"
                                class="font-medium tabular-nums"
                                :style="spellUsageTextColorStyle(castUsage.metas.cooldown.color)"
                            >{{ castUsage.t }}</span>
                            <span
                                class="text-base-content/85"
                                :style="spellUsageTextColorStyle(castUsage.metas.cooldown.color)"
                            >{{ castUsage.cooldownTourWord }}</span>
                        </span>
                    </template>
                </span>
            </Tooltip>
            <Tooltip
                v-if="castingRitual.show"
                placement="top"
                color="neutral"
                :accent-style="castingRitualTooltipAccent"
            >
                <template #content>
                    <div class="flex max-w-xs flex-col gap-2 text-xs">
                        <SpellUsageCharacteristicTooltipPanel
                            v-if="castingRitual.showCasting"
                            :visual="castingRitual.castingVisual"
                            :status-text="castingRitual.tooltip || castingRitual.castingText"
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
        </div>
    </div>
</template>
