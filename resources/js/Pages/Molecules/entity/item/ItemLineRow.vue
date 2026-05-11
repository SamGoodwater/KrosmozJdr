<script setup>
/**
 * ItemLineRow — Une ligne de la vue Line pour Item
 *
 * @description
 * Même structure que ResourceLineRow : State • Image • Level • Nom • Type • Rareté • Prix • Description • Effets
 * Pas de poids (équipements). Prix : `EntityPropertyDisplay` (aligné sur ItemViewCompact).
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import { Link } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "items" },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

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

const isInteractiveTarget = (event) => {
    const el = event?.target;
    if (!el || typeof el.closest !== "function") return false;
    return Boolean(
        el.closest(
            'a,button,input,select,textarea,[role="button"],[role="link"],[contenteditable="true"],[data-no-row-select]',
        ),
    );
};

const handleDoubleClick = (e) => {
    if (!isInteractiveTarget(e)) emit("row-dblclick", props.row);
};

/** Entité source : rowParams.entity (API) ou row lui-même (données plates) */
const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const showPriceKamas = computed(() => {
    const raw = entity.value?.price ?? entity.value?._data?.price;
    if (raw === null || raw === undefined || raw === "") {
        return false;
    }
    const n = Math.round(Number(raw));
    return Number.isFinite(n) && n > 0;
});

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const typeCell = computed(() => getCell("item_type"));
/** Description brute (non tronquée) */
const descriptionFull = computed(() => entity.value?.description ?? entity.value?._data?.description ?? "");

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["item", "panoply"],
        size: "md",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = entity.value?.rarity ?? entity.value?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

/** Ingrédients (ressources) de recette */
const ingredients = computed(
    () => entity.value?.resources ?? entity.value?._data?.resources ?? []
);

const handleRowClick = (e) => emit("row-click", props.row, e);

const contextMenuVisible = ref(false);
const contextMenuPosition = ref({ x: 0, y: 0 });
const handleContextMenu = (e) => {
    if (!props.entityType) return;
    e.preventDefault();
    e.stopPropagation();
    contextMenuPosition.value = { x: e.clientX, y: e.clientY };
    contextMenuVisible.value = true;
};
const closeContextMenu = () => {
    contextMenuVisible.value = false;
    nextTick(() => focusTableRowById(props.row?.id));
};
const handleContextAction = (actionKey) => {
    closeContextMenu();
    emit("action", actionKey, entity.value ?? props.row, props.row);
};
onUnmounted(() => {
    if (typeof window !== "undefined") document.removeEventListener("click", closeContextMenu);
});
if (typeof window !== "undefined") document.addEventListener("click", closeContextMenu);
</script>

<template>
    <div
        class="group relative rounded-box border border-base-300 bg-glass-2xl p-3 flex flex-col gap-2 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="handleRowClick"
        @dblclick="handleDoubleClick"
        @contextmenu="handleContextMenu"
    >
        <!-- Bloc Image + titre + propriétés -->
        <div class="flex gap-3">
        <!-- Image : pleine hauteur à gauche -->
        <div
            class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
        >
            <Image
                v-if="imageCell?.value"
                :source="imageCell.value"
                :alt="entity?.name ?? row?.name ?? 'Image'"
                fit="contain"
                class="h-full w-full"
            />
            <Icon v-else source="fa-solid fa-image" alt="" size="sm" class="text-base-content/40" />
        </div>
        <!-- Contenu à droite de l'image -->
        <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
            <!-- Ligne 1 : Titre + Niveau + Actions -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                    <div class="min-w-0 flex-1">
                        <Link
                            v-if="nameCell?.type === 'route' && nameCell?.params?.href"
                            :href="nameCell.params.href"
                            class="font-semibold truncate block text-base-content hover:text-base-content link link-neutral link-hover"
                            @click.stop
                        >
                            {{ nameCell.value || "—" }}
                        </Link>
                        <span v-else class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                    </div>
                </div>
                <div
                    v-if="showActions"
                    class="entity-row-actions-hover-reveal"
                    @click.stop
                >
                    <EntityActions
                        entity-type="items"
                        :entity="entity || row"
                        format="dropdown"
                        :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                        @action="(k, e) => emit('action', k, e, row)"
                    />
                </div>
                <div
                    v-if="showSelection"
                    class="flex shrink-0 items-center transition-[max-width,opacity] duration-150 ease-out"
                    :class="
                        isSelected
                            ? 'max-w-10 overflow-visible opacity-100 pointer-events-auto'
                            : 'max-w-0 overflow-hidden opacity-0 pointer-events-none group-hover:max-w-10 group-hover:overflow-visible group-hover:opacity-100 group-hover:pointer-events-auto group-focus-within:max-w-10 group-focus-within:overflow-visible group-focus-within:opacity-100 group-focus-within:pointer-events-auto'
                    "
                    @click.stop
                >
                    <CheckboxCore
                        :model-value="isSelected"
                        size="xs"
                        :color="uiColor"
                        aria-label="Sélectionner"
                        class="shrink-0"
                        @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                    />
                </div>
            </div>
            <!-- Ligne 2 : Type • Rareté • Prix -->
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <Badge v-if="typeCell?.value" color="auto" :auto-label="typeCell.value" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
                    {{ typeCell.value }}
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
                />
            </div>
            <!-- Ligne 3 : Description (complète, retour à la ligne) -->
            <p
                v-if="descriptionFull"
                class="text-xs text-base-content/80 whitespace-normal wrap-break-word"
                :title="descriptionFull"
            >
                {{ descriptionFull }}
            </p>
        </div>
        </div>
        <!-- Effets : pleine largeur sous le bloc Image/titre/propriétés -->
        <div
            v-if="effectItems.length > 0"
            class="w-full pt-2 mt-1 border-t border-base-300"
        >
            <CharacteristicEffectsGrid :items="effectItems" />
        </div>
        <!-- Ingrédients (ressources) : icône + nom, sous les effets -->
        <div
            v-if="ingredients.length > 0"
            class="w-full pt-2 mt-1 border-t border-base-300"
        >
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>
    </div>

    <Teleport to="body">
        <EntityActions
            v-if="entityType && contextMenuVisible"
            :entity-type="entityType"
            :entity="entity || row"
            format="context"
            display="icon-text"
            size="sm"
            color="primary"
            :context="{ inPanel: false }"
            :context-position="contextMenuPosition"
            :context-visible="contextMenuVisible"
            @close="closeContextMenu"
            @action="handleContextAction"
        />
    </Teleport>
</template>
