<script setup>
/**
 * ConsumableViewMinimal — Vue Minimal pour Consumable
 *
 * @description
 * Même structure que ConsumableLineRow mais condensée : State • Image • Level • Nom • Type • Rareté • Prix • Description • Effets (icône + valeur).
 * Affiche uniquement les propriétés métier (pas read_level, write_level, id, created_by, etc.).
 * Prix : `EntityPropertyDisplay` (aligné sur ConsumableViewCompact).
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
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

const props = defineProps({
    consumable: {
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

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["edit", "view", "delete", "action"]);

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("consumables", "viewAny"),
        createAny: permissions.can("consumables", "createAny"),
        updateAny: permissions.can("consumables", "updateAny"),
        deleteAny: permissions.can("consumables", "deleteAny"),
        manageAny: permissions.can("consumables", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));

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

const entity = computed(() => props.consumable);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);
const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const typeName = computed(
    () =>
        entity.value?.consumableType?.name ??
        entity.value?.consumable_type ??
        entity.value?._data?.consumableType?.name ??
        "—"
);
const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["consumable", "item"],
        size: "sm",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = entity.value?.rarity ?? entity.value?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

const imageUrl = computed(() => entity.value?.image ?? entity.value?._data?.image ?? null);

const ingredients = computed(() => entity.value?.resources ?? entity.value?._data?.resources ?? []);
const showHref = computed(() =>
    entity.value?.id ? route("entities.consumables.show", { consumable: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const consumableId = entity.value?.id;
    if (!consumableId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.consumables.show", { consumable: consumableId }));
            emit("view", props.consumable);
            break;
        case "edit":
            router.visit(route("entities.consumables.edit", { consumable: consumableId }));
            emit("edit", props.consumable);
            break;
        case "delete":
            emit("delete", props.consumable);
            break;
        default:
            emit("action", actionKey, props.consumable);
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
                        <Icon v-else source="fa-solid fa-flask" alt="" size="xs" class="text-base-content/40" />
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
                                    entity-type="consumables"
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
                            <Badge v-if="typeName && typeName !== '—'" color="auto" :auto-label="typeName" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
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
                            <EntityPropertyDisplay
                                v-if="canShowField('price')"
                                field-key="price"
                                :entity="entity"
                                entity-type="consumable"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
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
                        <Icon v-else source="fa-solid fa-flask" alt="" size="xs" class="text-base-content/40" />
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
                                    entity-type="consumables"
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
                            <Badge v-if="typeName && typeName !== '—'" color="auto" :auto-label="typeName" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
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
                            <EntityPropertyDisplay
                                v-if="canShowField('price')"
                                field-key="price"
                                :entity="entity"
                                entity-type="consumable"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
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
