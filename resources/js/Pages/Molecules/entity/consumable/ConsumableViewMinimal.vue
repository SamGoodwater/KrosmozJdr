<script setup>
/**
 * ConsumableViewMinimal — Vue Minimal pour Consumable
 *
 * @description
 * Même structure que ConsumableLineRow mais condensée : State • Image • Level • Nom • Type • Rareté • Prix • Description • Effets (icône + valeur).
 * Affiche uniquement les propriétés métier (pas read_level, write_level, id, created_by, etc.).
 * Prix : `EntityPropertyDisplay` (aligné sur ConsumableViewFull).
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
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityFieldTooltip from "@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue";
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

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

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


const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "consumables",
    showRoute: "entities.consumables.show",
    editRoute: "entities.consumables.edit",
    routeParam: "consumable",
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
        pinned-entity-type="consumables"
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
                        :label="entity?.name ?? 'Consommable'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <EntityFieldTooltip
                                v-if="levelValue != null"
                                field-key="level"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <LevelBadge :level="levelValue" size="xs" class="shrink-0" hide-tooltip />
                            </EntityFieldTooltip>
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <EntityFieldTooltip
                                v-if="typeName && typeName !== '—'"
                                field-key="consumable_type"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge color="auto" :auto-label="typeName" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
                                    {{ typeName }}
                                </Badge>
                            </EntityFieldTooltip>
                            <EntityFieldTooltip
                                v-if="rarityConfig"
                                field-key="rarity"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                                    variant="soft"
                                    size="xs"
                                >
                                    {{ rarityConfig.label }}
                                </Badge>
                            </EntityFieldTooltip>
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
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="entity?.name ?? 'Consommable'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <EntityFieldTooltip
                                v-if="levelValue != null"
                                field-key="level"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <LevelBadge :level="levelValue" size="xs" class="shrink-0" hide-tooltip />
                            </EntityFieldTooltip>
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="consumables"
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
                            <EntityFieldTooltip
                                v-if="typeName && typeName !== '—'"
                                field-key="consumable_type"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge color="auto" :auto-label="typeName" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
                                    {{ typeName }}
                                </Badge>
                            </EntityFieldTooltip>
                            <EntityFieldTooltip
                                v-if="rarityConfig"
                                field-key="rarity"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                                    variant="soft"
                                    size="xs"
                                >
                                    {{ rarityConfig.label }}
                                </Badge>
                            </EntityFieldTooltip>
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
                            class="text-xs text-base-content/80 whitespace-pre-wrap break-words"
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
