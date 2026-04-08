<script setup>
/**
 * PanoplyLineRow — Une ligne de la vue Line pour les panoplies
 *
 * @description
 * Aligné sur BreedLineRow : état • picto • nom • nb objets • bonus • relations • description.
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "panoplies" },
});

const emit = defineEmits(["row-click", "toggle-select", "action"]);

const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const nameCell = computed(() => getCell("name"));
const itemsCountCell = computed(() => getCell("items_count"));
const bonusCell = computed(() => getCell("bonus"));
const relationsCell = computed(() => getCell("panoply_summary_relations"));

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
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
                <Icon source="fa-solid fa-layer-group" alt="" size="lg" class="text-base-content/35" />
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
                            entity-type="panoplies"
                            :entity="entity || row"
                            format="dropdown"
                            :whitelist="['pin', 'quick-view', 'view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
                            @action="(k, e) => emit('action', k, e, row)"
                        />
                    </div>
                    <CheckboxCore
                        v-if="showSelection && isSelected"
                        :model-value="isSelected"
                        size="xs"
                        :color="uiColor"
                        aria-label="Sélectionner"
                        class="shrink-0"
                        @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                        @click.stop
                    />
                </div>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span
                        v-if="itemsCountCell?.value && itemsCountCell.value !== '-' && itemsCountCell.value !== '—'"
                        class="text-xs text-base-content/80"
                    >
                        <span class="font-medium text-base-content">Objets</span>
                        {{ itemsCountCell.value }}
                    </span>
                    <CellRenderer
                        v-if="bonusCell && bonusCell.type === 'chips' && (bonusCell?.params?.items?.length ?? 0) > 0"
                        :cell="bonusCell"
                        class="inline-flex max-w-full"
                    />
                    <CellRenderer
                        v-if="relationsCell?.type === 'chips' && (relationsCell?.params?.items?.length ?? 0) > 0"
                        :cell="relationsCell"
                        class="inline-flex"
                    />
                </div>
                <p
                    v-if="descriptionFull"
                    class="text-xs text-base-content/80 whitespace-normal wrap-break-word line-clamp-3"
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
