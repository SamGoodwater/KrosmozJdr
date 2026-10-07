<script setup>
/**
 * Contexte caractéristique / élément pour un sous-effet de sort.
 */
import { computed } from 'vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import { getByCharacteristicKey } from '@/Composables/store/useCharacteristicsStore';

const props = defineProps({
    modelValue: { type: [String, null], default: '' },
    options: { type: Array, default: () => [] },
    label: { type: String, default: 'Caractéristique' },
    helper: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const enrichedOptions = computed(() =>
    (props.options || []).map((c) => {
        const meta =
            getByCharacteristicKey('creature', c.key) ||
            getByCharacteristicKey('spell', c.key) ||
            getByCharacteristicKey('item', c.key);
        return {
            value: c.key,
            label: c.label ?? c.key,
            icon: meta?.icon || c.icon || null,
            color: meta?.color || c.color || null,
        };
    }),
);
</script>

<template>
    <SelectSearchField
        size="xs"
        :label="label"
        :helper="helper"
        :placeholder="label + '…'"
        :options="enrichedOptions"
        :model-value="modelValue"
        :searchable="enrichedOptions.length > 8"
        @update:model-value="emit('update:modelValue', $event)"
    />
</template>
