<script setup>
/**
 * NpcViewMinimal — Vue Minimal pour NPC
 *
 * @description
 * Identité et méta toujours visibles (classe, rôle, lieu, taille, hostilité) ;
 * description discrète au survol (modes compact / hover) ;
 * en état étendu : caractéristiques complètes, compétences, sorts et équipements.
 * Pas de panneau DofusDB.
 *
 * @props {Npc} npc
 */
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterCreatureItemsList from "@/Pages/Molecules/entity/monster/MonsterCreatureItemsList.vue";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import { cellHasRenderableContent } from "@/Utils/Entity/entity-view-ui";
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";

const props = defineProps({
    npc: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "extended",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
    characteristicRuntime: { type: Object, default: null },
});

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "npcs",
    showRoute: "entities.npcs.show",
    editRoute: "entities.npcs.edit",
    routeParam: "npc",
    emit,
    getEntity: () => props.npc,
});

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("npcs", "viewAny") || permissions.can("npc", "viewAny"),
        createAny: permissions.can("npcs", "createAny") || permissions.can("npc", "createAny"),
        updateAny: permissions.can("npcs", "updateAny") || permissions.can("npc", "updateAny"),
        deleteAny: permissions.can("npcs", "deleteAny") || permissions.can("npc", "deleteAny"),
        manageAny: permissions.can("npcs", "manageAny") || permissions.can("npc", "manageAny"),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getNpcFieldDescriptors(ctx.value));

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

const entity = computed(() => props.npc);

const creatureData = computed(
    () => entity.value?.creature ?? entity.value?._data?.creature ?? null,
);

const creatureIdForStats = computed(
    () =>
        creatureData.value?.id ??
        entity.value?.creature_id ??
        entity.value?._data?.creature_id ??
        null,
);

const { runtime: fetchedRuntime } = useCreatureResolvedStats(creatureIdForStats, "npc");

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

const competenceGroups = computed(() =>
    buildCreatureCompetenceGroupsByPrimary(creatureData.value, {
        includeZero: true,
        runtime: effectiveRuntime.value,
    }),
);

const cardEntityForCompetences = computed(() =>
    creatureData.value ? { level: creatureData.value.level } : null,
);

const levelValue = computed(() => {
    const lv = creatureData.value?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const imageUrl = computed(() => {
    const u = creatureData.value?.image ?? entity.value?.image;
    return u && String(u).trim() ? String(u) : null;
});

const displayName = computed(
    () => creatureData.value?.name ?? entity.value?.name ?? "PNJ",
);

const descriptionFull = computed(() => {
    const d = creatureData.value?.description;
    return d && String(d).trim() ? String(d) : "";
});

const cellOpts = () => ({ size: "xs", context: "minimal", ctx: props.tableMeta });

function getCell(fieldKey) {
    if (entity.value && typeof entity.value.toCell === "function") {
        return entity.value.toCell(fieldKey, cellOpts());
    }
    return { type: "text", value: "—", params: {} };
}

const breedCell = computed(() => getCell("breed"));
const specializationCell = computed(() => getCell("specialization"));
const roleCell = computed(() => getCell("npc_role"));
const locationCell = computed(() => getCell("creature_location"));
const sizeCell = computed(() => getCell("size"));
const hostilityCell = computed(() => getCell("creature_hostility"));

const showBreed = computed(
    () => canShowField("breed") && cellHasRenderableContent(breedCell.value),
);
const showSpecialization = computed(
    () =>
        canShowField("specialization") &&
        cellHasRenderableContent(specializationCell.value),
);
const showRole = computed(
    () => canShowField("npc_role") && cellHasRenderableContent(roleCell.value),
);
const showLocation = computed(
    () =>
        canShowField("creature_location") &&
        cellHasRenderableContent(locationCell.value),
);
const showSizeCell = computed(
    () => canShowField("size") && cellHasRenderableContent(sizeCell.value),
);
const showHostility = computed(
    () =>
        canShowField("creature_hostility") &&
        cellHasRenderableContent(hostilityCell.value),
);

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
        pinned-entity-type="npcs"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative flex flex-col gap-1.5 p-2 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb size="compact" :src="imageUrl || ''" :label="displayName" />
                    <div class="flex min-w-0 flex-1 flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge
                                v-if="levelValue != null"
                                :level="levelValue"
                                size="xs"
                                class="shrink-0"
                            />
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="showBreed"
                                :cell="breedCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showRole"
                                :cell="roleCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showLocation"
                                :cell="locationCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showSpecialization"
                                :cell="specializationCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showSizeCell"
                                :cell="sizeCell"
                                class="inline-flex text-[11px] text-base-content/80"
                            />
                            <CellRenderer
                                v-if="showHostility"
                                :cell="hostilityCell"
                                class="inline-flex text-[11px] text-base-content/85"
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
                            class="max-h-0 overflow-hidden text-[11px] leading-snug italic text-base-content/45 opacity-0 transition-all duration-200 ease-out group-hover:mt-0.5 group-hover:max-h-32 group-hover:opacity-100"
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
                    <EntityThumb size="compact" :src="imageUrl || ''" :label="displayName" />
                    <div class="flex min-w-0 flex-1 flex-col gap-1 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <LevelBadge
                                v-if="levelValue != null"
                                :level="levelValue"
                                size="xs"
                                class="shrink-0"
                            />
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                            </div>
                            <div
                                v-if="showActions"
                                data-entity-actions
                                class="ml-auto flex min-w-8 flex-1 justify-end"
                                @click.stop
                            >
                                <EntityActions
                                    entity-type="npcs"
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
                                v-if="showBreed"
                                :cell="breedCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showRole"
                                :cell="roleCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showLocation"
                                :cell="locationCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showSpecialization"
                                :cell="specializationCell"
                                class="inline-flex text-[11px]"
                            />
                            <CellRenderer
                                v-if="showSizeCell"
                                :cell="sizeCell"
                                class="inline-flex text-[11px] text-base-content/80"
                            />
                            <CellRenderer
                                v-if="showHostility"
                                :cell="hostilityCell"
                                class="inline-flex text-[11px] text-base-content/85"
                            />
                        </div>
                        <p
                            v-if="descriptionFull"
                            :class="
                                displayMode === 'extended'
                                    ? 'mt-0.5 text-[11px] leading-snug italic text-base-content/55'
                                    : 'max-h-0 overflow-hidden text-[11px] leading-snug italic text-base-content/45 opacity-0 transition-all duration-200 ease-out group-hover:max-h-40 group-hover:opacity-100'
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
                    class="minimal-npc-full-chars w-full border-t border-primary/20 bg-primary/5 pt-1"
                >
                    <CharacteristicsCard
                        :entity="cardEntityForCompetences"
                        :groups="fullCharacteristicGroups"
                        :runtime="effectiveRuntime"
                        :density="CHARACTERISTIC_CARD_DENSITY.icon"
                        class="border-0 bg-transparent p-0 shadow-none ring-0"
                    />
                </div>

                <div
                    v-if="creatureData && competenceGroups.length"
                    class="minimal-npc-competences mt-1 max-h-[min(85vh,42rem)] overflow-y-auto overscroll-contain border-t border-base-300/80 pt-1"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase leading-none tracking-wide text-primary-300/90">
                        Compétences
                    </p>
                    <CharacteristicsCard
                        :entity="cardEntityForCompetences"
                        :groups="competenceGroups"
                        :runtime="effectiveRuntime"
                        :density="CHARACTERISTIC_CARD_DENSITY.icon"
                        class="minimal-npc-competences-card border-0 bg-transparent p-0 shadow-none ring-0"
                    />
                </div>

                <MonsterCreatureSpellsList
                    v-if="creatureData"
                    :creature="creatureData"
                    :table-meta="tableMeta"
                    :characteristic-runtime="characteristicRuntime"
                    section-class="mt-1.5 border-t border-base-300/80 pt-1.5"
                />
                <MonsterCreatureItemsList
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
.minimal-npc-full-chars :deep(.characteristics-card),
.minimal-npc-competences-card.characteristics-card,
.minimal-npc-competences-card :deep(.characteristics-card) {
    padding: 0;
}
.minimal-npc-full-chars :deep(.characteristic-group),
.minimal-npc-competences-card :deep(.characteristic-group) {
    margin-bottom: 0.15rem;
}
.minimal-npc-full-chars :deep(.characteristic-group:last-child),
.minimal-npc-competences-card :deep(.characteristic-group:last-child) {
    margin-bottom: 0;
}
.minimal-npc-full-chars :deep(.characteristic-group h4),
.minimal-npc-competences-card :deep(.characteristic-group h4) {
    margin-bottom: 0.1rem;
    font-size: 0.6rem;
    line-height: 1.1;
    letter-spacing: 0.04em;
}
.minimal-npc-full-chars :deep(.characteristic-group__items),
.minimal-npc-competences-card :deep(.characteristic-group__items) {
    gap: 0.15rem 0.35rem;
}
.minimal-npc-competences-card :deep(.characteristic-property) {
    font-size: 0.7rem;
    line-height: 1.15;
    gap: 0.2rem;
}
</style>
