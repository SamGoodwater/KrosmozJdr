<script setup>
/**
 * CapabilityLineRow — Ligne vue Line pour les capacités
 *
 * @description
 * Aligné sur SpellLineRow / PanoplyLineRow : état • image • niveau • nom • élément • PA/PO • effet • description.
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "capabilities" },
});

const emit = defineEmits(["row-click", "toggle-select", "action"]);

const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const nameCell = computed(() => getCell("name"));
const elementCell = computed(() => getCell("element"));
const paCell = computed(() => getCell("pa"));
const poCell = computed(() => getCell("po"));

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["spell", "item", "panoply"],
        size: "md",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
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
        class="relative rounded-box border border-base-300 bg-base-100/50 p-3 flex flex-col gap-2 transition-colors hover:bg-glass-sm"
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
                    :alt="entity?.name ?? row?.name ?? 'Capacité'"
                    class="h-full w-full object-contain"
                    loading="lazy"
                />
                <Icon v-else source="fa-solid fa-bolt" alt="" size="sm" class="text-base-content/40" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <span class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div v-if="showActions" class="shrink-0" @click.stop>
                        <EntityActions
                            entity-type="capabilities"
                            :entity="entity || row"
                            format="dropdown"
                            :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
                            @action="(k, e) => emit('action', k, e, row)"
                        />
                    </div>
                    <CheckboxCore
                        v-if="showSelection"
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
                    <CellRenderer
                        v-if="elementCell?.value && elementCell.value !== '—' && elementCell.value !== '-'"
                        :cell="elementCell"
                        class="inline-flex"
                    />
                    <CellRenderer
                        v-if="paCell?.value && paCell.value !== '—' && paCell.value !== '-'"
                        :cell="paCell"
                        class="inline-flex"
                    />
                    <CellRenderer
                        v-if="poCell?.value && poCell.value !== '—' && poCell.value !== '-'"
                        :cell="poCell"
                        class="inline-flex"
                    />
                </div>
                <div
                    v-if="effectItems.length > 0"
                    class="w-full pt-1 border-t border-base-300/80"
                >
                    <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
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
