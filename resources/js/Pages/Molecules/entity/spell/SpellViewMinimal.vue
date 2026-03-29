<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 *
 * @description
 * Effets : `resolveSpellEffectsDisplayCell` (résumé API + SpellEffectChips, ou fallback `effect`).
 *
 * @props {Spell} spell - Instance du modèle Spell
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { getEntityCharacteristicsByDbColumn, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";
import { getCharacteristicColorStyle, isPoCac, PO_CAC_ICON, PO_CAC_TOOLTIP } from "@/Composables/entity/useCharacteristicDisplay";
import { getByCharacteristicKey } from "@/Composables/store/useCharacteristicsStore";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import SpellSummonMonsterInline from "@/Pages/Molecules/entity/spell/SpellSummonMonsterInline.vue";
import { Spell } from "@/Models/Entity/Spell";

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
        default: "extended",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["edit", "view", "delete", "action"]);

const entity = computed(() => props.spell);

const cellOpts = () => ({ size: "xs", context: "minimal" });

const elementCell = computed(() => entity.value?.toCell?.("element", cellOpts()) ?? null);
const categoryCell = computed(() => entity.value?.toCell?.("category", cellOpts()) ?? null);
const spellTypesCell = computed(() => entity.value?.toCell?.("spell_types", cellOpts()) ?? null);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const paValue = computed(() => entity.value?.pa ?? entity.value?._data?.pa ?? null);

/** Portée affichée « min - max » (aligné modèle Spell / API). */
const poRangeLabel = computed(() => {
    const min = entity.value?.poMin ?? entity.value?._data?.po_min;
    const max = entity.value?.poMax ?? entity.value?._data?.po_max;
    const hasMin = min != null && String(min).trim() !== "";
    const hasMax = max != null && String(max).trim() !== "";
    if (!hasMin && !hasMax) {
        const fallback = entity.value?.po ?? entity.value?._data?.po ?? "";
        return fallback !== "" ? String(fallback) : "";
    }
    const a = hasMin ? String(min).trim() : "";
    const b = hasMax ? String(max).trim() : a;
    if (a === b) {
        return a;
    }
    return `${a} - ${b}`;
});

const poIsCac = computed(() => isPoCac(poRangeLabel.value));

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectDisplayCell = computed(() =>
    resolveSpellEffectsDisplayCell(entity.value, {
        size: "xs",
        context: "minimal",
        ctx: props.tableMeta,
        maxEffectRows: 3,
    }),
);
const hasEffects = computed(() => spellEffectsCellHasContent(effectDisplayCell.value));

/** Monstres invoqués (instance Spell ou objet brut table / Inertia avec `effects_definitions`). */
const summonMonsterBriefs = computed(() => {
    const e = entity.value;
    const raw =
        e instanceof Spell
            ? e.effectsDefinitions
            : e?.effects_definitions ?? e?._data?.effects_definitions;
    return Spell.summonMonstersFromEffectsDefinitionsPayload(raw);
});

const byDbColumn = computed(() => getEntityCharacteristicsByDbColumn(props.tableMeta, "spell"));
const paMeta = computed(() => byDbColumn.value?.pa || null);
const rangeSpellMeta = computed(() => getByCharacteristicKey("spell", "range_spell") || null);

const descriptorsMinimal = computed(() => getSpellFieldDescriptors({ meta: {} }));

function fieldUiTooltip(fieldKey) {
    return resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptorsMinimal.value,
        tableMeta: props.tableMeta,
        entityType: "spell",
    }).tooltip;
}

const paTooltip = computed(() => {
    const h = paMeta.value?.helper || paMeta.value?.descriptions?.join?.(" ") || "";
    return [h || "Coût en points d’action.", paValue.value != null && `Valeur : ${paValue.value}`]
        .filter(Boolean)
        .join("\n\n");
});

const poTooltip = computed(() => {
    const m = rangeSpellMeta.value;
    const h = m?.helper || (Array.isArray(m?.descriptions) ? m.descriptions.join(" ") : m?.descriptions) || "";
    const base = h || "Portée minimale et maximale en cases.";
    if (poIsCac.value) {
        return [base, PO_CAC_TOOLTIP].filter(Boolean).join("\n\n");
    }
    return [base, poRangeLabel.value && `Affichage : ${poRangeLabel.value}`].filter(Boolean).join("\n\n");
});

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.spells.show", { spell: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const spellId = entity.value?.id;
    if (!spellId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.spells.show", { spell: spellId }));
            emit("view", props.spell);
            break;
        case "edit":
            router.visit(route("entities.spells.edit", { spell: spellId }));
            emit("edit", props.spell);
            break;
        case "delete":
            emit("delete", props.spell);
            break;
        default:
            emit("action", actionKey, props.spell);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode">
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="absolute top-1.5 left-1.5 z-10">
                    <EntityUsableDot :state="stateValue" />
                </div>
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <img
                            v-if="imageUrl"
                            :src="imageUrl"
                            :alt="entity?.name ?? 'Sort'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon
                            v-else
                            source="fa-solid fa-wand-magic-sparkles"
                            alt=""
                            size="xs"
                            class="text-base-content/40"
                        />
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <Route
                                    v-if="showHref"
                                    :href="showHref"
                                    color="neutral"
                                    class="font-semibold truncate block text-sm text-base-content hover:text-base-content no-underline"
                                >
                                    {{ entity?.name ?? "—" }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ entity?.name ?? "—" }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="spells"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <Tooltip
                                v-if="elementCell?.value && elementCell.value !== '—'"
                                :content="fieldUiTooltip('element')"
                                placement="top"
                            >
                                <span class="inline-flex items-center">
                                    <CellRenderer :cell="elementCell" class="inline-flex items-center" />
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="categoryCell?.value && categoryCell.value !== '-' && categoryCell.value !== '—'"
                                :content="fieldUiTooltip('category')"
                                placement="top"
                            >
                                <span class="inline-flex items-center">
                                    <CellRenderer :cell="categoryCell" class="inline-flex items-center" />
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="spellTypesCell?.value && spellTypesCell.value !== '-' && spellTypesCell.value !== '—'"
                                :content="fieldUiTooltip('spell_types')"
                                placement="top"
                            >
                                <span class="inline-flex items-center">
                                    <CellRenderer :cell="spellTypesCell" class="inline-flex items-center" />
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="paValue != null && paValue !== ''"
                                :content="paTooltip"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="paMeta?.icon || 'fa-solid fa-bolt'"
                                        alt="PA"
                                        size="xs"
                                        :style="paMeta?.color ? getCharacteristicColorStyle(paMeta.color) : undefined"
                                    />
                                    <span>{{ paValue }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip v-if="poRangeLabel !== ''" :content="poTooltip" placement="top">
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="poIsCac ? PO_CAC_ICON : (rangeSpellMeta?.icon || 'fa-solid fa-crosshairs')"
                                        alt="Portée"
                                        size="xs"
                                        :style="
                                            rangeSpellMeta?.color
                                                ? getCharacteristicColorStyle(rangeSpellMeta.color)
                                                : undefined
                                        "
                                    />
                                    <span v-if="!poIsCac">{{ poRangeLabel }}</span>
                                </span>
                            </Tooltip>
                        </div>
                    </div>
                </div>
                <div
                    v-if="hasEffects"
                    class="spell-effects-minimal w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CellRenderer
                        :cell="effectDisplayCell"
                        ui-color="primary"
                        class="text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
                <div
                    v-if="summonMonsterBriefs.length > 0"
                    class="spell-summon-monsters w-full pt-1.5 mt-1 border-t border-base-300 flex flex-col gap-1.5"
                >
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-base-content/55">
                        Invocation{{ summonMonsterBriefs.length > 1 ? "s" : "" }}
                    </span>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                        <SpellSummonMonsterInline
                            v-for="m in summonMonsterBriefs"
                            :key="m.id"
                            :monster-brief="m"
                        />
                    </div>
                </div>
            </div>
        </template>
        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="absolute top-1.5 left-1.5 z-10">
                    <EntityUsableDot :state="stateValue" />
                </div>
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <img
                            v-if="imageUrl"
                            :src="imageUrl"
                            :alt="entity?.name ?? 'Sort'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon
                            v-else
                            source="fa-solid fa-wand-magic-sparkles"
                            alt=""
                            size="xs"
                            class="text-base-content/40"
                        />
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <Route
                                    v-if="showHref"
                                    :href="showHref"
                                    color="neutral"
                                    class="font-semibold truncate block text-sm text-base-content hover:text-base-content no-underline"
                                >
                                    {{ entity?.name ?? "—" }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ entity?.name ?? "—" }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="spells"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <Tooltip
                                v-if="elementCell?.value && elementCell.value !== '—'"
                                :content="fieldUiTooltip('element')"
                                placement="top"
                            >
                                <span class="inline-flex items-center">
                                    <CellRenderer :cell="elementCell" class="inline-flex items-center" />
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="categoryCell?.value && categoryCell.value !== '-' && categoryCell.value !== '—'"
                                :content="fieldUiTooltip('category')"
                                placement="top"
                            >
                                <span class="inline-flex items-center">
                                    <CellRenderer :cell="categoryCell" class="inline-flex items-center" />
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="spellTypesCell?.value && spellTypesCell.value !== '-' && spellTypesCell.value !== '—'"
                                :content="fieldUiTooltip('spell_types')"
                                placement="top"
                            >
                                <span class="inline-flex items-center">
                                    <CellRenderer :cell="spellTypesCell" class="inline-flex items-center" />
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="paValue != null && paValue !== ''"
                                :content="paTooltip"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="paMeta?.icon || 'fa-solid fa-bolt'"
                                        alt="PA"
                                        size="xs"
                                        :style="paMeta?.color ? getCharacteristicColorStyle(paMeta.color) : undefined"
                                    />
                                    <span>{{ paValue }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip v-if="poRangeLabel !== ''" :content="poTooltip" placement="top">
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="poIsCac ? PO_CAC_ICON : (rangeSpellMeta?.icon || 'fa-solid fa-crosshairs')"
                                        alt="Portée"
                                        size="xs"
                                        :style="
                                            rangeSpellMeta?.color
                                                ? getCharacteristicColorStyle(rangeSpellMeta.color)
                                                : undefined
                                        "
                                    />
                                    <span v-if="!poIsCac">{{ poRangeLabel }}</span>
                                </span>
                            </Tooltip>
                        </div>
                        <p
                            v-if="descriptionFull"
                            class="text-xs text-base-content/80 line-clamp-3"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
                <div
                    v-if="hasEffects"
                    class="spell-effects-minimal w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CellRenderer
                        :cell="effectDisplayCell"
                        ui-color="primary"
                        class="text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
                <div
                    v-if="summonMonsterBriefs.length > 0"
                    class="spell-summon-monsters w-full pt-1.5 mt-1 border-t border-base-300 flex flex-col gap-1.5"
                >
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-base-content/55">
                        Invocation{{ summonMonsterBriefs.length > 1 ? "s" : "" }}
                    </span>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                        <SpellSummonMonsterInline
                            v-for="m in summonMonsterBriefs"
                            :key="m.id"
                            :monster-brief="m"
                        />
                    </div>
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
