<script setup>
/**
 * Badge outline pour un type de sort : icône dédiée + ombre teintée.
 *
 * @see resources/js/Utils/Entity/spellTypeVisual.js
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { resolveSpellTypeVisual } from '@/Utils/Entity/spellTypeVisual.js';

const props = defineProps({
    /** Libellé affiché (nom du type) */
    name: { type: String, required: true },
    /** Couleur optionnelle depuis l’API (#rrggbb) si le nom ne correspond pas au thème */
    color: { type: String, default: null },
    /** sm | md */
    size: { type: String, default: 'sm' },
});

const visual = computed(() => resolveSpellTypeVisual(props.name, props.color));

const shellClass = computed(() => {
    const base =
        'inline-flex max-w-full min-w-0 items-center gap-1.5 rounded-full border border-base-300/80 bg-base-100/90 font-medium text-base-content';
    if (props.size === 'md') {
        return `${base} px-2.5 py-1 text-sm`;
    }
    return `${base} px-2 py-0.5 text-xs`;
});

const imgClass = computed(() => (props.size === 'md' ? 'h-4 w-4' : 'h-3.5 w-3.5'));

const shellStyle = computed(() => {
    const hex = visual.value.hex;
    return {
        borderColor: `${hex}99`,
        boxShadow: `0 0 0 1px ${hex}22, 0 2px 8px -2px ${hex}55`,
    };
});
</script>

<template>
    <span :class="shellClass" :style="shellStyle">
        <img
            v-if="visual.iconUrl"
            :src="visual.iconUrl"
            :alt="''"
            class="shrink-0 object-contain"
            :class="imgClass"
            loading="lazy"
        />
        <Icon v-else source="fa-solid fa-tag" alt="Type" size="xs" class="shrink-0 opacity-70" />
        <span class="truncate leading-tight">{{ name }}</span>
    </span>
</template>
