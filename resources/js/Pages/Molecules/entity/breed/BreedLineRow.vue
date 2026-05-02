<script setup>
/**
 * BreedLineRow — Une ligne de la vue Line pour les classes (Breed)
 *
 * @description
 * Aligné sur MonsterLineRow / SpellLineRow : état • image • dé de vie • nom • spécificité • relations • description.
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "breeds" },
});

const emit = defineEmits(["row-click", "toggle-select", "action"]);

const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?.icon ?? entity.value?._data?.image ?? entity.value?._data?.icon;
    return u && String(u).trim() ? String(u) : null;
});

const nameCell = computed(() => getCell("name"));
const lifeDiceCell = computed(() => getCell("life_dice"));
const specificityCell = computed(() => getCell("specificity"));
const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const orientationMap = computed(() => {
    const raw = entity.value?._data ?? entity.value;
    return normalizeElementOrientationMap(raw?.element_orientations);
});

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
        class="group relative rounded-box border border-base-300 bg-base-100/50 p-3 flex flex-col gap-2 transition-colors hover:bg-glass-sm"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        data-row-contextmenu-target
        @click="handleRowClick"
        @contextmenu="handleContextMenu"
    >
        <div class="absolute top-2 left-2 z-10" @click.stop>
            <EntityUsableDot :state="stateValue" />
        </div>
        <div class="flex gap-3">
            <div
                class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
            >
                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    :alt="entity?.name ?? row?.name ?? 'Classe'"
                    class="h-full w-full object-contain"
                    loading="lazy"
                />
                <Icon v-else source="fa-solid fa-graduation-cap" alt="" size="sm" class="text-base-content/40" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <div class="min-w-0 flex-1">
                            <span class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div
                        v-if="showActions"
                        class="entity-row-actions-hover-reveal"
                        @click.stop
                    >
                        <EntityActions
                            entity-type="breeds"
                            :entity="entity || row"
                            format="dropdown"
                            :whitelist="['pin', 'quick-view', 'view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
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
                <div
                    v-if="lifeDiceCell?.value && lifeDiceCell.value !== '-' && lifeDiceCell.value !== '—'"
                    class="flex flex-wrap items-center gap-2 text-sm"
                >
                    <span class="text-xs text-base-content/80">
                        <span class="font-medium text-base-content">Dé de vie</span>
                        {{ lifeDiceCell.value }}
                    </span>
                </div>
                <div class="w-full mt-1">
                    <BreedElementOrientationsDisplay :orientation-map="orientationMap" size="xs" />
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
                    class="text-xs text-base-content/80 whitespace-normal wrap-break-word"
                    :title="descriptionFull"
                >
                    {{ descriptionFull }}
                </p>
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
    </div>
</template>
