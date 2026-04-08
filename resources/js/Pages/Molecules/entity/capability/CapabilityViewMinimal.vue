<script setup>
/**
 * CapabilityViewMinimal — Vue Minimal pour Capability
 *
 * @description
 * Alignée sur SpellViewMinimal : EntityMinimalCard, état • image • niveau • nom • élément • PA/PO • effet.
 *
 * @props {Capability} capability - Instance du modèle Capability
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";

const props = defineProps({
    capability: {
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

const entity = computed(() => props.capability);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const cellOpts = () => ({ size: "xs", context: "minimal" });

const elementCell = computed(() => entity.value?.toCell?.("element", cellOpts()) ?? null);
const paCell = computed(() => entity.value?.toCell?.("pa", cellOpts()) ?? null);
const poCell = computed(() => entity.value?.toCell?.("po", cellOpts()) ?? null);

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["spell", "item", "panoply"],
        size: "sm",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const descriptionFull = computed(() => {
    const d = entity.value?.description ?? entity.value?._data?.description;
    return d && String(d).trim() ? String(d) : "";
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.capabilities.show", { capability: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const capabilityId = entity.value?.id;
    if (!capabilityId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.capabilities.show", { capability: capabilityId }));
            emit("view", props.capability);
            break;
        case "edit":
            router.visit(route("entities.capabilities.edit", { capability: capabilityId }));
            emit("edit", props.capability);
            break;
        case "delete":
            emit("delete", props.capability);
            break;
        default:
            emit("action", actionKey, props.capability);
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
                            :alt="entity?.name ?? 'Capacité'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-bolt" alt="" size="xs" class="text-base-content/40" />
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
                                    entity-type="capabilities"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['pin', 'quick-view', 'view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="elementCell?.value && elementCell.value !== '—' && elementCell.value !== '-'"
                                :cell="elementCell"
                                class="inline-flex items-center"
                            />
                            <Tooltip
                                v-if="paCell?.value && paCell.value !== '—' && paCell.value !== '-'"
                                content="PA"
                                placement="top"
                            >
                                <span class="text-base-content/80">{{ paCell.value }}</span>
                            </Tooltip>
                            <Tooltip
                                v-if="poCell?.value && poCell.value !== '—' && poCell.value !== '-'"
                                content="PO"
                                placement="top"
                            >
                                <span class="text-base-content/80">{{ poCell.value }}</span>
                            </Tooltip>
                        </div>
                        <div
                            v-if="effectItems.length > 0"
                            class="w-full pt-1.5 mt-1 border-t border-base-300"
                        >
                            <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
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
                            :alt="entity?.name ?? 'Capacité'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-bolt" alt="" size="xs" class="text-base-content/40" />
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
                                    entity-type="capabilities"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['pin', 'quick-view', 'view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="elementCell?.value && elementCell.value !== '—' && elementCell.value !== '-'"
                                :cell="elementCell"
                                class="inline-flex items-center"
                            />
                            <CellRenderer
                                v-if="paCell?.value && paCell.value !== '—' && paCell.value !== '-'"
                                :cell="paCell"
                                class="inline-flex items-center"
                            />
                            <CellRenderer
                                v-if="poCell?.value && poCell.value !== '—' && poCell.value !== '-'"
                                :cell="poCell"
                                class="inline-flex items-center"
                            />
                        </div>
                        <div
                            v-if="effectItems.length > 0"
                            class="w-full pt-1.5 mt-1 border-t border-base-300"
                        >
                            <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
                        </div>
                        <p
                            v-if="descriptionFull"
                            class="text-xs text-base-content/80 line-clamp-4"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
