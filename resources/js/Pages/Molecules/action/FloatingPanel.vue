<script setup>
/**
 * FloatingPanel Molecule
 *
 * @description
 * Panneau flottant non-modal (pas de `dialog.showModal`) : déplaçable,
 * fermeture via croix uniquement (pas de clic extérieur / Esc).
 * Permet d’éditer le contenu sous-jacent en parallèle.
 *
 * @example
 * <FloatingPanel :open="open" title="Référence" @close="open = false">
 *   Contenu…
 *   <template #actions><Btn>Ouvrir</Btn></template>
 * </FloatingPanel>
 */
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import interact from 'interactjs';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { OVERLAY_Z_INDEX } from '@/Composables/overlay/overlayConstants';

const props = defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: '' },
    /** Largeur CSS (ex. 22rem) */
    width: { type: String, default: '22rem' },
    /** Position initiale (coin haut-droit par défaut) */
    initialTop: { type: Number, default: 96 },
    initialRight: { type: Number, default: 24 },
    draggable: { type: Boolean, default: true },
    closeOnButton: { type: Boolean, default: true },
});

const emit = defineEmits(['close', 'open']);

const panelRef = ref(null);
const headerRef = ref(null);
const isDragging = ref(false);
const position = ref({ x: 0, y: 0 });

let dragInstance = null;

const panelStyle = computed(() => ({
    zIndex: Math.max(OVERLAY_Z_INDEX.floatingPanel, 5000),
    width: props.width,
    top: `${props.initialTop}px`,
    right: `${props.initialRight}px`,
    left: 'auto',
    transform: `translate(${position.value.x}px, ${position.value.y}px)`,
}));

function closePanel() {
    emit('close');
}

function destroyInteract() {
    if (dragInstance) {
        dragInstance.unset();
        dragInstance = null;
    }
}

function initInteract() {
    destroyInteract();
    if (!props.draggable || !headerRef.value || !panelRef.value) return;

    dragInstance = interact(headerRef.value).draggable({
        listeners: {
            start() {
                isDragging.value = true;
                if (panelRef.value) {
                    const rect = panelRef.value.getBoundingClientRect();
                    panelRef.value.style.right = 'auto';
                    panelRef.value.style.left = `${rect.left}px`;
                    panelRef.value.style.top = `${rect.top}px`;
                    position.value = { x: 0, y: 0 };
                }
            },
            move(event) {
                position.value = {
                    x: position.value.x + event.dx,
                    y: position.value.y + event.dy,
                };
            },
            end() {
                isDragging.value = false;
            },
        },
        modifiers: [
            interact.modifiers.restrictRect({
                restriction: document.documentElement,
                endOnly: true,
            }),
        ],
    });
}

watch(
    () => props.open,
    async (val) => {
        if (val) {
            position.value = { x: 0, y: 0 };
            emit('open');
            await nextTick();
            initInteract();
        } else {
            destroyInteract();
        }
    },
);

onBeforeUnmount(() => {
    destroyInteract();
});
</script>

<template>
    <Teleport to="body">
        <aside
            v-if="open"
            ref="panelRef"
            class="floating-panel box-glass-lg bg-base-100 text-base-content shadow-2xl border border-base-300 rounded-box pointer-events-auto fixed min-h-32"
            role="complementary"
            :aria-label="title || 'Panneau flottant'"
            :style="panelStyle"
        >
            <header
                ref="headerRef"
                class="floating-panel__header flex items-start justify-between gap-2 px-3 pt-3 pb-2 border-b border-base-300/60"
                :class="{
                    'cursor-grab select-none': draggable && !isDragging,
                    'cursor-grabbing select-none': draggable && isDragging,
                }"
            >
                <div class="min-w-0 flex-1">
                    <slot name="header">
                        <h3 v-if="title" class="text-sm font-bold truncate pr-2">{{ title }}</h3>
                    </slot>
                </div>
                <button
                    v-if="closeOnButton"
                    type="button"
                    class="btn btn-sm btn-circle btn-ghost shrink-0"
                    aria-label="Fermer le panneau"
                    @click="closePanel"
                >
                    <Icon source="fa-xmark" pack="solid" alt="Fermer" size="sm" />
                </button>
            </header>

            <div class="floating-panel__body px-3 py-3 cursor-default">
                <slot />
            </div>

            <footer v-if="$slots.actions" class="floating-panel__actions flex flex-wrap gap-2 justify-end px-3 pb-3">
                <slot name="actions" />
            </footer>
        </aside>
    </Teleport>
</template>
