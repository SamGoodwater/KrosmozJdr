<script setup>
/**
 * PanoplyViewMinimal — Vue Minimal pour Panoply
 *
 * @description
 * Alignée sur BreedViewMinimal : EntityMinimalCard, état • picto • nom • objets • bonus • relations • description.
 *
 * @props {Panoply} panoply - Instance du modèle Panoply
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import PanoplyEquipmentTextList from "@/Pages/Molecules/entity/panoply/PanoplyEquipmentTextList.vue";

const props = defineProps({
    panoply: {
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

const entity = computed(() => props.panoply);

const cellOpts = () => ({ size: "xs", context: "minimal" });

const itemsCountCell = computed(() => entity.value?.toCell?.("items_count", cellOpts()) ?? null);
const bonusCell = computed(() => entity.value?.toCell?.("bonus", cellOpts()) ?? null);
const relationsCell = computed(() => entity.value?.toCell?.("panoply_summary_relations", cellOpts()) ?? null);

const linkedItems = computed(() => {
    const raw = entity.value?.items ?? entity.value?._data?.items;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedItems = computed(() => linkedItems.value.length > 0);

const showEquipmentOnHover = computed(() => props.displayMode !== "extended");

const descriptionFull = computed(() => {
    const d = entity.value?.description ?? entity.value?._data?.description;
    return d && String(d).trim() ? String(d) : "";
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.panoplies.show", { panoply: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const panoplyId = entity.value?.id;
    if (!panoplyId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.panoplies.show", { panoply: panoplyId }));
            emit("view", props.panoply);
            break;
        case "edit":
            router.visit(route("entities.panoplies.edit", { panoply: panoplyId }));
            emit("edit", props.panoply);
            break;
        case "delete":
            emit("delete", props.panoply);
            break;
        default:
            emit("action", actionKey, props.panoply);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode" pinned-entity-type="panoplies" :pinned-entity-id="entity?.id">
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :label="entity?.name ?? 'Panoplie'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
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
                                    entity-type="panoplies"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 text-xs">
                            <div
                                v-if="bonusCell?.type === 'chips' && (bonusCell?.params?.items?.length ?? 0) > 0"
                                class="min-w-0"
                            >
                                <span class="text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                                    Effet
                                </span>
                                <CellRenderer :cell="bonusCell" class="inline-flex items-center max-w-full mt-0.5" />
                            </div>
                            <div
                                v-if="hasLinkedItems && showEquipmentOnHover"
                                class="max-h-0 overflow-hidden opacity-0 transition-all duration-150 group-hover:max-h-48 group-hover:opacity-100 group-focus-within:max-h-48 group-focus-within:opacity-100"
                            >
                                <span class="text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                                    Équipements
                                </span>
                                <PanoplyEquipmentTextList :items="linkedItems" class="mt-0.5" />
                            </div>
                            <div v-else-if="hasLinkedItems" class="min-w-0">
                                <span class="text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                                    Équipements
                                </span>
                                <PanoplyEquipmentTextList :items="linkedItems" class="mt-0.5" />
                            </div>
                            <div v-else-if="itemsCountCell?.value && itemsCountCell.value !== '-' && itemsCountCell.value !== '—'" class="text-base-content/70">
                                <span class="font-medium">Objets</span> {{ itemsCountCell.value }}
                            </div>
                            <CellRenderer
                                v-if="
                                    relationsCell?.type === 'chips' &&
                                    (relationsCell?.params?.items?.length ?? 0) > 0
                                "
                                :cell="relationsCell"
                                class="inline-flex items-center"
                            />
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
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :label="entity?.name ?? 'Panoplie'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
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
                                    entity-type="panoplies"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 text-xs">
                            <div
                                v-if="bonusCell?.type === 'chips' && (bonusCell?.params?.items?.length ?? 0) > 0"
                                class="min-w-0"
                            >
                                <span class="text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                                    Effet
                                </span>
                                <CellRenderer :cell="bonusCell" class="inline-flex items-center max-w-full mt-0.5" />
                            </div>
                            <div
                                v-if="hasLinkedItems && showEquipmentOnHover"
                                class="max-h-0 overflow-hidden opacity-0 transition-all duration-150 group-hover:max-h-48 group-hover:opacity-100 group-focus-within:max-h-48 group-focus-within:opacity-100"
                            >
                                <span class="text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                                    Équipements
                                </span>
                                <PanoplyEquipmentTextList :items="linkedItems" class="mt-0.5" />
                            </div>
                            <div v-else-if="hasLinkedItems" class="min-w-0">
                                <span class="text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                                    Équipements
                                </span>
                                <PanoplyEquipmentTextList :items="linkedItems" class="mt-0.5" />
                            </div>
                            <div v-else-if="itemsCountCell?.value && itemsCountCell.value !== '-' && itemsCountCell.value !== '—'" class="text-base-content/70">
                                <span class="font-medium">Objets</span> {{ itemsCountCell.value }}
                            </div>
                            <CellRenderer
                                v-if="
                                    relationsCell?.type === 'chips' &&
                                    (relationsCell?.params?.items?.length ?? 0) > 0
                                "
                                :cell="relationsCell"
                                class="inline-flex items-center"
                            />
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
