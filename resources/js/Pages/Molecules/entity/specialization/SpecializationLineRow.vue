<script setup>
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";

const props = defineProps({
    row: { type: Object, required: true },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
});

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const entity = computed(() => getRowEntity(props.row));
const imageUrl = computed(() => entity.value?.image || null);
const name = computed(() => entity.value?.name || "—");
const shortDescription = computed(() => entity.value?.short_description || entity.value?.description || "");
const traits = computed(() => Array.isArray(entity.value?.creatureTraits) ? entity.value.creatureTraits : []);
</script>

<template>
    <div
        class="group relative rounded-box border border-base-300 bg-glass-2xl p-3 flex gap-3 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <div class="w-16 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center">
            <Image v-if="imageUrl" :source="imageUrl" :alt="name" fit="contain" class="h-full w-full" />
            <Icon v-else source="fa-solid fa-graduation-cap" alt="" size="sm" class="text-base-content/40" />
        </div>

        <div class="flex-1 min-w-0 space-y-1">
            <div class="flex items-center gap-2">
                <span class="font-semibold truncate">{{ name }}</span>
                <span class="text-xs opacity-70">Cap: {{ entity?.capabilities_count ?? entity?.capabilities?.length ?? 0 }}</span>
                <span class="text-xs opacity-70">Sorts: {{ entity?.spells_count ?? entity?.spells?.length ?? 0 }}</span>
            </div>
            <p v-if="shortDescription" class="text-xs text-base-content/80 line-clamp-2">{{ shortDescription }}</p>
            <CreatureTraitBadges v-if="traits.length" :traits="traits" show-level size="xs" />
        </div>

        <EntityLineRowActions
            v-if="showActions"
            entity-type="specializations"
            :entity="entity"
            @action="(k, e) => emit('action', k, e, row)"
        />

        <div v-if="showSelection" class="flex items-center" @click.stop>
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
</template>
