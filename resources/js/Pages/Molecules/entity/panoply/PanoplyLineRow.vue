<script setup>
/**
 * PanoplyLineRow — Une ligne de la vue Line pour les panoplies
 *
 * @description
 * Aligné sur BreedLineRow : état • picto • nom • nb objets • bonus • relations • description.
 */
import { computed } from "vue";
import PanoplyThumb from "@/Pages/Molecules/entity/panoply/PanoplyThumb.vue";
import PanoplyEquipmentTextList from "@/Pages/Molecules/entity/panoply/PanoplyEquipmentTextList.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRowEntity } from "@/Utils/Entity/rowEntity";

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

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const entity = computed(() => getRowEntity(props.row));

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const nameCell = computed(() => getCell("name"));
const bonusCell = computed(() => getCell("bonus"));
const relationsCell = computed(() => getCell("panoply_summary_relations"));

const linkedItems = computed(() => {
    const raw = entity.value?.items ?? entity.value?._data?.items;
    return Array.isArray(raw) ? raw : [];
});

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

</script>

<template>
    <div
        class="group relative rounded-box border border-base-300 bg-glass-2xl p-3 flex flex-col gap-2 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <div class="flex gap-3">
            <PanoplyThumb
                size="line"
                :items="linkedItems"
                :label="nameCell?.value || 'Panoplie'"
            />
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <div class="min-w-0 flex-1">
                            <span class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <EntityLineRowActions
                        v-if="showActions"
                        entity-type="panoplies"
                        :entity="entity"
                        @action="(k, e) => emit('action', k, e, row)"
                    />
                    <div
                        v-if="showSelection"
                        class="flex shrink-0 items-center"
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
                    v-if="linkedItems.length"
                    class="text-sm"
                    @click.stop
                    @dblclick.stop
                >
                    <PanoplyEquipmentTextList :items="linkedItems" :table-meta="tableMeta" />
                </div>
                <div class="flex flex-wrap items-center gap-2 text-sm">
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

    </div>
</template>
