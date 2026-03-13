<script setup>
/**
 * ResourceViewMinimal — Vue Minimal pour Resource
 *
 * @description
 * Même structure que ResourceLineRow mais condensée : State • Image • Level • Nom • Type • Rareté • Prix • Poids • Description • Effets (icône + valeur).
 * Affiche uniquement les propriétés métier (pas read_level, write_level, id, created_by, etc.).
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";

const props = defineProps({
    resource: {
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

const entity = computed(() => props.resource);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);
const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const typeName = computed(
    () =>
        entity.value?.resourceType?.name ??
        entity.value?.resource_type ??
        entity.value?._data?.resourceType?.name ??
        "—"
);
const priceValue = computed(() => entity.value?.price ?? entity.value?._data?.price ?? null);
const weightValue = computed(() => entity.value?.weight ?? entity.value?._data?.weight ?? null);
const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectItems = computed(() => {
    const ctx = {
        ...props.tableMeta,
        characteristics: props.tableMeta?.characteristics || {},
    };
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: { ctx },
        sourceGroups: ["resource", "item"],
        size: "sm",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = entity.value?.rarity ?? entity.value?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

const byDbColumn = computed(
    () =>
        props.tableMeta?.characteristics?.resource?.byDbColumn ||
        props.tableMeta?.characteristics?.item?.byDbColumn ||
        {}
);
const priceMeta = computed(() => byDbColumn.value?.price || byDbColumn.value?.kamas || null);
const weightMeta = computed(() => byDbColumn.value?.weight || byDbColumn.value?.pods || null);

const imageUrl = computed(() => entity.value?.image ?? entity.value?._data?.image ?? null);

const ingredients = computed(() => entity.value?.recipe_ingredients ?? entity.value?.recipeIngredients ?? entity.value?._data?.recipe_ingredients ?? entity.value?._data?.recipeIngredients ?? []);
const showHref = computed(() =>
    entity.value?.id ? route("entities.resources.show", { resource: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const resourceId = entity.value?.id;
    if (!resourceId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.resources.show", { resource: resourceId }));
            emit("view", props.resource);
            break;
        case "edit":
            router.visit(route("entities.resources.edit", { resource: resourceId }));
            emit("edit", props.resource);
            break;
        case "delete":
            emit("delete", props.resource);
            break;
        default:
            emit("action", actionKey, props.resource);
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
                            :alt="entity?.name ?? 'Image'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-image" alt="" size="xs" class="text-base-content/40" />
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
                                    entity-type="resources"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :available="['view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k, e) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <Badge v-if="typeName && typeName !== '—'" color="neutral" variant="soft" size="xs">
                                {{ typeName }}
                            </Badge>
                            <Badge
                                v-if="rarityConfig"
                                :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                                variant="soft"
                                size="xs"
                            >
                                {{ rarityConfig.label }}
                            </Badge>
                            <Tooltip
                                v-if="priceValue != null && priceValue !== ''"
                                :content="`Prix: ${priceValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="priceMeta?.icon || 'fa-solid fa-coins'"
                                        alt="Prix"
                                        size="xs"
                                        :style="priceMeta?.color ? { color: `var(--color-${priceMeta.color})` } : undefined"
                                    />
                                    <span>{{ priceValue }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="weightValue != null && weightValue !== ''"
                                :content="`Poids: ${weightValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="weightMeta?.icon || 'fa-solid fa-weight-hanging'"
                                        alt="Poids"
                                        size="xs"
                                        :style="weightMeta?.color ? { color: `var(--color-${weightMeta.color})` } : undefined"
                                    />
                                    <span>{{ weightValue }}</span>
                                </span>
                            </Tooltip>
                        </div>
                    </div>
                </div>
                <div
                    v-if="effectItems.length > 0"
                    class="w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
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
                            :alt="entity?.name ?? 'Image'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon v-else source="fa-solid fa-image" alt="" size="xs" class="text-base-content/40" />
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
                                    entity-type="resources"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :available="['view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k, e) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <Badge v-if="typeName && typeName !== '—'" color="neutral" variant="soft" size="xs">
                                {{ typeName }}
                            </Badge>
                            <Badge
                                v-if="rarityConfig"
                                :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                                variant="soft"
                                size="xs"
                            >
                                {{ rarityConfig.label }}
                            </Badge>
                            <Tooltip
                                v-if="priceValue != null && priceValue !== ''"
                                :content="`Prix: ${priceValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="priceMeta?.icon || 'fa-solid fa-coins'"
                                        alt="Prix"
                                        size="xs"
                                        :style="priceMeta?.color ? { color: `var(--color-${priceMeta.color})` } : undefined"
                                    />
                                    <span>{{ priceValue }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="weightValue != null && weightValue !== ''"
                                :content="`Poids: ${weightValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="weightMeta?.icon || 'fa-solid fa-weight-hanging'"
                                        alt="Poids"
                                        size="xs"
                                        :style="weightMeta?.color ? { color: `var(--color-${weightMeta.color})` } : undefined"
                                    />
                                    <span>{{ weightValue }}</span>
                                </span>
                            </Tooltip>
                        </div>
                        <p
                            v-if="descriptionFull"
                            class="text-xs text-base-content/80 line-clamp-2"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
                <div
                    v-if="effectItems.length > 0"
                    class="w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
                </div>
                <ResourceIngredientsList
                    v-if="ingredients.length > 0"
                    :ingredients="ingredients"
                    class="w-full pt-1.5 mt-1 border-t border-base-300"
                />
            </div>
        </template>
    </EntityMinimalCard>
</template>
