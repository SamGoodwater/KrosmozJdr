<script setup>
/**
 * DofusDbReferencePanel Molecule
 *
 * @description
 * Panneau flottant Pinia : iframe DofusDB, déplaçable (header) et redimensionnable.
 * Pas d’ouverture auto de fenêtre externe — uniquement via le bouton / lien.
 */
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import interact from 'interactjs';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { useDofusDbReferenceStore } from '@/Composables/store/useDofusDbReferenceStore';

const DOFUS_ICON = '/images/logos/dofus.png';
const IFRAME_BLOCK_HINT_MS = 2500;
const DEFAULT_WIDTH = 672;
const DEFAULT_HEIGHT = 560;
const MIN_WIDTH = 360;
const MIN_HEIGHT = 280;

const store = useDofusDbReferenceStore();
const { isOpen, entityLabel, dofusdbId, dofusDbUrl, popupBlocked } = storeToRefs(store);

const panelRef = ref(null);
const headerRef = ref(null);
const isDragging = ref(false);
const isResizing = ref(false);

const panelLeft = ref(0);
const panelTop = ref(0);
const panelWidth = ref(DEFAULT_WIDTH);
const panelHeight = ref(DEFAULT_HEIGHT);

const iframeLoaded = ref(false);
const iframeLikelyBlocked = ref(false);
let blockHintTimer = null;
let dragInstance = null;
let resizeInstance = null;

const showIframe = computed(() => Boolean(isOpen.value && dofusDbUrl.value));

const panelStyle = computed(() => ({
    left: `${panelLeft.value}px`,
    top: `${panelTop.value}px`,
    width: `${panelWidth.value}px`,
    height: `${panelHeight.value}px`,
    right: 'auto',
}));

function clearBlockHintTimer() {
    if (blockHintTimer) {
        clearTimeout(blockHintTimer);
        blockHintTimer = null;
    }
}

function resetIframeState() {
    clearBlockHintTimer();
    iframeLoaded.value = false;
    iframeLikelyBlocked.value = false;
}

function onIframeLoad() {
    iframeLoaded.value = true;
}

function placePanelDefault() {
    const margin = 12;
    const width = Math.min(DEFAULT_WIDTH, window.innerWidth - margin * 2);
    const height = Math.min(DEFAULT_HEIGHT, window.innerHeight - margin * 2 - 72);
    panelWidth.value = Math.max(MIN_WIDTH, width);
    panelHeight.value = Math.max(MIN_HEIGHT, height);
    panelLeft.value = Math.max(margin, window.innerWidth - panelWidth.value - margin);
    panelTop.value = Math.max(margin + 64, 72);
}

function destroyInteract() {
    if (dragInstance) {
        dragInstance.unset();
        dragInstance = null;
    }
    if (resizeInstance) {
        resizeInstance.unset();
        resizeInstance = null;
    }
}

function initInteract() {
    destroyInteract();
    if (!panelRef.value || !headerRef.value) return;

    dragInstance = interact(headerRef.value).draggable({
        listeners: {
            start() {
                isDragging.value = true;
            },
            move(event) {
                panelLeft.value += event.dx;
                panelTop.value += event.dy;
            },
            end() {
                isDragging.value = false;
                // Garder le panneau dans le viewport
                const maxLeft = Math.max(0, window.innerWidth - panelWidth.value);
                const maxTop = Math.max(0, window.innerHeight - 48);
                panelLeft.value = Math.min(Math.max(0, panelLeft.value), maxLeft);
                panelTop.value = Math.min(Math.max(0, panelTop.value), maxTop);
            },
        },
        modifiers: [
            interact.modifiers.restrictRect({
                restriction: document.documentElement,
                endOnly: true,
            }),
        ],
        // Ne pas démarrer un drag depuis le bouton fermer
        ignoreFrom: 'button, a, input',
    });

    resizeInstance = interact(panelRef.value).resizable({
        edges: { left: true, right: true, bottom: true, top: false },
        listeners: {
            start() {
                isResizing.value = true;
            },
            move(event) {
                let { x, y } = { x: panelLeft.value, y: panelTop.value };
                panelWidth.value = Math.max(MIN_WIDTH, event.rect.width);
                panelHeight.value = Math.max(MIN_HEIGHT, event.rect.height);
                x += event.deltaRect.left;
                y += event.deltaRect.top;
                panelLeft.value = x;
                panelTop.value = y;
            },
            end() {
                isResizing.value = false;
            },
        },
        modifiers: [
            interact.modifiers.restrictSize({
                min: { width: MIN_WIDTH, height: MIN_HEIGHT },
                max: {
                    width: () => window.innerWidth - 16,
                    height: () => window.innerHeight - 16,
                },
            }),
            interact.modifiers.restrictEdges({
                outer: document.documentElement,
                endOnly: true,
            }),
        ],
        inertia: false,
    });
}

watch(
    () => [isOpen.value, dofusDbUrl.value],
    async ([open, url]) => {
        resetIframeState();
        if (!open) {
            destroyInteract();
            return;
        }

        placePanelDefault();
        await nextTick();
        initInteract();

        if (!url) return;
        blockHintTimer = setTimeout(() => {
            iframeLikelyBlocked.value = true;
        }, IFRAME_BLOCK_HINT_MS);
    },
);

onBeforeUnmount(() => {
    clearBlockHintTimer();
    destroyInteract();
});
</script>

<template>
    <Teleport to="body">
        <aside
            v-if="isOpen"
            ref="panelRef"
            data-testid="dofusdb-reference-panel"
            class="dofusdb-ref-panel"
            :class="{
                'dofusdb-ref-panel--dragging': isDragging,
                'dofusdb-ref-panel--resizing': isResizing,
            }"
            role="complementary"
            aria-label="Référence DofusDB"
            :style="panelStyle"
        >
            <header
                ref="headerRef"
                class="dofusdb-ref-panel__header"
                :class="{
                    'cursor-grab': !isDragging,
                    'cursor-grabbing': isDragging,
                }"
            >
                <div class="flex items-center gap-2 min-w-0 flex-1 select-none">
                    <Icon :source="DOFUS_ICON" alt="Dofus" size="sm" class="shrink-0" />
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate">Référence DofusDB</p>
                        <p v-if="entityLabel" class="text-xs opacity-70 truncate">{{ entityLabel }}</p>
                    </div>
                </div>
                <button
                    type="button"
                    class="btn btn-sm btn-circle btn-ghost shrink-0"
                    aria-label="Fermer"
                    data-testid="dofusdb-reference-close"
                    @click="store.closePanel()"
                >
                    <Icon source="fa-solid fa-xmark" alt="Fermer" size="sm" />
                </button>
            </header>

            <div class="dofusdb-ref-panel__meta text-xs px-3 py-2 border-b border-base-content/15 flex flex-wrap gap-x-3 gap-y-1">
                <span>
                    <span class="opacity-60">ID</span>
                    <span class="font-mono ml-1">{{ dofusdbId || '—' }}</span>
                </span>
                <a
                    v-if="dofusDbUrl"
                    :href="dofusDbUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="link link-primary truncate max-w-full"
                    data-testid="dofusdb-reference-link"
                >
                    {{ dofusDbUrl }}
                </a>
            </div>

            <div class="dofusdb-ref-panel__frame-wrap">
                <iframe
                    v-if="showIframe"
                    :key="dofusDbUrl"
                    class="dofusdb-ref-panel__iframe"
                    :src="dofusDbUrl"
                    title="Fiche DofusDB"
                    referrerpolicy="no-referrer-when-downgrade"
                    data-testid="dofusdb-reference-iframe"
                    @load="onIframeLoad"
                />

                <div
                    v-if="showIframe && !iframeLoaded && !iframeLikelyBlocked"
                    class="dofusdb-ref-panel__loading"
                >
                    <span class="loading loading-spinner loading-md" />
                    <span class="text-xs opacity-70">Chargement DofusDB…</span>
                </div>

                <div
                    v-if="iframeLikelyBlocked"
                    class="dofusdb-ref-panel__banner"
                    data-testid="dofusdb-reference-fallback"
                >
                    <p class="text-xs">
                        Si la zone reste vide, DofusDB bloque l’affichage embarqué.
                        Utilisez « Ouvrir dans une fenêtre » ci-dessous.
                    </p>
                </div>
            </div>

            <footer class="dofusdb-ref-panel__footer">
                <Btn
                    type="button"
                    size="sm"
                    color="primary"
                    variant="glass"
                    class="gap-2"
                    :disabled="!dofusDbUrl"
                    data-testid="dofusdb-reference-open"
                    @click="store.openExternalWindow()"
                >
                    <Icon :source="DOFUS_ICON" alt="Dofus" size="xs" />
                    Ouvrir dans une fenêtre
                </Btn>
                <p v-if="popupBlocked" class="text-warning text-xs w-full text-right">
                    Fenêtre bloquée — autorisez les pop-ups ou cliquez le lien.
                </p>
            </footer>

            <div class="dofusdb-ref-panel__resize-grip" aria-hidden="true" />
        </aside>
    </Teleport>
</template>

<style scoped>
.dofusdb-ref-panel {
    position: fixed;
    z-index: 10050;
    display: flex;
    flex-direction: column;
    padding: 0;
    border-radius: var(--radius-box, 0.75rem);
    border: 1px solid color-mix(in srgb, var(--color-base-content) 25%, transparent);
    background: var(--color-base-100, #1d232a);
    color: var(--color-base-content, #fff);
    box-shadow:
        0 20px 40px rgba(0, 0, 0, 0.45),
        0 0 0 1px rgba(255, 255, 255, 0.06);
    pointer-events: auto;
    overflow: hidden;
    touch-action: none;
}

.dofusdb-ref-panel--dragging,
.dofusdb-ref-panel--resizing {
    user-select: none;
}

.dofusdb-ref-panel__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.65rem 0.75rem;
    border-bottom: 1px solid color-mix(in srgb, var(--color-base-content) 15%, transparent);
    flex-shrink: 0;
}

.dofusdb-ref-panel__meta {
    flex-shrink: 0;
}

.dofusdb-ref-panel__frame-wrap {
    position: relative;
    flex: 1 1 auto;
    min-height: 0;
    background: #0b0f14;
}

.dofusdb-ref-panel__iframe {
    width: 100%;
    height: 100%;
    border: 0;
    background: #fff;
    /* Laisse le resize sur les bords du panneau */
    pointer-events: auto;
}

.dofusdb-ref-panel__loading {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: color-mix(in srgb, var(--color-base-100, #1d232a) 88%, transparent);
}

.dofusdb-ref-panel__banner {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 0.5rem 0.75rem;
    background: color-mix(in srgb, var(--color-warning, #fbbf24) 18%, var(--color-base-100, #1d232a));
    border-top: 1px solid color-mix(in srgb, var(--color-warning, #fbbf24) 35%, transparent);
}

.dofusdb-ref-panel__footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-top: 1px solid color-mix(in srgb, var(--color-base-content) 15%, transparent);
    flex-shrink: 0;
}

.dofusdb-ref-panel__resize-grip {
    position: absolute;
    right: 2px;
    bottom: 2px;
    width: 14px;
    height: 14px;
    pointer-events: none;
    background:
        linear-gradient(
            135deg,
            transparent 45%,
            color-mix(in srgb, var(--color-base-content) 45%, transparent) 46%,
            color-mix(in srgb, var(--color-base-content) 45%, transparent) 54%,
            transparent 55%
        ),
        linear-gradient(
            135deg,
            transparent 60%,
            color-mix(in srgb, var(--color-base-content) 45%, transparent) 61%,
            color-mix(in srgb, var(--color-base-content) 45%, transparent) 69%,
            transparent 70%
        );
}
</style>
