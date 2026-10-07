<script setup>
/**
 * Habillage d’une propriété de degré, aligné sur les champs généraux du sort.
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getByDbColumn } from '@/Composables/store/useCharacteristicsStore';
import { getCharacteristicColorStyle, getCharacteristicContainerStyle } from '@/Utils/color/Color';

const props = defineProps({
    characteristicKey: { type: String, default: '' },
    label: { type: String, required: true },
    icon: { type: String, default: 'fa-solid fa-sliders' },
    helper: { type: String, default: '' },
});

const meta = computed(() =>
    props.characteristicKey ? getByDbColumn('spell', props.characteristicKey) : null,
);
const color = computed(() => meta.value?.color || null);
const containerStyle = computed(() =>
    color.value ? getCharacteristicContainerStyle(color.value) || {} : {},
);
const labelStyle = computed(() =>
    color.value ? getCharacteristicColorStyle(color.value) || {} : {},
);
const iconSource = computed(() => meta.value?.icon || props.icon);
</script>

<template>
    <div
        class="min-w-0 rounded-lg border border-base-300/60 bg-base-100/35 p-2.5"
        :style="containerStyle"
    >
        <div class="mb-1.5 flex min-h-5 items-center gap-1.5">
            <Icon :source="iconSource" :alt="label" size="xs" :style="labelStyle" />
            <span class="text-xs font-semibold text-base-content/80" :style="labelStyle">
                {{ label }}
            </span>
        </div>
        <slot />
        <p v-if="helper" class="mt-1 text-xs leading-snug text-base-content/70">
            {{ helper }}
        </p>
    </div>
</template>
