<script setup>
/**
 * Badge outline pour un type de sort : icône dédiée + ombre teintée.
 *
 * @see resources/js/Utils/Entity/spellTypeVisual.js
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { resolveSpellTypeVisual } from '@/Utils/Entity/spellTypeVisual.js';

const props = defineProps({
    /** Libellé affiché (nom du type) */
    name: { type: String, required: true },
    /** Couleur optionnelle depuis l’API (#rrggbb) si le nom ne correspond pas au thème */
    color: { type: String, default: null },
    /**
     * Indice d’icône BDD (`spell_types.icon`) : clé orientation (`degats`, …) ou ancien slug FontAwesome (`sword`, …).
     */
    iconHint: { type: String, default: null },
    /** sm | md */
    size: { type: String, default: 'sm' },
    /** Afficher le libellé du type à côté de l’icône */
    showLabel: { type: Boolean, default: true },
});

const visual = computed(() =>
    resolveSpellTypeVisual(props.name, props.color, props.iconHint),
);

/** Libellé pour infobulle / accessibilité (icône seule). */
const tooltipLabel = computed(() => {
    const n = String(props.name ?? "").trim();
    return n || "Type de sort";
});

const shellClass = computed(() => {
    const base =
        'inline-flex max-w-full min-w-0 items-center gap-1.5 rounded-full border border-base-300/80 bg-base-100/90 font-medium text-base-content';
    if (!props.showLabel) {
        return `${base} p-0.5`;
    }
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
    <Tooltip
        v-if="!showLabel"
        :content="tooltipLabel"
        placement="top"
        class="inline-flex max-w-full min-w-0"
        :tabindex="-1"
    >
        <span
            :class="shellClass"
            :style="shellStyle"
            class="cursor-help"
            :title="tooltipLabel"
            role="img"
            :aria-label="tooltipLabel"
            data-no-row-select
        >
            <img
                v-if="visual.iconUrl"
                :src="visual.iconUrl"
                alt=""
                class="shrink-0 object-contain"
                :class="imgClass"
                loading="lazy"
            />
            <Icon v-else source="fa-solid fa-tag" alt="" size="xs" class="shrink-0 opacity-70" />
        </span>
    </Tooltip>
    <span v-else :class="shellClass" :style="shellStyle">
        <img
            v-if="visual.iconUrl"
            :src="visual.iconUrl"
            :alt="name"
            class="shrink-0 object-contain"
            :class="imgClass"
            loading="lazy"
        />
        <Icon v-else source="fa-solid fa-tag" :alt="name" size="xs" class="shrink-0 opacity-70" />
        <span class="truncate leading-tight">{{ name }}</span>
    </span>
</template>
