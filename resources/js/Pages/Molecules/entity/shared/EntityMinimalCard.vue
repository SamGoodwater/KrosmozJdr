<script setup>
/**
 * EntityMinimalCard — Composant de base pour les cartes Minimal.
 *
 * @description
 * Gère l'affichage compact par défaut et l'expansion au survol en overlay.
 * L'overlay ne modifie pas le flux du DOM : la carte conserve sa place,
 * le contenu étendu passe par-dessus le reste (z-index &lt; tooltips).
 *
 * @slot compact - Contenu toujours visible, définit la taille du slot dans la grille
 * @slot expanded - Contenu affiché au hover (ou toujours si display-mode="extended")
 *
 * @props displayMode - 'hover' : expansion au survol | 'extended' : toujours étendu | 'compact' : jamais étendu
 */
import { ref, computed } from "vue";

const props = defineProps({
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
});

const isHovered = ref(props.displayMode === "extended");

const showExpanded = computed(() => {
    if (props.displayMode === "compact") return false;
    if (props.displayMode === "extended") return true;
    return isHovered.value;
});

const canHover = computed(() => props.displayMode === "hover");

function onEnter() {
    if (canHover.value) isHovered.value = true;
}

function onLeave() {
    if (canHover.value) isHovered.value = false;
}
</script>

<template>
    <div
        class="entity-minimal-card relative w-full"
        :class="{ 'entity-minimal-card--expanded': showExpanded && canHover }"
        @mouseenter="onEnter"
        @mouseleave="onLeave"
    >
        <!-- Compact : définit la taille du slot, ne bouge pas -->
        <div
            class="entity-minimal-card__compact border border-base-300 overflow-hidden"
            :class="{ 'opacity-0 pointer-events-none': showExpanded && canHover }"
        >
            <slot name="compact" />
        </div>

        <!-- Expanded : overlay au survol, ne modifie pas le flux -->
        <Transition name="entity-minimal-expand">
            <div
                v-if="showExpanded"
                class="entity-minimal-card__expanded"
                role="region"
                aria-label="Détails"
            >
                <slot name="expanded" />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.entity-minimal-card__compact {
    min-height: 6rem;
    transition: opacity 0.15s ease-out;
    background: oklch(var(--b1) / 0.5);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
}

/* Quand étendu au survol : la carte passe au-dessus des voisines */
.entity-minimal-card--expanded {
    z-index: 100;
}

.entity-minimal-card__expanded {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    max-width: 100%;
    /* Taille fixe : même largeur que le compact, pas d’élargissement */
    box-sizing: border-box;
    overflow-y: auto;
    overflow-x: hidden;
    max-height: 80vh;
    z-index: 1;
    /* Sous les tooltips (typiquement 9999) */
    border-radius: var(--rounded-box, 0.1rem);
    border: 1px solid var(--color-base-300, oklch(var(--b3)));
    background: oklch(var(--b1) / 0.95);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    box-shadow: 0 8px 24px oklch(0 0 0 / 0.15);
}

/* Masquer les actions par défaut (aucune place), les afficher au survol */
.entity-minimal-card :deep([data-entity-actions]) {
    max-width: 0;
    min-width: 0;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
    transition: max-width 0.15s ease, opacity 0.15s ease;
}
.entity-minimal-card:hover :deep([data-entity-actions]) {
    max-width: 3rem;
    opacity: 1;
    pointer-events: auto;
}

.entity-minimal-expand-enter-active,
.entity-minimal-expand-leave-active {
    transition: opacity 0.15s ease-out;
}

.entity-minimal-expand-enter-from,
.entity-minimal-expand-leave-to {
    opacity: 0;
}
</style>
