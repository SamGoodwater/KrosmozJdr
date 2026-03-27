<script setup>
/**
 * BreedViewMinimal — Vue Minimal pour Breed
 *
 * @description
 * Alignée sur MonsterViewMinimal : EntityMinimalCard, état • image • nom • vie / dé • spécificité • relations • description.
 *
 * @props {Breed} breed - Instance du modèle Breed
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";

const props = defineProps({
    breed: {
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

const entity = computed(() => props.breed);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?.icon ?? entity.value?._data?.image ?? entity.value?._data?.icon;
    return u && String(u).trim() ? String(u) : null;
});

const cellOpts = () => ({ size: "xs", context: "minimal" });

const lifeCell = computed(() => entity.value?.toCell?.("life", cellOpts()) ?? null);
const lifeDiceCell = computed(() => entity.value?.toCell?.("life_dice", cellOpts()) ?? null);
const specificityCell = computed(() => entity.value?.toCell?.("specificity", cellOpts()) ?? null);
const relationsCell = computed(() => entity.value?.toCell?.("breed_summary_relations", cellOpts()) ?? null);

const descriptionFull = computed(() => {
    const d = entity.value?.description ?? entity.value?._data?.description;
    return d && String(d).trim() ? String(d) : "";
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.breeds.show", { breed: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const breedId = entity.value?.id;
    if (!breedId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.breeds.show", { breed: breedId }));
            emit("view", props.breed);
            break;
        case "edit":
            router.visit(route("entities.breeds.edit", { breed: breedId }));
            emit("edit", props.breed);
            break;
        case "delete":
            emit("delete", props.breed);
            break;
        default:
            emit("action", actionKey, props.breed);
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
                            :alt="entity?.name ?? 'Classe'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-graduation-cap" alt="" size="xs" class="text-base-content/40" />
                    </div>
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
                                    entity-type="breeds"
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
                                v-if="lifeCell?.value && lifeCell.value !== '-' && lifeCell.value !== '—'"
                                :content="`Vie : ${lifeCell.value}`"
                                placement="top"
                            >
                                <span class="text-base-content/80">
                                    <span class="font-medium">Vie</span>
                                    {{ lifeCell.value }}
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="lifeDiceCell?.value && lifeDiceCell.value !== '-' && lifeDiceCell.value !== '—'"
                                :content="`Dé de vie : ${lifeDiceCell.value}`"
                                placement="top"
                            >
                                <span class="text-base-content/80">
                                    <span class="font-medium">Dé</span>
                                    {{ lifeDiceCell.value }}
                                </span>
                            </Tooltip>
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
                            v-if="specificityCell?.value && specificityCell.value !== '-' && specificityCell.value !== '—'"
                            class="text-xs text-base-content/70 line-clamp-2"
                            :title="String(specificityCell.value)"
                        >
                            {{ specificityCell.value }}
                        </p>
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
                            :alt="entity?.name ?? 'Classe'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-graduation-cap" alt="" size="xs" class="text-base-content/40" />
                    </div>
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
                                    entity-type="breeds"
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
                                v-if="lifeCell?.value && lifeCell.value !== '-' && lifeCell.value !== '—'"
                                :content="`Vie : ${lifeCell.value}`"
                                placement="top"
                            >
                                <span class="text-base-content/80">
                                    <span class="font-medium">Vie</span>
                                    {{ lifeCell.value }}
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="lifeDiceCell?.value && lifeDiceCell.value !== '-' && lifeDiceCell.value !== '—'"
                                :content="`Dé de vie : ${lifeDiceCell.value}`"
                                placement="top"
                            >
                                <span class="text-base-content/80">
                                    <span class="font-medium">Dé</span>
                                    {{ lifeDiceCell.value }}
                                </span>
                            </Tooltip>
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
                            v-if="specificityCell?.value && specificityCell.value !== '-' && specificityCell.value !== '—'"
                            class="text-xs text-base-content/70 line-clamp-2"
                            :title="String(specificityCell.value)"
                        >
                            {{ specificityCell.value }}
                        </p>
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
