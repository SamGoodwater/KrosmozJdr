<script setup>
/**
 * MonsterViewMinimal — Vue Minimal pour Monster
 *
 * @description
 * Identité et méta toujours visibles ; description discrète au survol (modes compact / hover) ;
 * résumés combat / stats en blocs compacts ; en état étendu : caractéristiques complètes,
 * compétences (maîtrises, y compris à 0) groupées par stat, sorts de la créature.
 * Pas de bloc « contenus liés » / ressources en Minimal.
 *
 * @props {Monster} monster - Instance du modèle Monster
 */
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterBossMark from "@/Pages/Molecules/entity/monster/MonsterBossMark.vue";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import { cellHasRenderableContent, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";

const props = defineProps({
    monster: {
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
    characteristicRuntime: { type: Object, default: null },
});

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "monsters",
    showRoute: "entities.monsters.show",
    editRoute: "entities.monsters.edit",
    routeParam: "monster",
    emit,
    getEntity: () => props.monster,
});

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("monsters", "viewAny"),
        createAny: permissions.can("monsters", "createAny"),
        updateAny: permissions.can("monsters", "updateAny"),
        deleteAny: permissions.can("monsters", "deleteAny"),
        manageAny: permissions.can("monsters", "manageAny"),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getMonsterFieldDescriptors(ctx.value));

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

const entity = computed(() => props.monster);

const creatureData = computed(() => entity.value?.creature ?? entity.value?._data?.creature ?? null);

const creatureIdForStats = computed(
    () => creatureData.value?.id ?? entity.value?.creature_id ?? entity.value?._data?.creature_id ?? null,
);

const { runtime: fetchedRuntime } = useCreatureResolvedStats(creatureIdForStats);

const effectiveRuntime = computed(
    () => props.characteristicRuntime ?? fetchedRuntime.value ?? null,
);

provideCharacteristicRuntime(effectiveRuntime);

const summaryCharacteristicGroups = computed(() =>
    buildCreatureCharacteristicGroups(creatureData.value, {
        mode: "summary",
        runtime: effectiveRuntime.value,
    }),
);

const fullCharacteristicGroups = computed(() =>
    buildCreatureCharacteristicGroups(creatureData.value, {
        mode: "full",
        runtime: effectiveRuntime.value,
    }),
);

const levelValue = computed(() => {
    const lv = creatureData.value?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const imageUrl = computed(() => {
    const u = creatureData.value?.image;
    return u && String(u).trim() ? String(u) : null;
});

const creatureName = computed(() => creatureData.value?.name ?? "—");

const isBoss = computed(() => Boolean(entity.value?.isBoss ?? entity.value?._data?.is_boss));

const bossFieldTooltip = computed(() =>
    resolveEntityFieldUi({
        fieldKey: "is_boss",
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "monster",
    }).tooltip,
);

const descriptionFull = computed(() => {
    const d = creatureData.value?.description;
    return d && String(d).trim() ? String(d) : "";
});

const cellOpts = () => ({ size: "xs", context: "minimal", ctx: props.tableMeta });

function getSummaryCell(fieldKey) {
    const m = entity.value;
    if (m && typeof m.toCell === "function") {
        return m.toCell(fieldKey, cellOpts());
    }
    return { type: "text", value: "—", params: {} };
}

/** Maîtrises regroupées par caractéristique primaire (visibles en Minimal étendu, y compris palier 0). */
const competenceGroups = computed(() =>
    buildCreatureCompetenceGroupsByPrimary(creatureData.value, {
        includeZero: true,
        runtime: effectiveRuntime.value,
    }),
);

const cardEntityForCompetences = computed(() =>
    creatureData.value ? { level: creatureData.value.level } : null,
);

const raceCell = computed(() => getSummaryCell("monster_race"));
const sizeCell = computed(() => getSummaryCell("size"));
/** Les chips (taille) ont `value` vide : ne pas tester seulement `cell.value`. */
const showSizeCell = computed(() => cellHasRenderableContent(sizeCell.value));
const hostilityCell = computed(() => getSummaryCell("creature_hostility"));
const bossPaCell = computed(() => getSummaryCell("boss_pa"));

const linkedLanguages = computed(() => {
    const raw = entity.value?._data?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw = creatureData.value?.creatureTraits ?? [];
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const showDescriptionInCompactSlot = computed(() => props.displayMode === "compact");

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="monsters"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="creatureName || 'Monstre'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <MonsterBossMark
                                v-if="isBoss && canShowField('is_boss')"
                                :tooltip="bossFieldTooltip"
                                size-class="h-5 w-5"
                                class="shrink-0"
                            />
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="creatureName" @open="openQuickView" />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="raceCell?.value && raceCell.value !== '-' && raceCell.value !== '—'"
                                :cell="raceCell"
                                class="inline-flex text-xs"
                            />
                            <CellRenderer
                                v-if="showSizeCell"
                                :cell="sizeCell"
                                class="inline-flex text-[11px] text-base-content/80"
                            />
                            <CellRenderer
                                v-if="canShowField('creature_hostility') && hostilityCell?.value !== '—'"
                                :cell="hostilityCell"
                                class="inline-flex text-[11px] text-base-content/85"
                            />
                            <CellRenderer
                                v-if="isBoss && canShowField('boss_pa') && bossPaCell?.value !== '—'"
                                :cell="bossPaCell"
                                class="inline-flex text-[11px]"
                            />
                        </div>
                        <div
                            v-if="summaryCharacteristicGroups.length"
                            class="w-full border-t border-primary/20 bg-primary/5 pt-1.5"
                        >
                            <CharacteristicsCard
                                :entity="cardEntityForCompetences"
                                :groups="summaryCharacteristicGroups"
                                :runtime="effectiveRuntime"
                                :density="CHARACTERISTIC_CARD_DENSITY.icon"
                                class="border-0 bg-transparent p-0 shadow-none ring-0"
                            />
                        </div>
                        <p
                            v-if="showDescriptionInCompactSlot && descriptionFull"
                            class="text-[11px] leading-snug italic text-base-content/45 max-h-0 opacity-0 overflow-hidden transition-all duration-200 ease-out group-hover:max-h-32 group-hover:opacity-100 group-hover:mt-0.5"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                        <div
                            v-if="hasLinkedLanguages"
                            class="flex flex-wrap gap-1"
                            role="region"
                            aria-label="Langues"
                        >
                            <LanguageViewMinimal
                                v-for="lang in linkedLanguages"
                                :key="lang.id"
                                :language="lang"
                                class="min-w-0 max-w-[11rem]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative flex flex-col gap-1.5 p-2 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="creatureName || 'Monstre'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <MonsterBossMark
                                v-if="isBoss && canShowField('is_boss')"
                                :tooltip="bossFieldTooltip"
                                size-class="h-5 w-5"
                                class="shrink-0"
                            />
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="creatureName" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="monsters"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="raceCell?.value && raceCell.value !== '-' && raceCell.value !== '—'"
                                :cell="raceCell"
                                class="inline-flex text-xs"
                            />
                            <CellRenderer
                                v-if="showSizeCell"
                                :cell="sizeCell"
                                class="inline-flex text-[11px] text-base-content/80"
                            />
                            <CellRenderer
                                v-if="canShowField('creature_hostility') && hostilityCell?.value !== '—'"
                                :cell="hostilityCell"
                                class="inline-flex text-[11px] text-base-content/85"
                            />
                            <CellRenderer
                                v-if="isBoss && canShowField('boss_pa') && bossPaCell?.value !== '—'"
                                :cell="bossPaCell"
                                class="inline-flex text-[11px]"
                            />
                        </div>
                        <p
                            v-if="descriptionFull"
                            :class="
                                displayMode === 'extended'
                                    ? 'text-[11px] leading-snug italic text-base-content/55 mt-0.5'
                                    : 'text-[11px] leading-snug italic text-base-content/45 max-h-0 overflow-hidden opacity-0 transition-all duration-200 ease-out group-hover:max-h-40 group-hover:opacity-100'
                            "
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="hasLinkedCreatureTraits"
                    class="w-full border-t border-base-300/80 pt-1.5"
                    role="region"
                    aria-label="Traits"
                >
                    <CreatureTraitBadges :traits="linkedCreatureTraits" size="xs" />
                </div>

                <div
                    v-if="hasLinkedLanguages"
                    class="w-full border-t border-base-300/80 pt-1.5"
                    role="region"
                    aria-label="Langues"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-300/90">
                        Langues
                    </p>
                    <div class="flex flex-wrap gap-1">
                        <LanguageViewMinimal
                            v-for="lang in linkedLanguages"
                            :key="lang.id"
                            :language="lang"
                            class="min-w-0 max-w-[11rem]"
                        />
                    </div>
                </div>

                <div
                    v-if="creatureData && fullCharacteristicGroups.length"
                    class="minimal-monster-full-chars w-full border-t border-primary/20 bg-primary/5 pt-1"
                >
                    <CharacteristicsCard
                        :entity="cardEntityForCompetences"
                        :groups="fullCharacteristicGroups"
                        :runtime="effectiveRuntime"
                        :density="CHARACTERISTIC_CARD_DENSITY.icon"
                        class="border-0 bg-transparent p-0 shadow-none ring-0"
                    />
                </div>

                <!-- Compétences : totaux + noms + (M|E), densifié pour Minimal étendu -->
                <div
                    v-if="creatureData && competenceGroups.length"
                    class="minimal-monster-competences mt-1 max-h-[min(85vh,42rem)] overflow-y-auto overscroll-contain border-t border-base-300/80 pt-1"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-300/90 leading-none">
                        Compétences
                    </p>
                    <CharacteristicsCard
                        :entity="cardEntityForCompetences"
                        :groups="competenceGroups"
                        :runtime="effectiveRuntime"
                        :density="CHARACTERISTIC_CARD_DENSITY.icon"
                        class="minimal-monster-competences-card border-0 bg-transparent p-0 shadow-none ring-0"
                    />
                </div>

                <!-- Sorts liés à la créature (texte + aperçu minimal au survol de chaque sort) -->
                <MonsterCreatureSpellsList
                    v-if="creatureData"
                    :creature="creatureData"
                    :table-meta="tableMeta"
                    :characteristic-runtime="characteristicRuntime"
                    section-class="mt-1.5 border-t border-base-300/80 pt-1.5"
                />
            </div>
        </template>
    </EntityMinimalCard>
</template>

<style scoped>
.minimal-monster-full-chars :deep(.characteristics-card),
.minimal-monster-competences-card.characteristics-card,
.minimal-monster-competences-card :deep(.characteristics-card) {
    padding: 0;
}
.minimal-monster-full-chars :deep(.characteristic-group),
.minimal-monster-competences-card :deep(.characteristic-group) {
    margin-bottom: 0.15rem;
}
.minimal-monster-full-chars :deep(.characteristic-group:last-child),
.minimal-monster-competences-card :deep(.characteristic-group:last-child) {
    margin-bottom: 0;
}
.minimal-monster-full-chars :deep(.characteristic-group h4),
.minimal-monster-competences-card :deep(.characteristic-group h4) {
    margin-bottom: 0.1rem;
    font-size: 0.6rem;
    line-height: 1.1;
    letter-spacing: 0.04em;
}
.minimal-monster-full-chars :deep(.characteristic-group__items),
.minimal-monster-competences-card :deep(.characteristic-group__items) {
    gap: 0.15rem 0.35rem;
}
.minimal-monster-competences-card :deep(.characteristic-property) {
    font-size: 0.7rem;
    line-height: 1.15;
    gap: 0.2rem;
}
</style>
