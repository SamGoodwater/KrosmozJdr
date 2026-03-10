<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuCollapsibleSection Atom (Navigation Glass)
 *
 * @description
 * Section repliable avec style titre (uppercase, gris, centré).
 * Si sectionId est fourni, l'état ouvert/fermé est persisté dans sessionStorage.
 *
 * @example
 * <GlassMenuCollapsibleSection section-id="regles" :default-open="true">
 *   <GlassMenuItem href="/regles">Chapitre 1</GlassMenuItem>
 * </GlassMenuCollapsibleSection>
 *
 * @props {String} sectionId - Identifiant pour persistance (sessionStorage)
 * @props {Boolean} defaultOpen - Ouvert par défaut (si pas de valeur persistée)
 * @props {Boolean} compact - Réduit la hauteur du titre
 */
import { ref, watch, computed, onMounted } from 'vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const STORAGE_KEY = 'dynamic-menu-sections';

function getPersistedState(sectionId) {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (!raw) return null;
        const data = JSON.parse(raw);
        return data[sectionId] ?? null;
    } catch {
        return null;
    }
}

function setPersistedState(sectionId, isOpen) {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY) || '{}';
        const data = { ...JSON.parse(raw), [sectionId]: isOpen };
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch {
        // ignore
    }
}

const props = defineProps({
    ...getCommonProps(),
    sectionId: { type: String, default: '' },
    defaultOpen: { type: Boolean, default: true },
    compact: { type: Boolean, default: false },
});

const isOpen = ref(true); // valeur temporaire, corrigée dans onMounted

function initState() {
    if (props.sectionId) {
        const stored = getPersistedState(props.sectionId);
        if (stored !== null) {
            isOpen.value = stored;
            return;
        }
    }
    isOpen.value = props.defaultOpen;
}

onMounted(initState);

watch(
    () => [props.sectionId, props.defaultOpen],
    () => initState()
);

watch(
    () => props.defaultOpen,
    (open) => {
        if (open && !props.sectionId) isOpen.value = true;
    }
);

function toggle() {
    isOpen.value = !isOpen.value;
    if (props.sectionId) {
        setPersistedState(props.sectionId, isOpen.value);
    }
}

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
