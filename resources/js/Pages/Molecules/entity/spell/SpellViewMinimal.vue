<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 *
 * @description
 * Colonne image : vignette + icône d’élément superposée + onglets de degré en pile.
 * Colonne contenu : nom ; coût puis incantation (toujours).
 * Identité / description / aides : slot étendu seulement (survol en `hover`, toujours en `extended`).
 * Sous les colonnes : résolution + effets.
 * Défaut `hover` : carte contractée au repos, déployée au survol.
 *
 * @props {Spell} spell - Instance du modèle Spell
 */
import { computed, ref, watch } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import EntityStateBadge from "@/Pages/Atoms/data-display/EntityStateBadge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { spellTypesCellHasRenderableContent } from "@/Utils/Entity/spellTypeVisual.js";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import SpellDegreeTabs from "@/Pages/Molecules/entity/spell/SpellDegreeTabs.vue";
import SpellEffectsJournal from "@/Pages/Molecules/entity/spell/SpellEffectsJournal.vue";
import SpellResolutionFormula from "@/Pages/Molecules/entity/spell/SpellResolutionFormula.vue";
import SpellMinimalDegreeFacts from "@/Pages/Molecules/entity/spell/SpellMinimalDegreeFacts.vue";
import SpellUsageBlock from "@/Pages/Molecules/entity/spell/SpellUsageBlock.vue";
import SpellUsageCharacteristicTooltipPanel from "@/Pages/Molecules/entity/spell/SpellUsageCharacteristicTooltipPanel.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import {
    readSpellDegreesPayload,
    spellHasDegreePayload,
    useSpellDisplayDegrees,
} from "@/Composables/entity/useSpellDisplayDegrees";
import { fetchEntityModelById } from "@/Composables/entity/useEntityTableFetch";
import {
    ELEMENT_PRIMARY_LABELS,
    getElementGlassSurfaceStyle,
    getElementIcon,
    getElementPrimaries,
    resolveEntityElementValue,
} from "@/Utils/Entity/Elements";
import { spellTypeRuleNotes } from "@/Utils/Entity/spellTypeRuleNotes";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import { readSpellField } from "@/Utils/Entity/spellMinimalUsageDisplay";
import {
    resolveSpellUsageCharacteristicVisual,
    spellUsageIconBackdropStyle,
    spellUsageTextColorStyle,
} from "@/Utils/Entity/spellUsageCharacteristicVisual";

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("spells", "viewAny"),
        createAny: permissions.can("spells", "createAny"),
        updateAny: permissions.can("spells", "updateAny"),
        deleteAny: permissions.can("spells", "deleteAny"),
        manageAny: permissions.can("spells", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const hydratedSpell = ref(null);
const degreesLoading = ref(false);
let hydrateToken = 0;

const entity = computed(() => hydratedSpell.value || props.spell);

watch(
    () => props.spell,
    async (incoming) => {
        const token = ++hydrateToken;
        hydratedSpell.value = null;
        const id = incoming?.id ?? incoming?._data?.id;
        if (!id || spellHasDegreePayload(incoming)) {
            degreesLoading.value = false;
            return;
        }
        degreesLoading.value = true;
        try {
            const full = await fetchEntityModelById("spells", id);
            if (token !== hydrateToken) return;
            if (full && spellHasDegreePayload(full)) {
                hydratedSpell.value = full;
            }
        } catch {
            /* payload allégé en repli */
        } finally {
            if (token === hydrateToken) {
                degreesLoading.value = false;
            }
        }
    },
    { immediate: true },
);

const { degrees: displayDegrees, hasDegrees } = useSpellDisplayDegrees(entity);
const activeDegreeIndex = ref(0);

watch(
    displayDegrees,
    (list) => {
        if (!list.length) {
            activeDegreeIndex.value = 0;
            return;
        }
        if (activeDegreeIndex.value >= list.length) {
            activeDegreeIndex.value = 0;
        }
    },
    { immediate: true },
);

const activeDegree = computed(() => displayDegrees.value[activeDegreeIndex.value] || null);

const usageEntity = computed(() => {
    const base = entity.value;
    const propsDeg = activeDegree.value?.properties;
    if (!base || !propsDeg) {
        return base;
    }
    const merged = { ...(base._data || {}) };
    for (const [k, v] of Object.entries(propsDeg)) {
        if (v !== null && v !== undefined) {
            merged[k] = v;
        }
    }
    const Ctor = base.constructor;
    if (typeof Ctor === "function" && Ctor !== Object) {
        try {
            return new Ctor(merged);
        } catch {
            /* fallback */
        }
    }
    return { ...base, _data: merged, ...merged };
});

const spellDegreesPayload = computed(() => readSpellDegreesPayload(entity.value));
const effectsDefinitions = computed(() =>
    Array.isArray(entity.value?.effectsDefinitions) ? entity.value.effectsDefinitions : [],
);
const freeEffectComment = computed(() => {
    const t = entity.value?.effect != null ? String(entity.value.effect).trim() : "";
    return t;
});

const ruleNotes = computed(() => spellTypeRuleNotes(entity.value));

const activeDegreeHasRows = computed(() => {
    const rows = activeDegree.value?.rows;
    return Array.isArray(rows) && rows.length > 0;
});

const cellOpts = () => ({ size: "xs", context: "minimal" });

const spellTypesCell = computed(() => entity.value?.toCell?.("spell_types", cellOpts()) ?? null);
const showSpellTypesCell = computed(() => spellTypesCellHasRenderableContent(spellTypesCell.value));

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? "",
);

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const stateTick = ref(0);
const entityState = computed(() => {
    void stateTick.value;
    return entity.value?.state ?? entity.value?._data?.state ?? null;
});

const elementRaw = computed(() => resolveEntityElementValue(entity.value));
const elementOverlay = computed(() => {
    const primaries = getElementPrimaries(elementRaw.value);
    if (!primaries.length) return null;
    const first = primaries[0];
    if (first === 0 && primaries.length === 1) return null;
    return {
        icon: getElementIcon(first),
        label: ELEMENT_PRIMARY_LABELS[first] ?? "",
    };
});

const isMagicRaw = computed(() => readSpellField(entity.value, "isMagic", "is_magic"));
const isMagicDefined = computed(
    () => entity.value != null && isMagicRaw.value !== null && isMagicRaw.value !== undefined,
);
const isMagicOn = computed(() => Boolean(isMagicRaw.value));
const magicUsageVisual = computed(() =>
    isMagicDefined.value
        ? resolveSpellUsageCharacteristicVisual("is_magic", isMagicOn.value)
        : resolveSpellUsageCharacteristicVisual("is_magic"),
);
const magicKindLabel = computed(() => (isMagicOn.value ? "Wakfu" : "Physique"));
const magicAffixMuted = computed(
    () =>
        isMagicDefined.value &&
        !isMagicOn.value &&
        magicUsageVisual.value.hasIcon &&
        !magicUsageVisual.value.hasDistinctFalseIcon,
);
const magicStatusLabel = computed(() =>
    !isMagicDefined.value
        ? ""
        : isMagicOn.value
          ? "Sort Wakfu : le sort puise dans le Wakfu du lanceur ou de la lanceuse (équivalent du magique côté Dofus)."
          : "Sort physique : le sort puise dans la force physique.",
);

function tooltipAccentFromVisual(visual) {
    const c = visual?.color;
    return typeof c === "string" && c.startsWith("#") ? { "--color": c } : {};
}

const showHoverIdentity = computed(
    () =>
        (canShowField("spell_types") && showSpellTypesCell.value) ||
        canShowField("category") ||
        (canShowField("is_magic") && isMagicDefined.value),
);

const showHoverExtras = computed(
    () =>
        showHoverIdentity.value ||
        Boolean(descriptionFull.value) ||
        ruleNotes.value.length > 0 ||
        Boolean(freeEffectComment.value),
);

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "spells",
    showRoute: "entities.spells.show",
    editRoute: "entities.spells.edit",
    routeParam: "spell",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    if (actionKey === "state") {
        stateTick.value += 1;
        emit("action", actionKey, props.spell);
        return;
    }
    await handleMinimalAction(actionKey);
};

const elementSurfaceStyle = computed(() =>
    getElementGlassSurfaceStyle(resolveEntityElementValue(entity.value)),
);

const USAGE_IMG_PX = "14";
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="spells"
        :pinned-entity-id="entity?.id"
        :surface-style="elementSurfaceStyle"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative flex flex-col gap-1.5 p-2 transition-colors"
            >
                <div class="flex min-w-0 gap-2">
                    <div class="flex w-14 shrink-0 flex-col items-stretch gap-1">
                        <div class="relative shrink-0">
                            <EntityThumb
                                size="compact"
                                :src="imageUrl || ''"
                                :label="entity?.name ?? 'Sort'"
                            />
                            <span
                                v-if="elementOverlay"
                                class="pointer-events-none absolute -left-0.5 -top-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-base-100/90 shadow-sm ring-1 ring-base-content/15"
                                data-cy="spell-minimal-element-overlay"
                            >
                                <Icon
                                    :source="elementOverlay.icon"
                                    :alt="elementOverlay.label"
                                    size="xs"
                                    class="shrink-0"
                                />
                            </span>
                        </div>
                        <SpellDegreeTabs
                            v-if="hasDegrees"
                            v-model="activeDegreeIndex"
                            :degrees="displayDegrees"
                            size="xs"
                            appearance="stack"
                            collapse-inactive
                        />
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col gap-1 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <div
                                v-if="showActions || entityState"
                                data-entity-actions
                                class="ml-auto flex min-w-8 flex-1 items-center justify-end gap-1"
                                @click.stop
                            >
                                <EntityStateBadge
                                    v-if="entityState"
                                    :state="entityState"
                                    size="xs"
                                    :show-label="false"
                                    class="shrink-0"
                                />
                                <EntityActions
                                    v-if="showActions"
                                    entity-type="spells"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    placement="bottom-end"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>

                        <SpellMinimalDegreeFacts
                            :entity="usageEntity"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            :can-show-field="canShowField"
                            :area="activeDegree?.area || ''"
                            property-size="xs"
                        />
                    </div>
                </div>

                <!-- Contracté : résolution + effets (identité / description seulement dans #expanded) -->
                <div class="mt-1 w-full min-w-0 space-y-1 border-t border-base-300 pt-1.5">
                    <SpellResolutionFormula
                        :entity="usageEntity"
                        variant="compact"
                        class="mb-1 text-xs text-base-content/75"
                    />
                    <SpellEffectsJournal
                        v-if="activeDegreeHasRows"
                        v-model="activeDegreeIndex"
                        hide-tabs
                        flush
                        :spell-degrees="spellDegreesPayload"
                        :definitions="effectsDefinitions"
                        sub-effect-layout="minimal"
                    />
                    <p
                        v-else-if="degreesLoading"
                        class="text-xs text-base-content/55"
                        data-cy="spell-effects-loading"
                    >
                        Chargement des effets…
                    </p>
                    <SpellUsageBlock
                        v-else
                        parts="effects"
                        :entity="usageEntity"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        :can-show-field="canShowField"
                        :max-effect-rows="3"
                        :show-empty-effects="true"
                        :show-rule-notes="false"
                        flush-effects
                    />
                </div>
            </div>
        </template>

        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative flex flex-col gap-1.5 p-2 transition-colors"
            >
                <div class="flex min-w-0 gap-2">
                    <div class="flex w-14 shrink-0 flex-col items-stretch gap-1">
                        <div class="relative shrink-0">
                            <EntityThumb
                                size="compact"
                                :src="imageUrl || ''"
                                :label="entity?.name ?? 'Sort'"
                            />
                            <span
                                v-if="elementOverlay"
                                class="pointer-events-none absolute -left-0.5 -top-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-base-100/90 shadow-sm ring-1 ring-base-content/15"
                                data-cy="spell-minimal-element-overlay"
                            >
                                <Icon
                                    :source="elementOverlay.icon"
                                    :alt="elementOverlay.label"
                                    size="xs"
                                    class="shrink-0"
                                />
                            </span>
                        </div>
                        <SpellDegreeTabs
                            v-if="hasDegrees"
                            v-model="activeDegreeIndex"
                            :degrees="displayDegrees"
                            size="xs"
                            appearance="stack"
                        />
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col gap-1 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <div
                                v-if="showActions || entityState"
                                data-entity-actions
                                class="ml-auto flex min-w-8 flex-1 items-center justify-end gap-1"
                                @click.stop
                            >
                                <EntityStateBadge
                                    v-if="entityState"
                                    :state="entityState"
                                    size="xs"
                                    :show-label="false"
                                    class="shrink-0"
                                />
                                <EntityActions
                                    v-if="showActions"
                                    entity-type="spells"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    placement="bottom-end"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>

                        <SpellMinimalDegreeFacts
                            :entity="usageEntity"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            :can-show-field="canShowField"
                            :area="activeDegree?.area || ''"
                            property-size="xs"
                        />

                        <!-- Extended / overlay : identité toujours ouverte -->
                        <div
                            v-if="showHoverExtras"
                            class="space-y-1"
                            data-cy="spell-minimal-hover-identity"
                        >
                            <div
                                v-if="showHoverIdentity"
                                class="inline-flex max-w-full flex-wrap items-center gap-1.5"
                            >
                                <EntityPropertyDisplay
                                    v-if="canShowField('spell_types') && showSpellTypesCell"
                                    field-key="spell_types"
                                    presentation="spell-types-icons-only"
                                    :entity="entity"
                                    entity-type="spell"
                                    :display-mode="PROPERTY_DISPLAY_MODES.minimal"
                                    :descriptors="descriptors"
                                    :table-meta="tableMeta"
                                    size="xs"
                                    class="min-w-0"
                                />
                                <EntityPropertyDisplay
                                    v-if="canShowField('category')"
                                    field-key="category"
                                    :entity="entity"
                                    entity-type="spell"
                                    :display-mode="PROPERTY_DISPLAY_MODES.minimal"
                                    variant="inline"
                                    hide-field-label
                                    :descriptors="descriptors"
                                    :table-meta="tableMeta"
                                    size="xs"
                                    class="min-w-0"
                                />
                                <Tooltip
                                    v-if="canShowField('is_magic') && isMagicDefined"
                                    placement="top"
                                    color="neutral"
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
                                            class="text-xs text-base-content/80"
                                            :style="spellUsageTextColorStyle(magicUsageVisual.color)"
                                        >{{ magicKindLabel }}</span>
                                    </span>
                                </Tooltip>
                            </div>
                            <p
                                v-if="descriptionFull"
                                class="text-xs leading-snug text-base-content/80 line-clamp-3"
                                :title="descriptionFull"
                                data-cy="spell-minimal-description"
                            >
                                {{ descriptionFull }}
                            </p>
                            <div
                                v-if="ruleNotes.length"
                                data-cy="spell-rule-notes"
                                class="spell-rule-notes space-y-0.5"
                            >
                                <p
                                    v-for="note in ruleNotes"
                                    :key="note"
                                    class="text-[0.7rem] leading-snug text-base-content/70"
                                >
                                    {{ note }}
                                </p>
                            </div>
                            <p
                                v-if="freeEffectComment"
                                class="text-xs leading-snug whitespace-pre-wrap text-base-content/70"
                                data-cy="spell-free-effect-comment"
                            >
                                {{ freeEffectComment }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-1 w-full min-w-0 space-y-1 border-t border-base-300 pt-1.5">
                    <SpellResolutionFormula
                        :entity="usageEntity"
                        variant="compact"
                        class="mb-1 text-xs text-base-content/75"
                    />
                    <SpellEffectsJournal
                        v-if="activeDegreeHasRows"
                        v-model="activeDegreeIndex"
                        hide-tabs
                        flush
                        :spell-degrees="spellDegreesPayload"
                        :definitions="effectsDefinitions"
                        sub-effect-layout="minimal"
                    />
                    <p
                        v-else-if="degreesLoading"
                        class="text-xs text-base-content/55"
                        data-cy="spell-effects-loading"
                    >
                        Chargement des effets…
                    </p>
                    <SpellUsageBlock
                        v-else
                        parts="effects"
                        :entity="usageEntity"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        :can-show-field="canShowField"
                        :max-effect-rows="3"
                        :show-empty-effects="true"
                        :show-rule-notes="false"
                        flush-effects
                    />
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
