<script setup>
/**
 * ItemViewMinimal — Vue Minimal pour Item
 *
 * @description
 * Même structure que ItemLineRow mais condensée : State • Image • Level • Nom • Type • Rareté • Prix • Description • Bonus (icône + valeur).
 * Affiche uniquement les propriétés métier (pas read_level, write_level, auto_update, id, created_by, etc.).
 * Prix : `EntityPropertyDisplay` (aligné sur ItemViewFull).
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
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityFieldTooltip from "@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue";
import ItemPanoplyMark from "@/Pages/Molecules/entity/item/ItemPanoplyMark.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import EntityRuleNotes from "@/Pages/Molecules/entity/shared/EntityRuleNotes.vue";
import { itemRuleNotes } from "@/Utils/Entity/itemRuleNotes";

const props = defineProps({
    item: {
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
        viewAny: permissions.can("items", "viewAny"),
        createAny: permissions.can("items", "createAny"),
        updateAny: permissions.can("items", "updateAny"),
        deleteAny: permissions.can("items", "deleteAny"),
        manageAny: permissions.can("items", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getItemFieldDescriptors(ctx.value));

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

const entity = computed(() => props.item);

const showPriceKamas = computed(() => {
    const raw = entity.value?.price ?? entity.value?._data?.price;
    if (raw === null || raw === undefined || raw === '') {
        return false;
    }
    const n = Math.round(Number(raw));
    return Number.isFinite(n) && n > 0;
});

const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const typeName = computed(
    () => entity.value?.itemType?.name ?? entity.value?.item_type ?? entity.value?._data?.itemType?.name ?? "—"
);
const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectItems = computed(() => {
    const bonus = entity.value?.bonus ?? entity.value?._data?.bonus;
    const effect = entity.value?.effect ?? entity.value?._data?.effect;
    const cell = buildCharacteristicEffectCell({
        rawValues: [bonus, effect],
        options: {},
        sourceGroups: ["item", "panoply"],
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

const ruleNotes = computed(() => itemRuleNotes(entity.value));


const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "items",
    showRoute: "entities.items.show",
    editRoute: "entities.items.edit",
    routeParam: "item",
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
        pinned-entity-type="items"
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
                        :label="entity?.name ?? 'Équipement'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <EntityFieldTooltip
                                v-if="levelValue != null"
                                field-key="level"
                                entity-type="item"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <LevelBadge :level="levelValue" size="xs" class="shrink-0" hide-tooltip />
                            </EntityFieldTooltip>
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <ItemPanoplyMark :item="entity" density="icon" :table-meta="tableMeta" class="shrink-0" />
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <EntityFieldTooltip
                                v-if="typeName && typeName !== '—'"
                                field-key="item_type"
                                entity-type="item"
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
                                entity-type="item"
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
                                v-if="canShowField('price') && showPriceKamas"
                                field-key="price"
                                :entity="entity"
                                entity-type="item"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                                hide-field-label
                            />
                        </div>
                        <EntityRuleNotes
                            :notes="ruleNotes"
                            notes-class="mt-0.5 text-[0.7rem] leading-snug text-base-content/75"
                        />
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
                        :label="entity?.name ?? 'Équipement'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <EntityFieldTooltip
                                v-if="levelValue != null"
                                field-key="level"
                                entity-type="item"
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
                                    entity-type="items"
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
                                field-key="item_type"
                                entity-type="item"
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
                                entity-type="item"
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
                                v-if="canShowField('price') && showPriceKamas"
                                field-key="price"
                                :entity="entity"
                                entity-type="item"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                                hide-field-label
                            />
                            <ItemPanoplyMark :item="entity" density="named" :table-meta="tableMeta" />
                        </div>
                        <p
                            v-if="descriptionFull"
                            class="text-xs text-base-content/80 whitespace-pre-wrap break-words"
                        >
                            {{ descriptionFull }}
                        </p>
                        <EntityRuleNotes :notes="ruleNotes" />
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
                    :table-meta="tableMeta"
                    class="w-full pt-1.5 mt-1 border-t border-base-300"
                />
            </div>
        </template>
    </EntityMinimalCard>
</template>
