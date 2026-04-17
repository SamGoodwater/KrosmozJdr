<script setup>
/**
 * Multi-sélection des types d'équipement (`item_types`) pour restriction des caractéristiques groupe object.
 * `v-model` : tableau d'ids numériques. Utilise SelectSearchField (recherche, badges + croix).
 *
 * @example
 * <ItemTypesMultiField v-model="ent.item_type_ids" :options="itemTypesFromServer" />
 */
import { computed } from 'vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';

const props = defineProps({
    /** @type {Array<{ id: number, name: string, dofusdb_type_id: number }>} */
    options: { type: Array, default: () => [] },
    modelValue: { type: Array, default: () => [] },
    label: { type: [String, Object], default: "Types d'équipement autorisés" },
    helper: {
        type: [String, Object],
        default:
            "Liste vide : tous les types d'objets. Sinon restriction aux types choisis (pivot characteristic_object_item_type).",
    },
    disabled: { type: Boolean, default: false },
    placeholder: { type: String, default: "Rechercher un type d'équipement…" },
});

const emit = defineEmits(['update:modelValue']);

const ids = computed(() =>
    Array.isArray(props.modelValue) ? props.modelValue.map((n) => Number(n)).filter((n) => Number.isFinite(n)) : [],
);

const selectOptions = computed(() =>
    [...(props.options ?? [])]
        .sort((a, b) => String(a.name ?? '').localeCompare(String(b.name ?? ''), 'fr'))
        .map((t) => ({
            value: Number(t.id),
            label: `${t.name} (DofusDB ${t.dofusdb_type_id})`,
        })),
);

/**
 * @param {unknown} val
 */
function onUpdate(val) {
    const arr = (Array.isArray(val) ? val : [val])
        .map((n) => Number(n))
        .filter((n) => Number.isFinite(n));
    const sorted = [...new Set(arr)].sort((a, b) => a - b);
    emit('update:modelValue', sorted);
}
</script>

<template>
    <SelectSearchField
        :model-value="ids"
        :options="selectOptions"
        :label="label"
        :helper="helper"
        :disabled="disabled"
        :searchable="selectOptions.length > 6"
        :placeholder="placeholder"
        multiple
        @update:model-value="onUpdate"
    />
</template>
