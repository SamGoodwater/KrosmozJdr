<script setup>
/**
 * NpcLineRow — Une ligne de la vue Line pour NPC
 *
 * @description
 * État • Image • Niveau • Nom • métas (classe, rôle, taille, hostilité, lieu) • résumés caracs • sorts/items.
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterCreatureItemsList from "@/Pages/Molecules/entity/monster/MonsterCreatureItemsList.vue";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import { cellHasRenderableContent } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "npcs" },
});

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const entity = computed(() => getRowEntity(props.row));

const creature = computed(() => entity.value?.creature ?? entity.value?._data?.creature ?? null);

const creatureIdForStats = computed(() => creature.value?.id ?? null);
const { runtime: fetchedRuntime } = useCreatureResolvedStats(creatureIdForStats, "npc");

const characteristicRuntime = computed(
    () => props.tableMeta?.characteristicRuntime ?? fetchedRuntime.value ?? null,
);

const competenceGroups = computed(() =>
    buildCreatureCompetenceGroupsByPrimary(creature.value, {
        includeZero: true,
        runtime: characteristicRuntime.value,
    }),
);

const summaryCharacteristicGroups = computed(() =>
    buildCreatureCharacteristicGroups(creature.value, {
        mode: "summary",
        runtime: characteristicRuntime.value,
    }),
);

const cardEntityForCompetences = computed(() =>
    creature.value ? { level: creature.value.level } : null,
);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const cellForKey = (fieldKey) => {
    if (!props.getCellFor) return { type: "text", value: "—", params: {} };
    return (
        props.getCellFor(props.row, { id: fieldKey, cellId: fieldKey }) || {
            type: "text",
            value: "—",
            params: {},
        }
    );
};

const levelValue = computed(() => {
    const lv = creature.value?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const nameCell = computed(() => getCell("creature_name"));
const imageUrl = computed(() => {
    const u = creature.value?.image ?? entity.value?.image;
    return u && String(u).trim() ? String(u) : "";
});
const breedCell = computed(() => getCell("breed"));
const roleCell = computed(() => cellForKey("npc_role"));
const sizeCell = computed(() => cellForKey("size"));
const hostilityCell = computed(() => cellForKey("creature_hostility"));
const locationCell = computed(() => cellForKey("creature_location"));

const showBreedCell = computed(() => cellHasRenderableContent(breedCell.value));
const showRoleCell = computed(() => cellHasRenderableContent(roleCell.value));
const showSizeCell = computed(() => cellHasRenderableContent(sizeCell.value));
const showHostilityCell = computed(() => cellHasRenderableContent(hostilityCell.value));
const showLocationCell = computed(() => cellHasRenderableContent(locationCell.value));

const descriptionFull = computed(
    () => creature.value?.description ?? entity.value?._data?.creature?.description ?? "",
);

const linkedLanguages = computed(() => {
    const raw = entity.value?._data?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw =
        entity.value?._data?.creature?.creatureTraits ??
        entity.value?.creature?.creatureTraits ??
        [];
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);
</script>

<template>
    <div
        class="group/entity-minimal relative rounded-box border border-base-300 bg-glass-2xl p-3 flex flex-col gap-2 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <div
            v-if="showActions || showSelection"
            class="npc-line-actions-host absolute top-2 right-2 z-20 flex w-48 max-w-48 items-center justify-end gap-2"
            @click.stop
        >
            <div v-if="showActions" class="npc-line-actions-reveal min-w-0 flex-1">
                <EntityLineRowActions
                    entity-type="npcs"
                    :entity="entity"
                    @action="(k, e) => emit('action', k, e, row)"
                />
            </div>
            <div
                v-if="showSelection"
                class="flex h-8 w-8 shrink-0 items-center justify-end"
            >
                <CheckboxCore
                    :model-value="isSelected"
                    size="xs"
                    :color="uiColor"
                    aria-label="Sélectionner"
                    class="shrink-0"
                    @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                />
            </div>
        </div>
        <div class="flex w-full min-w-0 flex-col gap-3 pr-14 sm:pr-16 lg:flex-row lg:items-start">
            <div class="flex min-w-0 shrink-0 gap-3 lg:max-w-[min(100%,26rem)]">
                <EntityThumb
                    size="line"
                    :src="imageUrl"
                    :label="creature?.name ?? entity?.name ?? 'PNJ'"
                />
                <div class="flex min-w-0 flex-1 flex-col gap-1.5 pl-1">
                    <div class="flex min-w-0 items-center gap-2">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <span class="block truncate font-semibold">{{ nameCell?.value || creature?.name || "—" }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <CellRenderer
                            v-if="showBreedCell"
                            :cell="breedCell"
                            class="inline-flex text-xs"
                        />
                        <CellRenderer
                            v-if="showRoleCell"
                            :cell="roleCell"
                            class="inline-flex text-xs"
                        />
                        <CellRenderer
                            v-if="showSizeCell"
                            :cell="sizeCell"
                            class="inline-flex text-xs text-base-content/80"
                        />
                        <CellRenderer
                            v-if="showHostilityCell"
                            :cell="hostilityCell"
                            class="inline-flex text-xs font-medium text-base-content/85"
                        />
                        <CellRenderer
                            v-if="showLocationCell"
                            :cell="locationCell"
                            class="inline-flex text-xs text-base-content/70"
                        />
                    </div>
                    <div
                        v-if="hasLinkedCreatureTraits"
                        class="flex flex-wrap gap-1"
                        role="region"
                        aria-label="Traits"
                    >
                        <CreatureTraitBadges :traits="linkedCreatureTraits" size="xs" />
                    </div>
                    <p
                        v-if="descriptionFull"
                        class="wrap-break-word text-xs whitespace-normal text-base-content/80 italic line-clamp-3 transition-[line-clamp] duration-150 group-hover/entity-minimal:line-clamp-none"
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
                            class="min-w-0 max-w-44"
                        />
                    </div>
                </div>
            </div>

            <div class="w-full min-h-0 min-w-0 flex-1">
                <CharacteristicsCard
                    v-if="summaryCharacteristicGroups.length"
                    :entity="cardEntityForCompetences"
                    :groups="summaryCharacteristicGroups"
                    :runtime="characteristicRuntime"
                    :density="CHARACTERISTIC_CARD_DENSITY.icon"
                    class="border-0 bg-transparent p-0 shadow-none ring-0"
                />
            </div>
        </div>

        <div
            v-if="competenceGroups.length"
            class="npc-line-competences max-h-[min(85vh,42rem)] overflow-y-auto overscroll-contain rounded-box border border-base-300/60 bg-base-200/25 px-2 py-1.5"
        >
            <p class="mb-1 text-[0.625rem] font-semibold uppercase tracking-wide text-base-content/60">
                Compétences
            </p>
            <CharacteristicsCard
                :entity="cardEntityForCompetences"
                :groups="competenceGroups"
                :runtime="characteristicRuntime"
                :density="CHARACTERISTIC_CARD_DENSITY.icon"
                class="border-0 bg-transparent p-0 shadow-none ring-0"
            />
        </div>

        <MonsterCreatureSpellsList
            v-if="creature"
            :creature="creature"
            :table-meta="tableMeta"
            :characteristic-runtime="characteristicRuntime"
            section-class="mt-1.5 border-t border-base-300/50 pt-1.5"
        />
        <MonsterCreatureItemsList
            v-if="creature"
            :creature="creature"
            :table-meta="tableMeta"
            :characteristic-runtime="characteristicRuntime"
            section-class="mt-1.5 border-t border-base-300/50 pt-1.5"
        />
    </div>
</template>

<style scoped>
.npc-line-actions-reveal :deep(.entity-row-actions-hover-reveal) {
    max-width: 12rem !important;
    width: 12rem !important;
    overflow: visible !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
}
.group:hover .npc-line-actions-reveal :deep(.entity-row-actions-hover-reveal),
.group:focus-within .npc-line-actions-reveal :deep(.entity-row-actions-hover-reveal),
.group:has(.npc-line-actions-host .entity-row-actions-hover-reveal [data-dropdown-open="true"])
    .npc-line-actions-reveal
    :deep(.entity-row-actions-hover-reveal),
.group:has(.npc-line-actions-host .entity-row-actions-hover-reveal [aria-expanded="true"])
    .npc-line-actions-reveal
    :deep(.entity-row-actions-hover-reveal) {
    opacity: 1;
    pointer-events: auto;
}

.npc-line-competences :deep(.characteristics-card) {
    padding: 0;
    border: none;
    background: transparent;
    box-shadow: none;
}
.npc-line-competences :deep(.characteristic-group) {
    margin-bottom: 0.15rem;
}
.npc-line-competences :deep(.characteristic-group:last-child) {
    margin-bottom: 0;
}
.npc-line-competences :deep(.characteristic-group h4) {
    margin-bottom: 0.1rem;
    font-size: 0.6rem;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    opacity: 0.8;
}
.npc-line-competences :deep(.characteristic-group__items) {
    gap: 0.15rem 0.35rem;
}
.npc-line-competences :deep(.characteristic-property) {
    font-size: 0.7rem;
    line-height: 1.15;
    gap: 0.2rem;
}
</style>
