<script setup>
/**
 * NpcViewMinimal — Vue Minimal pour NPC
 *
 * @description
 * Carte EntityMinimalCard : identité + résumé créature (5 stats) en compact ;
 * groupes complets + méta (race/classe) en déployé. Parcours modal via double-clic / titre.
 *
 * @props {Npc} npc
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { cellHasRenderableContent } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    npc: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
    characteristicRuntime: { type: Object, default: null },
});

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

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

const cardEntity = computed(() =>
    creatureData.value ? { level: creatureData.value.level } : null,
);

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
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

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

const cellOpts = () => ({ size: "xs", context: "minimal", ctx: props.tableMeta });

function getCell(fieldKey) {
    if (entity.value && typeof entity.value.toCell === "function") {
        return entity.value.toCell(fieldKey, cellOpts());
    }
    return { type: "text", value: "—", params: {} };
}

const breedCell = computed(() => getCell("breed"));
const specializationCell = computed(() => getCell("specialization"));
const showBreed = computed(
    () => canShowField("breed") && cellHasRenderableContent(breedCell.value),
);
const showSpecialization = computed(
    () =>
        canShowField("specialization") &&
        cellHasRenderableContent(specializationCell.value),
);
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="npcs"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div data-cy="entity-minimal-card-compact" class="relative flex flex-col gap-1.5 p-2">
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
                                v-if="showSpecialization"
                                :cell="specializationCell"
                                class="inline-flex text-[11px]"
                            />
                        </div>
                        <CharacteristicsCard
                            v-if="summaryCharacteristicGroups.length"
                            :entity="cardEntity"
                            :groups="summaryCharacteristicGroups"
                            :runtime="effectiveRuntime"
                            :density="CHARACTERISTIC_CARD_DENSITY.icon"
                            class="mt-0.5 border-0 bg-transparent p-0 shadow-none ring-0"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template #expanded>
            <div data-cy="entity-minimal-card-expanded" class="relative flex flex-col gap-1.5 p-2">
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
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
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
                                v-if="showSpecialization"
                                :cell="specializationCell"
                                class="inline-flex text-[11px]"
                            />
                        </div>
                    </div>
                </div>

                <CharacteristicsCard
                    v-if="fullCharacteristicGroups.length"
                    :entity="cardEntity"
                    :groups="fullCharacteristicGroups"
                    :runtime="effectiveRuntime"
                    :density="CHARACTERISTIC_CARD_DENSITY.icon"
                    class="border-0 bg-transparent p-0 shadow-none ring-0 [&_.characteristic-group>h4]:!text-[0.6rem]"
                />
            </div>
        </template>
    </EntityMinimalCard>
</template>
