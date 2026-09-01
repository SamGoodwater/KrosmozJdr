<script setup>
/**
 * EntityMinimalCard — Composant de base pour les cartes Minimal.
 *
 * @description
 * Gère l'affichage compact par défaut et l'expansion au survol en overlay.
 * L'overlay ne modifie pas le flux du DOM : la carte conserve sa place,
 * le contenu étendu passe par-dessus le reste (z-index &lt; tooltips).
 * En `display-mode="extended"` (popover, favoris) : une seule carte, hauteur du contenu.
 * Un tooltip ouvert (OverlayTrigger téléporté) maintient la carte déployée.
 *
 * @slot compact - Contenu toujours visible, définit la taille du slot dans la grille
 * @slot expanded - Contenu affiché au hover (ou toujours si display-mode="extended")
 *
 * @props displayMode - 'hover' : expansion au survol | 'extended' : toujours étendu | 'compact' : jamais étendu
 * @props surfaceStyle - style de surface (teinte `--bg-color`, dégradé multi-élément)
 */
import { ref, computed, onMounted, onUnmounted } from "vue";
import { provideEntityMinimalCardOverlayHold } from "@/Composables/overlay/entityMinimalCardOverlayHold";

const props = defineProps({
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    /** Conservé pour BC API — l’épinglage ouvre désormais une fenêtre flottante (PinnedEntitiesHost). */
    pinnedEntityType: {
        type: String,
        default: "",
    },
    pinnedEntityId: {
        type: [String, Number],
        default: "",
    },
    /**
     * Style de surface (ex. teinte élément via `--bg-color`).
     * Appliqué au compact et à l’overlay étendu.
     */
    surfaceStyle: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["open-quick-view"]);

const isHovered = ref(props.displayMode === "extended");
const isFocusWithin = ref(false);
const isExpandedLocked = ref(false);
const cardRef = ref(null);
const overlayHoldCount = provideEntityMinimalCardOverlayHold();

const showExpanded = computed(() => {
    if (props.displayMode === "compact") return false;
    if (props.displayMode === "extended") return true;
    return (
        isExpandedLocked.value ||
        isHovered.value ||
        isFocusWithin.value ||
        overlayHoldCount.value > 0
    );
});

const canHover = computed(() => props.displayMode === "hover");

function onEnter() {
    if (canHover.value) isHovered.value = true;
}

function onLeave() {
    if (canHover.value) isHovered.value = false;
}

function onFocusIn() {
    if (canHover.value) isFocusWithin.value = true;
}

function onFocusOut(event) {
    if (!canHover.value) return;
    const nextTarget = event.relatedTarget;
    if (event.currentTarget?.contains?.(nextTarget)) return;
    isFocusWithin.value = false;
}

function onCardClick() {
    if (!canHover.value) return;
    isExpandedLocked.value = true;
    isHovered.value = true;
}

/** Double-clic → ouverture modal full (quick-view), hors zone d’actions. */
function onCardDblClick(event) {
    if (event.target?.closest?.("[data-entity-actions]")) return;
    if (event.target?.closest?.("a[href]")) return;
    emit("open-quick-view");
}

function unlockExpanded() {
    isExpandedLocked.value = false;
}

function onDocumentPointerDown(event) {
    if (!canHover.value || !isExpandedLocked.value) return;
    const root = cardRef.value;
    if (!root) return;
    if (root.contains(event.target)) return;
    unlockExpanded();
}

function onDocumentKeydown(event) {
    if (!canHover.value || !isExpandedLocked.value) return;
    if (event.key !== "Escape") return;
    unlockExpanded();
}

onMounted(() => {
    document.addEventListener("pointerdown", onDocumentPointerDown, true);
    document.addEventListener("keydown", onDocumentKeydown);
});

onUnmounted(() => {
    document.removeEventListener("pointerdown", onDocumentPointerDown, true);
    document.removeEventListener("keydown", onDocumentKeydown);
});
</script>

<template>
    <div
        ref="cardRef"
        class="entity-minimal-card group relative w-full"
        :class="{
            'entity-minimal-card--expanded': showExpanded && canHover,
            'entity-minimal-card--extended': displayMode === 'extended',
        }"
        @mouseenter="onEnter"
        @mouseleave="onLeave"
        @focusin="onFocusIn"
        @focusout="onFocusOut"
        @click="onCardClick"
        @dblclick="onCardDblClick"
    >
        <!-- Compact : réserve la place en grille. Absent en extended (une seule carte, hauteur du contenu). -->
        <div
            v-if="displayMode !== 'extended'"
            class="entity-minimal-card__compact bg-glass-2xl border border-base-300 overflow-hidden"
            :class="{ 'opacity-0 pointer-events-none': showExpanded && canHover }"
            :style="surfaceStyle"
        >
            <slot name="compact" />
        </div>

        <!-- Expanded : overlay au survol ; en flux si display-mode="extended" -->
        <Transition name="entity-minimal-expand">
            <div
                v-if="showExpanded"
                class="entity-minimal-card__expanded bg-glass-3xl"
                :class="{ 'entity-minimal-card__expanded--overlay': canHover }"
                :style="surfaceStyle"
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
    --bg-color: var(--color-base-100, #0f172a);
    min-height: 6rem;
    transition: opacity 0.15s ease-out;
    backdrop-filter: blur(34px) saturate(1.12);
    -webkit-backdrop-filter: blur(34px) saturate(1.12);
    box-shadow:
        0 14px 34px rgb(0 0 0 / 0.34),
        0 0 0 1px color-mix(in srgb, var(--color-base-content, #ffffff) 8%, transparent) inset,
        0 1px 0 color-mix(in srgb, var(--color-base-content, #ffffff) 10%, transparent) inset;
}

/* Quand étendu au survol : la carte passe au-dessus des voisines */
.entity-minimal-card--expanded {
    z-index: 100;
}

.entity-minimal-card__expanded {
    --bg-color: var(--color-base-100, #0f172a);
    box-sizing: border-box;
    overflow-y: auto;
    overflow-x: hidden;
    max-height: 80vh;
    border-radius: var(--rounded-box, 0.1rem);
    border: 1px solid var(--color-base-300, rgb(51 65 85));
    backdrop-filter: blur(38px) saturate(1.15);
    -webkit-backdrop-filter: blur(38px) saturate(1.15);
    box-shadow:
        0 28px 80px rgb(0 0 0 / 0.58),
        0 10px 28px rgb(0 0 0 / 0.34),
        0 0 0 1px color-mix(in srgb, var(--color-base-content, #ffffff) 12%, transparent) inset,
        0 1px 0 color-mix(in srgb, var(--color-base-content, #ffffff) 16%, transparent) inset;
}

/* Survol en grille : même largeur que le compact, sans décaler le flux */
.entity-minimal-card__expanded--overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    max-width: 100%;
    z-index: 1;
}

/* Actions masquées par défaut, visibles au survol / focus de la carte */
.entity-minimal-card :deep([data-entity-actions]) {
    max-width: 0;
    min-width: 0;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
    transition:
        max-width 0.15s ease,
        opacity 0.15s ease;
}
.entity-minimal-card:hover :deep([data-entity-actions]),
.entity-minimal-card:focus-within :deep([data-entity-actions]),
.entity-minimal-card--extended :deep([data-entity-actions]),
/* Menu téléporté : survol perdu entre trigger et panneau */
.entity-minimal-card :deep([data-entity-actions]:has([data-dropdown-open="true"])),
.entity-minimal-card :deep([data-entity-actions]:has([aria-expanded="true"])) {
    max-width: 12rem;
    opacity: 1;
    pointer-events: auto;
    overflow: visible;
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
