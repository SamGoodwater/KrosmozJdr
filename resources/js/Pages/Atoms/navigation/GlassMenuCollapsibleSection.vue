<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuCollapsibleSection Atom (Navigation Glass)
 *
 * @description
 * Section repliable avec style titre (uppercase, gris, centré).
 * Variant `parent` : texte = lien page ; reste de la ligne (icône, fond, chevron) = toggle collapse.
 * Si sectionId est fourni, l'état ouvert/fermé est persisté dans sessionStorage.
 *
 * @props {String} sectionId - Identifiant pour persistance (sessionStorage)
 * @props {Boolean} defaultOpen - Ouvert par défaut (si pas de valeur persistée)
 * @props {Boolean} compact - Réduit la hauteur du titre
 * @props {('group'|'parent')} variant - `group` : libellé de section. `parent` : page parente avec collapse.
 * @props {String} parentHref - URL de la page parente (variant parent)
 * @props {String} parentLabel - Libellé pour tooltips (variant parent)
 * @props {String} icon - Chemin icône entité ou FA (variant parent)
 * @props {String} iconAlt - Texte alternatif de l'icône
 */
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
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
    variant: {
        type: String,
        default: 'group',
        validator: (v) => ['group', 'parent'].includes(v),
    },
    parentHref: { type: String, default: '' },
    parentLabel: { type: String, default: '' },
    icon: { type: String, default: '' },
    iconAlt: { type: String, default: '' },
});

const contentDomId = computed(() => {
    const raw = props.sectionId || 'menu-section';
    const slug = String(raw).replace(/[^a-zA-Z0-9_-]/g, '-');
    return `glass-menu-collapsible-content-${slug}`;
});

const isOpen = ref(true);
const isOpening = ref(false);
let openingTimer = null;

const isParentSplit = computed(
    () => props.variant === 'parent' && String(props.parentHref || '').trim() !== '',
);

const pageTooltip = computed(() => {
    const label = String(props.parentLabel || '').trim();
    return label !== '' ? `Aller à ${label}` : 'Aller à la page parente';
});

const collapseTooltip = computed(() =>
    isOpen.value ? 'Replier les sous-pages' : 'Afficher les sous-pages',
);

function clearOpeningTimer() {
    if (openingTimer != null) {
        clearTimeout(openingTimer);
        openingTimer = null;
    }
}

function triggerOpeningAnimation() {
    isOpening.value = true;
    clearOpeningTimer();
    openingTimer = setTimeout(() => {
        isOpening.value = false;
        openingTimer = null;
    }, 720);
}

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

onUnmounted(() => {
    clearOpeningTimer();
});

watch(
    () => [props.sectionId, props.defaultOpen],
    () => initState(),
);

watch(
    () => props.defaultOpen,
    (open) => {
        if (open && !props.sectionId) isOpen.value = true;
    },
);

function toggle() {
    const willOpen = !isOpen.value;
    isOpen.value = willOpen;
    if (props.sectionId) {
        setPersistedState(props.sectionId, isOpen.value);
    }
    if (willOpen) {
        triggerOpeningAnimation();
    } else {
        isOpening.value = false;
        clearOpeningTimer();
    }
}

const headerClasses = computed(() =>
    mergeClasses(
        [
            'glass-menu-collapsible-section-header',
            !isParentSplit.value && 'glass-menu-hover-accent-b-md',
            props.variant === 'group' && 'glass-menu-collapsible-section-header--group',
            props.variant === 'parent' && 'glass-menu-collapsible-section-header--parent',
            props.compact && 'glass-menu-collapsible-section-header--compact',
            isParentSplit.value && 'glass-menu-collapsible-section-header--parent-split',
        ],
        props.class,
    ),
);

const rootClasses = computed(() => [
    'glass-menu-collapsible-section',
    props.variant === 'parent' && 'glass-menu-collapsible-section--parent',
]);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div
        :class="[...rootClasses, { 'is-open': isOpen, 'is-opening': isOpening }]"
        v-bind="attrs"
        v-on="$attrs"
    >
        <!-- Variant parent : texte → page ; toute la ligne sauf le texte → collapse -->
        <div
            v-if="isParentSplit"
            :class="headerClasses"
            role="presentation"
        >
            <Tooltip :content="collapseTooltip" placement="right" class="glass-menu-parent-header-toggle-wrap">
                <button
                    type="button"
                    class="glass-menu-parent-header-toggle"
                    :aria-expanded="isOpen"
                    :aria-controls="contentDomId"
                    :aria-label="collapseTooltip"
                    @click="toggle"
                >
                    <span class="glass-menu-collapsible-section-caret" aria-hidden="true">
                        <i class="fa-solid fa-chevron-down glass-menu-collapsible-section-caret-icon"></i>
                    </span>
                </button>
            </Tooltip>

            <div class="glass-menu-parent-header-label">
                <Icon
                    v-if="icon"
                    :source="icon"
                    :alt="iconAlt || ''"
                    size="sm"
                    class="glass-menu-collapsible-section-icon"
                />
                <Tooltip :content="pageTooltip" placement="right" class="glass-menu-parent-header-link-wrap">
                    <Route
                        :href="parentHref"
                        class="glass-menu-parent-header-link glass-menu-hover-accent-b-md"
                        :aria-label="pageTooltip"
                    >
                        <span class="glass-menu-parent-header-link-text">
                            <slot name="title" />
                        </span>
                    </Route>
                </Tooltip>
            </div>
        </div>

        <!-- Variant group ou parent sans href : bouton unique -->
        <button
            v-else
            type="button"
            :class="headerClasses"
            :aria-expanded="isOpen"
            :aria-controls="contentDomId"
            @click="toggle"
        >
            <span
                class="glass-menu-collapsible-section-title"
                :class="{
                    'glass-menu-collapsible-section-title--parent': variant === 'parent',
                }"
            >
                <Icon
                    v-if="variant === 'parent' && icon"
                    :source="icon"
                    :alt="iconAlt || ''"
                    size="sm"
                    class="glass-menu-collapsible-section-icon"
                />
                <slot name="title" />
            </span>
            <span class="glass-menu-collapsible-section-caret" aria-hidden="true">
                <i class="fa-solid fa-chevron-down glass-menu-collapsible-section-caret-icon"></i>
            </span>
        </button>

        <div
            v-show="isOpen"
            :id="contentDomId"
            class="glass-menu-collapsible-section-content"
            :class="{
                'glass-menu-collapsible-section-content-compact': props.compact,
                'glass-menu-collapsible-section-content--parent': props.variant === 'parent',
                'glass-border-l-md': props.variant === 'parent',
            }"
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
    gap: 0.35rem;
    width: 100%;
    background: transparent;
    border: none;
    cursor: pointer;
    transition:
        color 0.2s ease,
        background 0.18s ease;
}

.glass-menu-collapsible-section-header--parent-split {
    position: relative;
    gap: 0;
    padding: 0;
    cursor: default;
    min-height: 1.85rem;
}

.glass-menu-parent-header-toggle-wrap {
    position: absolute;
    inset: 0;
    z-index: 0;
    display: block;
    width: 100%;
    min-width: 0;
}

.glass-menu-parent-header-toggle {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    height: 100%;
    min-height: 1.85rem;
    padding: 0.3rem 0.45rem;
    border: none;
    border-radius: var(--radius-field, 0.25rem);
    background: transparent;
    color: color-mix(in srgb, var(--color-base-content) 72%, transparent);
    cursor: pointer;
    transition:
        color 0.18s ease,
        background 0.18s ease;
}

.glass-menu-parent-header-toggle:hover,
.glass-menu-parent-header-toggle:focus-visible {
    background: color-mix(in srgb, var(--color-base-100) 24%, transparent);
    color: var(--color-base-content);
}

.glass-menu-parent-header-label {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    min-width: 0;
    max-width: calc(100% - 1.5rem);
    padding: 0.3rem 0.45rem;
    pointer-events: none;
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: color-mix(in srgb, var(--color-base-content) 88%, transparent);
}

.glass-menu-parent-header-link-wrap {
    min-width: 0;
    max-width: 100%;
}

.glass-menu-parent-header-link {
    display: inline-flex;
    max-width: 100%;
    border-radius: var(--radius-field, 0.25rem);
    text-decoration: none;
    color: inherit;
    pointer-events: auto;
    transition:
        color 0.18s ease,
        background 0.18s ease;
}

.glass-menu-parent-header-link:hover,
.glass-menu-parent-header-link:focus-visible {
    background: color-mix(in srgb, var(--color-base-100) 32%, transparent);
    color: var(--color-base-content);
}

.glass-menu-parent-header-link-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.glass-menu-collapsible-section-header--parent-split.glass-menu-collapsible-section-header--compact {
    min-height: 1.75rem;

    .glass-menu-parent-header-toggle,
    .glass-menu-parent-header-label {
        min-height: 1.75rem;
        padding: 0.24rem 0.45rem;
        font-size: 0.8rem;
    }
}

.glass-menu-collapsible-section-header--group {
    justify-content: center;
    text-align: center;
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.11em;
    text-transform: uppercase;
    color: color-mix(in srgb, var(--color-base-content) 52%, transparent);
    padding: 0.32rem 0.55rem;
}

.glass-menu-collapsible-section-header--group:hover {
    color: color-mix(in srgb, var(--color-base-content) 68%, transparent);
}

.glass-menu-collapsible-section-header--group.glass-menu-collapsible-section-header--compact {
    font-size: 0.74rem;
    padding: 0.26rem 0.48rem;
    letter-spacing: 0.1em;
}

.glass-menu-collapsible-section-header--parent {
    justify-content: space-between;
    text-align: left;
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: none;
    color: color-mix(in srgb, var(--color-base-content) 88%, transparent);
    padding: 0.3rem 0.5rem;
    min-height: 1.85rem;
    border-radius: var(--radius-field, 0.25rem);
}

.glass-menu-collapsible-section-header--parent:hover {
    background: color-mix(in srgb, var(--color-base-100) 32%, transparent);
    color: var(--color-base-content);
}

.glass-menu-collapsible-section-header--parent.glass-menu-collapsible-section-header--compact {
    font-size: 0.8rem;
    min-height: 1.75rem;
    padding: 0.24rem 0.45rem;
}

.glass-menu-collapsible-section-title--parent {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex: 1;
    min-width: 0;
    text-align: left;
}

.glass-menu-collapsible-section-icon {
    flex-shrink: 0;
    opacity: 0.82;
}

.glass-menu-collapsible-section-caret {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 0.85rem;
    height: 0.85rem;
    opacity: 0.55;
}

.glass-menu-collapsible-section-caret-icon {
    font-size: 0.6rem;
    line-height: 1;
    transform: rotate(-90deg);
    transform-origin: center center;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.18s ease;
}

.glass-menu-collapsible-section.is-open .glass-menu-collapsible-section-caret-icon {
    transform: rotate(90deg);
}

.glass-menu-parent-header-toggle:hover .glass-menu-collapsible-section-caret-icon,
.glass-menu-parent-header-toggle:focus-visible .glass-menu-collapsible-section-caret-icon {
    animation: glass-menu-caret-hint 1.35s ease-in-out infinite;
}

@keyframes glass-menu-caret-hint {
    0%,
    100% {
        opacity: 0.55;
        transform: rotate(var(--caret-rotate, -90deg)) scale(1);
    }

    50% {
        opacity: 0.95;
        transform: rotate(var(--caret-rotate, -90deg)) scale(1.07);
    }
}

.glass-menu-collapsible-section.is-open .glass-menu-parent-header-toggle:hover .glass-menu-collapsible-section-caret-icon,
.glass-menu-collapsible-section.is-open .glass-menu-parent-header-toggle:focus-visible .glass-menu-collapsible-section-caret-icon {
    --caret-rotate: 90deg;
}

.glass-menu-collapsible-section:not(.is-open) .glass-menu-parent-header-toggle:hover .glass-menu-collapsible-section-caret-icon,
.glass-menu-collapsible-section:not(.is-open) .glass-menu-parent-header-toggle:focus-visible .glass-menu-collapsible-section-caret-icon {
    --caret-rotate: -90deg;
}

.glass-menu-collapsible-section-header:hover .glass-menu-collapsible-section-caret {
    opacity: 0.75;
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

.glass-menu-collapsible-section-content--parent {
    gap: 0.28rem;
    padding-left: 0.55rem;
    margin-left: 0.25rem;
}

.glass-menu-collapsible-section-content--parent.glass-menu-collapsible-section-content-compact {
    gap: 0.22rem;
    padding-left: 0.5rem;
}

.glass-menu-collapsible-section-header--group .glass-menu-collapsible-section-caret {
    opacity: 0.45;
}

@media (prefers-reduced-motion: reduce) {
    .glass-menu-collapsible-section-caret-icon,
    .glass-menu-parent-header-toggle:hover .glass-menu-collapsible-section-caret-icon,
    .glass-menu-parent-header-toggle:focus-visible .glass-menu-collapsible-section-caret-icon {
        animation: none;
        transition: none;
    }
}
</style>
