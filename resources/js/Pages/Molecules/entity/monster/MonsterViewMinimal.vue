<script setup>
/**
 * MonsterViewMinimal — Vue Minimal pour Monster
 *
 * @description
 * Alignée sur ResourceViewMinimal : EntityMinimalCard, État • Image créature • Niveau • Nom • Race • Taille • Boss • Description,
 * caractéristiques créature en zone étendue.
 *
 * @props {Monster} monster - Instance du modèle Monster
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";

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
});

const emit = defineEmits(["edit", "view", "delete", "action"]);

const entity = computed(() => props.monster);

const creatureData = computed(() => entity.value?.creature ?? entity.value?._data?.creature ?? null);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

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

const raceName = computed(() => {
    const r = entity.value?.monsterRace ?? entity.value?._data?.monsterRace;
    return r?.name ?? r?.label ?? null;
});

const sizeLabels = {
    0: "Minuscule",
    1: "Petit",
    2: "Moyen",
    3: "Grand",
    4: "Colossal",
    5: "Gigantesque",
};

const sizeLabel = computed(() => {
    const s = entity.value?.size ?? entity.value?._data?.size;
    if (s === null || typeof s === "undefined") return null;
    return sizeLabels[Number(s)] ?? String(s);
});

const isBoss = computed(() => Boolean(entity.value?.isBoss ?? entity.value?._data?.is_boss));

const descriptionFull = computed(() => {
    const d = creatureData.value?.description;
    return d && String(d).trim() ? String(d) : "";
});

const creatureCharacteristicsGroups = computed(() => buildCreatureCharacteristicGroups(creatureData.value));
const hasCreatureCharacteristics = computed(() => !!creatureData.value);

const creatureIdForStats = computed(() => creatureData.value?.id ?? null);
const { runtime: creatureRuntimeStats } = useCreatureResolvedStats(creatureIdForStats);

const showHref = computed(() =>
    entity.value?.id ? route("entities.monsters.show", { monster: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const monsterId = entity.value?.id;
    if (!monsterId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.monsters.show", { monster: monsterId }));
            emit("view", props.monster);
            break;
        case "edit":
            router.visit(route("entities.monsters.edit", { monster: monsterId }));
            emit("edit", props.monster);
            break;
        case "delete":
            emit("delete", props.monster);
            break;
        default:
            emit("action", actionKey, props.monster);
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
                            :alt="creatureName"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-dragon" alt="" size="xs" class="text-base-content/40" />
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
                                    {{ creatureName }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ creatureName }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="monsters"
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
                            <Badge
                                v-if="raceName"
                                color="auto"
                                :auto-label="raceName"
                                auto-scheme="labelHash"
                                auto-tone="light"
                                variant="soft"
                                size="xs"
                            >
                                {{ raceName }}
                            </Badge>
                            <Tooltip v-if="sizeLabel" :content="`Taille: ${sizeLabel}`" placement="top">
                                <span class="text-base-content/80">{{ sizeLabel }}</span>
                            </Tooltip>
                            <Badge v-if="isBoss" color="error" variant="soft" size="xs">Boss</Badge>
                        </div>
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
                            :alt="creatureName"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-dragon" alt="" size="xs" class="text-base-content/40" />
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
                                    {{ creatureName }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ creatureName }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="monsters"
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
                            <Badge
                                v-if="raceName"
                                color="auto"
                                :auto-label="raceName"
                                auto-scheme="labelHash"
                                auto-tone="light"
                                variant="soft"
                                size="xs"
                            >
                                {{ raceName }}
                            </Badge>
                            <Tooltip v-if="sizeLabel" :content="`Taille: ${sizeLabel}`" placement="top">
                                <span class="text-base-content/80">{{ sizeLabel }}</span>
                            </Tooltip>
                            <Badge v-if="isBoss" color="error" variant="soft" size="xs">Boss</Badge>
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
                <section
                    v-if="hasCreatureCharacteristics"
                    class="w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CharacteristicsCard
                        :entity="creatureData"
                        :groups="creatureCharacteristicsGroups"
                        :dense="true"
                        :runtime="creatureRuntimeStats"
                    />
                </section>
            </div>
        </template>
    </EntityMinimalCard>
</template>
