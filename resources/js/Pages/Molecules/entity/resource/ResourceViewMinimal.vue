<script setup>
/**
 * ResourceViewMinimal — Vue Minimal pour Resource
 *
 * @description
 * Même structure que ResourceLineRow mais condensée : State • Image • Level • Nom • Type • Rareté • Prix • Poids • Description • Effets (icône + valeur).
 * Affiche uniquement les propriétés métier (pas read_level, write_level, id, created_by, etc.).
 * Prix / poids : `EntityPropertyDisplay` (aligné sur ResourceViewFull).
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

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
    /** Payload runtime (tooltips formules) — même schéma que les vues Compact */
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("resources", "viewAny"),
        createAny: permissions.can("resources", "createAny"),
        updateAny: permissions.can("resources", "updateAny"),
        deleteAny: permissions.can("resources", "deleteAny"),
        manageAny: permissions.can("resources", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));

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

const entity = computed(() => props.resource);

const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const typeName = computed(
    () =>
        entity.value?.resourceType?.name ??
        entity.value?.resource_type ??
        entity.value?._data?.resourceType?.name ??
        "—"
);
const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
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

const imageUrl = computed(() => entity.value?.image ?? entity.value?._data?.image ?? null);

const ingredients = computed(() => entity.value?.recipe_ingredients ?? entity.value?.recipeIngredients ?? entity.value?._data?.recipe_ingredients ?? entity.value?._data?.recipeIngredients ?? []);


const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "resources",
    showRoute: "entities.resources.show",
    editRoute: "entities.resources.edit",
    routeParam: "resource",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="resources"
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
                        :label="entity?.name ?? 'Ressource'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
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
                                v-if="canShowField('weight')"
                                field-key="weight"
                                :entity="entity"
                                entity-type="resource"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <EntityPropertyDisplay
                                v-if="canShowField('price')"
                                field-key="price"
                                :entity="entity"
                                entity-type="resource"
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
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="entity?.name ?? 'Ressource'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="resources"
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
                                v-if="canShowField('weight')"
                                field-key="weight"
                                :entity="entity"
                                entity-type="resource"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <EntityPropertyDisplay
                                v-if="canShowField('price')"
                                field-key="price"
                                :entity="entity"
                                entity-type="resource"
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
