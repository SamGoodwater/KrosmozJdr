<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuCollapsibleSection Atom (Navigation Glass)
 *
 * @description
 * Section repliable avec style titre (uppercase, gris, centré).
 * - Ressemble à GlassMenuSectionTitle, pas aux items du menu
 * - Flèche visible uniquement au survol
 * - Ouvert par défaut
 *
 * @example
 * <GlassMenuCollapsibleSection :default-open="true">Règles</GlassMenuCollapsibleSection>
 *   <GlassMenuItem href="/regles">Chapitre 1</GlassMenuItem>
 * </GlassMenuCollapsibleSection>
 *
 * @props {Boolean} defaultOpen - Ouvert par défaut
 * @props {Boolean} compact - Réduit la hauteur du titre
 * @slot default - Contenu repliable
 */
import { ref, watch, computed } from 'vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    defaultOpen: { type: Boolean, default: true },
    compact: { type: Boolean, default: false },
});

const isOpen = ref(props.defaultOpen);

watch(
    () => props.defaultOpen,
    (open) => {
        if (open) isOpen.value = true;
    }
);

const headerClasses = computed(() =>
    mergeClasses(
        [
            'glass-menu-collapsible-section-header',
            props.compact && 'glass-menu-collapsible-section-header-compact',
        ],
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));

function toggle() {
    isOpen.value = !isOpen.value;
}
</script>

<template>
    <div
        class="glass-menu-collapsible-section"
        :class="{ 'is-open': isOpen }"
        v-bind="attrs"
        v-on="$attrs"
    >
        <button
            type="button"
            :class="headerClasses"
            :aria-expanded="isOpen"
            aria-controls="collapsible-content"
            @click="toggle"
        >
            <span class="glass-menu-collapsible-section-title">
                <slot name="title" />
            </span>
            <span
                class="glass-menu-collapsible-section-caret"
                aria-hidden="true"
            >
                <i class="fa-solid fa-chevron-down glass-menu-collapsible-section-caret-icon"></i>
            </span>
        </button>
        <div
            v-show="isOpen"
            id="collapsible-content"
            class="glass-menu-collapsible-section-content"
            :class="{ 'glass-menu-collapsible-section-content-compact': props.compact }"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped lang="scss">
.glass-menu-collapsible-section {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.glass-menu-collapsible-section-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    width: 100%;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: color-mix(in srgb, var(--color-base-content) 55%, transparent);
    text-align: center;
    padding: 0.25rem 0.5rem;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: color 0.2s ease;
}

.glass-menu-collapsible-section-header:hover {
    color: color-mix(in srgb, var(--color-base-content) 72%, transparent);
}

.glass-menu-collapsible-section-header-compact {
    font-size: 0.65rem;
    padding: 0.15rem 0.45rem;
}

/* Flèche visible : toujours pour indiquer la rétractabilité */
.glass-menu-collapsible-section-caret {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 0.85rem;
    height: 0.85rem;
    opacity: 0.5;
    transform-origin: center;
    transition: transform 0.2s ease, opacity 0.15s ease;
}

.glass-menu-collapsible-section:not(.is-open) .glass-menu-collapsible-section-caret {
    transform: rotate(-90deg);
}

.glass-menu-collapsible-section.is-open .glass-menu-collapsible-section-caret {
    transform: rotate(0deg);
}

.glass-menu-collapsible-section-header:hover .glass-menu-collapsible-section-caret {
    opacity: 0.75;
}

.glass-menu-collapsible-section-caret-icon {
    font-size: 0.6rem;
    line-height: 1;
}

.glass-menu-collapsible-section-content {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding-left: 0.5rem;
}

.glass-menu-collapsible-section-content-compact {
    gap: 0.25rem;
    padding-left: 0.4rem;
}
</style>
