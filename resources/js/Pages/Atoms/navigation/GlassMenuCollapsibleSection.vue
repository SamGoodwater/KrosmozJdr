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
 * @props {('group'|'parent')} variant - `group` : libellé de section (majuscules, gris, centré). `parent` : ligne comme une entrée de navigation (alignée à gauche, titre naturel).
 * @props {String} icon - Chemin icône entité (ex. icons/entities/specialization.webp) ou FA, affiché en variant parent
 * @props {String} iconAlt - Texte alternatif de l'icône
 */
import { ref, watch, computed, onMounted } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
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
    icon: { type: String, default: '' },
    iconAlt: { type: String, default: '' },
});

const contentDomId = computed(() => {
    const raw = props.sectionId || 'menu-section';
    const slug = String(raw).replace(/[^a-zA-Z0-9_-]/g, '-');
    return `glass-menu-collapsible-content-${slug}`;
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
            props.variant === 'group' && 'glass-menu-collapsible-section-header--group',
            props.variant === 'parent' && 'glass-menu-collapsible-section-header--parent',
            props.compact && 'glass-menu-collapsible-section-header--compact',
        ],
        props.class
    )
);

const rootClasses = computed(() => [
    'glass-menu-collapsible-section',
    props.variant === 'parent' && 'glass-menu-collapsible-section--parent',
]);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div
        :class="[...rootClasses, { 'is-open': isOpen }]"
        v-bind="attrs"
        v-on="$attrs"
    >
        <button
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
            <span
                class="glass-menu-collapsible-section-caret"
                aria-hidden="true"
            >
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

/* Section menu (regroupement thématique) : plus lisible, gris, majuscules, intermédiaire / léger */
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

/* Page parente : même famille visuelle qu’un lien du menu, ligne cliquable */
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

.glass-menu-collapsible-section-content--parent {
    gap: 0.28rem;
    padding-left: 0.55rem;
    margin-left: 0.25rem;
    border-left: 1px solid color-mix(in srgb, var(--color-base-content) 14%, transparent);
}

.glass-menu-collapsible-section-content--parent.glass-menu-collapsible-section-content-compact {
    gap: 0.22rem;
    padding-left: 0.5rem;
}

/* Chevron un peu plus discret sur les groupes */
.glass-menu-collapsible-section-header--group .glass-menu-collapsible-section-caret {
    opacity: 0.45;
}

.glass-menu-collapsible-section-header--parent .glass-menu-collapsible-section-caret {
    opacity: 0.55;
}
</style>
