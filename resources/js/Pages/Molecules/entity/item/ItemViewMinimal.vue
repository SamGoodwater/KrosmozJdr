<script setup>
/**
 * ItemViewMinimal — Vue Minimal pour Item
 *
 * @description
 * Même structure que ItemLineRow mais condensée : State • Image • Level • Nom • Type • Rareté • Prix • Description • Effets (icône + valeur).
 * Affiche uniquement les propriétés métier (pas read_level, write_level, auto_update, id, created_by, etc.).
 * Prix : `EntityPropertyDisplay` (aligné sur ItemViewFull).
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

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

const emit = defineEmits(["edit", "view", "delete", "action"]);

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
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
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
const showHref = computed(() =>
    entity.value?.id ? route("entities.items.show", { item: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const itemId = entity.value?.id;
    if (!itemId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.items.show", { item: itemId }));
            emit("view", props.item);
            break;
        case "edit":
            router.visit(route("entities.items.edit", { item: itemId }));
            emit("edit", props.item);
            break;
        case "delete":
            emit("delete", props.item);
            break;
        default:
            emit("action", actionKey, props.item);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode" pinned-entity-type="items" :pinned-entity-id="entity?.id">
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <Image
                            v-if="imageUrl"
                            :source="imageUrl"
                            :alt="entity?.name ?? 'Image'"
                            fit="contain"
                            class="h-full w-full"
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
                                    entity-type="items"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
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
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <Image
                            v-if="imageUrl"
                            :source="imageUrl"
                            :alt="entity?.name ?? 'Image'"
                            fit="contain"
                            class="h-full w-full"
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
                                    entity-type="items"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
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
