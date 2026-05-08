<script setup>
/** ConditionLineRow — ligne dense pour les tables d'entité (états). */
import ConditionDissipableHighlight from "@/Pages/Molecules/entity/condition/ConditionDissipableHighlight.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";

const props = defineProps({
    row: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    entityType: { type: String, default: "conditions" },
});

const emit = defineEmits(["row-click", "action"]);
const entity = () => props.row?.rowParams?.entity ?? props.row;
const value = (key) => entity()?.[key] ?? entity()?._data?.[key] ?? null;
</script>

<template>
    <button
        type="button"
        class="grid w-full grid-cols-[minmax(0,1fr)_auto] items-center gap-3 rounded-lg px-3 py-2 text-left hover:bg-base-200/70"
        @click="emit('row-click', row)"
    >
        <span class="min-w-0 flex items-center gap-2">
            <ConditionDissipableHighlight
                :dissipable="value('dissipable')"
                variant="icon-only"
                :show-label="false"
                class="shrink-0"
            />
            <span class="min-w-0 flex flex-col">
            <span class="block truncate text-sm font-semibold text-base-content">{{ value('name') || '—' }}</span>
            <span v-if="value('description')" class="block truncate text-xs text-base-content/60">{{ value('description') }}</span>
            </span>
        </span>
        <span v-if="showActions" @click.stop>
            <EntityActions :entity-type="entityType" :entity="entity()" format="dropdown" @action="(action) => emit('action', { action, entity: entity() })" />
        </span>
    </button>
</template>
