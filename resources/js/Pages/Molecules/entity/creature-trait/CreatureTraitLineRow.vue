<script setup>
/** CreatureTraitLineRow — ligne dense pour les tables d’entité. */
import { computed } from "vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRowEntity } from "@/Utils/Entity/rowEntity";

const props = defineProps({
    row: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    entityType: { type: String, default: "creature-traits" },
});

const emit = defineEmits(["row-click", "row-dblclick", "action"]);
const entity = computed(() => getRowEntity(props.row));
const value = (key) => entity.value?.[key] ?? entity.value?._data?.[key] ?? null;
</script>

<template>
    <div
        class="grid w-full cursor-pointer grid-cols-[minmax(0,1fr)_auto] items-center gap-3 rounded-lg px-3 py-2 text-left hover:bg-base-200/70"
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <span class="min-w-0">
            <span class="block truncate text-sm font-semibold text-base-content">{{ value('name') || '—' }}</span>
            <span v-if="value('description')" class="block truncate text-xs text-base-content/60">{{ value('description') }}</span>
        </span>
        <EntityLineRowActions
            v-if="showActions"
            :entity-type="entityType"
            :entity="entity"
            @action="(k, e) => emit('action', k, e)"
        />
    </div>
</template>
