<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Modal Molecule (DaisyUI + Custom Utility, dialog native)
 *
 * @description
 * Molécule Modal stylée DaisyUI, conforme Atomic Design et KrosmozJDR, version dialog native (accessible).
 * - Utilise <dialog class="modal"> pour l'accessibilité
 * - Gère le drag & drop via le header
 * - Gère le redimensionnement (si activé)
 * - Gère la fermeture via Échap et clic sur overlay (configurable)
 * - Supporte plusieurs variants (glass, dash, outline, soft, ghost)
 * - Placement flexible (format Y-X : "middle-center", "top-start", etc.)
 * - Slots : header (titre), default (contenu principal), actions (boutons), backdrop (optionnel)
 *
 * @see https://daisyui.com/components/modal/
 *
 * @example
 * <Modal :open="showModal" size="lg" variant="glass" placement="middle-center" @close="showModal = false">
 *   <template #header><h3 class="text-lg font-bold">Titre</h3></template>
 *   <p>Contenu…</p>
 *   <template #actions><Btn @click="showModal = false">Fermer</Btn></template>
 * </Modal>
 *
 * @props {Boolean} open - Ouvre le modal à sa création (défaut: false)
 * @props {String} size - Taille du modal ('xs', 'sm', 'md', 'lg', 'xl', 'full', 'auto'), défaut 'auto'
 * @props {Boolean} closeOnOutsideClick - Permet de fermer en cliquant en dehors (défaut: true)
 * @props {Boolean} closeOnEsc - Active la fermeture via Échap (défaut: false)
 * @props {Boolean} closeOnButton - Affiche une icône de fermeture en haut à droite (défaut: true)
 * @props {String} color - Couleur du modal ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'), défaut ''
 * @props {String} variant - Style visuel ('glass', 'dash', 'outline', 'soft', 'ghost'), défaut 'glass'
 * @props {Boolean} overlay - Affiche l'arrière-plan (défaut: true)
 * @props {Boolean} resizable - Permet de redimensionner le modal (défaut: false)
 * @props {Boolean} draggable - Permet de déplacer le modal via son header (défaut: true)
 * @props {String} animation - Type d'animation ('none', 'fade', 'zoom', 'slide'), défaut 'fade'
 * @props {String} placement - Placement du modal (format Y-X, ex: 'middle-center'), défaut 'middle-center'
 * @slot header - Titre ou header custom (utilisé pour le drag)
 * @slot default - Contenu principal du modal
 * @slot actions - Boutons d'action (dans .modal-action)
 * @slot backdrop - Contenu custom du backdrop (optionnel)
 */
import { ref, watch, onMounted, onBeforeUnmount, computed, nextTick } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import interact from 'interactjs';
import { colorList } from '@/Pages/Atoms/atomMap';

const emit = defineEmits(['close', 'open']);

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    open: { 
        type: Boolean, 
        default: false 
    },
    size: {
        type: String,
        default: 'auto',
        validator: v => ['xs', 'sm', 'md', 'lg', 'xl', 'full', 'auto'].includes(v),
    },
    closeOnOutsideClick: {
        type: Boolean,
        default: true,
    },
    closeOnEsc: {
        type: Boolean,
        default: false,
    },
    closeOnButton: {
        type: Boolean,
        default: true,
    },
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    variant: {
        type: String,
        default: 'glass',
        validator: v => ['glass', 'dash', 'outline', 'soft', 'ghost'].includes(v),
    },
    overlay: {
        type: Boolean,
        default: true,
    },
    resizable: {
        type: Boolean,
        default: false,
    },
    draggable: {
        type: Boolean,
        default: true,
    },
    animation: {
        type: String,
        default: 'fade',
        validator: v => ['none', 'fade', 'zoom', 'slide'].includes(v),
    },
    placement: {
        type: String,
        default: 'middle-center',
        validator: v => {
            // Format Y-X : "top-center", "middle-start", etc.
            const parts = v.split('-');
            if (parts.length !== 2) return false;
            const [y, x] = parts;
            return ['top', 'middle', 'bottom'].includes(y) && 
                   ['start', 'center', 'end'].includes(x);
        },
    },
    id: { 
        type: String, 
        default: '' 
    },
});

const dialogRef = ref(null);
const modalBoxRef = ref(null);
const headerRef = ref(null);
const resizeHandleRef = ref(null);

// État pour le drag & drop
const isDragging = ref(false);
const isResizing = ref(false);
const position = ref({ x: 0, y: 0 });

// Classes pour le placement (DaisyUI)
const placementClasses = computed(() => {
    const parts = props.placement.split('-');
    const [y, x] = parts;
    const classes = [];
    
    // Classes Y (vertical) - DaisyUI
    if (y === 'top') classes.push('modal-top');
    else if (y === 'middle') classes.push('modal-middle');
    else if (y === 'bottom') classes.push('modal-bottom');
    
    // Classes X (horizontal) - DaisyUI
    // Note: DaisyUI n'a pas de classe modal-center par défaut (c'est le comportement par défaut)
    if (x === 'start') classes.push('modal-start');
    else if (x === 'end') classes.push('modal-end');
    // 'center' est le comportement par défaut, pas besoin de classe
    
    return classes;
});

// Classes pour le variant
const variantClasses = computed(() => {
    const variants = {
        glass: 'box-glass-lg',
        dash: 'box-dash-lg',
        outline: 'box-outline-lg',
        soft: 'box-soft-lg',
        ghost: 'box-ghost-lg',
    };
    return variants[props.variant] || variants.glass;
});

// Classes pour la couleur
// Utilise la classe color-{name} qui définit la variable CSS --color pour influencer le background
const colorClasses = computed(() => {
    if (!props.color) return 'color-neutral bg-color-neutral-950 light:bg-color-neutral-50';
    return `color-${props.color} bg-color-${props.color}-950 light:bg-color-${props.color}-50`;
});

// Classes pour l'overlay
const overlayClasses = computed(() => {
    if (!props.overlay) return '';
    return `modal-backdrop ${variantClasses.value} bg-base-900/30`;
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'modal',
            props.open && 'modal-open',
            ...placementClasses.value,
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);

const attrs = computed(() => getCommonAttrs(props));

// Mapping des tailles
const sizeClassMap = {
    xs: 'w-xs',
    sm: 'w-sm',
    md: 'w-md',
    lg: 'w-lg',
    xl: 'w-xl',
    full: 'w-full h-full max-w-full max-h-full',
    auto: '', // Pas de contrainte de taille
};

const modalBoxClasses = computed(() => {
    const base = [
        'modal-box',
        sizeClassMap[props.size] || '',
        variantClasses.value,
        colorClasses.value,
    ];
    
    // Classes pour le drag
    if (props.draggable) {
        base.push('cursor-move');
    }
    
    // Classes pour le resize
    if (props.resizable) {
        base.push('resize');
    }
    
    // Animation classes
    if (props.animation !== 'none') {
        const animationClassMap = {
            fade: 'transition-opacity duration-200 opacity-0 modal-fade',
            zoom: 'transition-transform duration-200 scale-95 modal-zoom',
            slide: 'transition-transform duration-200 translate-y-4 modal-slide',
        };
        base.push(animationClassMap[props.animation]);
        
        if (props.open) {
            const animationActiveClassMap = {
                fade: 'opacity-100',
                zoom: 'scale-100',
                slide: 'translate-y-0',
            };
            base.push(animationActiveClassMap[props.animation]);
        }
    }
    
    return mergeClasses(base);
});

// Fonction de fermeture
function closeModal() {
    emit('close');
    if (dialogRef.value && dialogRef.value.open) {
        dialogRef.value.close();
    }
}

// Initialisation d'interact.js pour le drag & drop et le resize
let dragInstance = null;
let resizeInstance = null;

function initInteract() {
    if (!modalBoxRef.value) return;
    
    // Réinitialiser la position quand le modal s'ouvre
    if (props.open) {
        position.value = { x: 0, y: 0 };
        if (modalBoxRef.value) {
            modalBoxRef.value.style.transform = 'translate(0px, 0px)';
            modalBoxRef.value.style.position = '';
            modalBoxRef.value.style.left = '';
            modalBoxRef.value.style.top = '';
            modalBoxRef.value.style.margin = '';
        }
    }
    
    // Configuration du drag (via le header, mais déplace le modal-box)
    if (props.draggable && headerRef.value && modalBoxRef.value) {
        dragInstance = interact(headerRef.value)
            .draggable({
                listeners: {
                    start() {
                        isDragging.value = true;
                        // S'assurer que le modal-box est en position fixed pour le drag
                        if (modalBoxRef.value) {
                            const rect = modalBoxRef.value.getBoundingClientRect();
                            modalBoxRef.value.style.position = 'fixed';
                            modalBoxRef.value.style.left = `${rect.left}px`;
                            modalBoxRef.value.style.top = `${rect.top}px`;
                            modalBoxRef.value.style.margin = '0';
                            position.value = { x: 0, y: 0 };
                        }
                    },
                    move(event) {
                        if (modalBoxRef.value) {
                            position.value.x += event.dx;
                            position.value.y += event.dy;
                            modalBoxRef.value.style.transform = `translate(${position.value.x}px, ${position.value.y}px)`;
                        }
                    },
                    end() {
                        isDragging.value = false;
                    }
                },
                // Limiter le drag à la fenêtre
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ]
            });
    }
    
    // Configuration du resize (sur le modal-box directement)
    if (props.resizable && modalBoxRef.value) {
        resizeInstance = interact(modalBoxRef.value)
            .resizable({
                edges: { right: true, bottom: true },
                listeners: {
                    start() {
                        isResizing.value = true;
                        // S'assurer que le modal-box est en position fixed pour le resize
                        if (modalBoxRef.value && modalBoxRef.value.style.position !== 'fixed') {
                            const rect = modalBoxRef.value.getBoundingClientRect();
                            modalBoxRef.value.style.position = 'fixed';
                            modalBoxRef.value.style.left = `${rect.left}px`;
                            modalBoxRef.value.style.top = `${rect.top}px`;
                            modalBoxRef.value.style.margin = '0';
                        }
                    },
                    move(event) {
                        if (modalBoxRef.value) {
                            const target = event.target;
                            let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.deltaRect.left;
                            let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.deltaRect.top;
                            
                            target.style.width = event.rect.width + 'px';
                            target.style.height = event.rect.height + 'px';
                            
                            target.setAttribute('data-x', x);
                            target.setAttribute('data-y', y);
                        }
                    },
                    end() {
                        isResizing.value = false;
                    }
                },
                modifiers: [
                    // Limiter la taille minimale
                    interact.modifiers.restrictSize({
                        min: { width: 200, height: 150 }
                    })
                ]
            });
    }
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
    if (headerRef.value) {
        interact(headerRef.value).unset();
    }
    if (modalBoxRef.value) {
        interact(modalBoxRef.value).unset();
    }
}

// Gestion de la touche Échap
function handleKeyDown(e) {
    if (e.key === 'Escape' && props.closeOnEsc && props.open) {
        closeModal();
    }
}

// Gestion du clic sur l'overlay
function handleBackdropClick(e) {
    // Ne pas fermer si on est en train de redimensionner ou de déplacer
    if (isResizing.value || isDragging.value) {
        return;
    }
    if (props.closeOnOutsideClick && e.target === e.currentTarget) {
        closeModal();
    }
}

// Watch pour l'ouverture/fermeture
watch(() => props.open, async (val) => {
    if (dialogRef.value) {
        if (val && !dialogRef.value.open) {
            dialogRef.value.showModal();
            emit('open');
            // Réinitialiser la position et initialiser interact après l'ouverture
            await nextTick();
            position.value = { x: 0, y: 0 };
            if (modalBoxRef.value) {
                modalBoxRef.value.style.transform = 'translate(0px, 0px)';
                modalBoxRef.value.style.position = '';
                modalBoxRef.value.style.left = '';
                modalBoxRef.value.style.top = '';
                modalBoxRef.value.style.margin = '';
            }
            initInteract();
        } else if (!val && dialogRef.value.open) {
            dialogRef.value.close();
            destroyInteract();
        }
    }
});

onMounted(async () => {
    if (props.open && dialogRef.value && !dialogRef.value.open) {
        dialogRef.value.showModal();
    }
    
    if (dialogRef.value) {
        dialogRef.value.addEventListener('close', closeModal);
        if (props.closeOnEsc) {
            dialogRef.value.addEventListener('cancel', closeModal);
        }
    }
    
    // Ajouter le listener pour Échap
    if (props.closeOnEsc) {
        document.addEventListener('keydown', handleKeyDown);
    }
    
    // Initialiser interact.js après le montage
    await nextTick();
    if (props.open) {
        initInteract();
    }
});

onBeforeUnmount(() => {
    // Nettoyer interact.js
    destroyInteract();
    
    if (dialogRef.value) {
        dialogRef.value.removeEventListener('close', closeModal);
        if (props.closeOnEsc) {
            dialogRef.value.removeEventListener('cancel', closeModal);
        }
    }
    
    // Nettoyer les listeners
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <dialog 
        ref="dialogRef" 
        :id="id" 
        :class="moleculeClasses" 
        v-bind="attrs"
        @click="handleBackdropClick"
    >
        <div 
            ref="modalBoxRef"
            :class="modalBoxClasses"
        >
            <header 
                v-if="$slots.header || closeOnButton" 
                ref="headerRef"
                class="mb-2 relative"
                :class="{ 
                    'cursor-grab select-none': draggable && !isDragging,
                    'cursor-grabbing select-none': draggable && isDragging
                }"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <slot name="header" />
                    </div>
                    <button
                        v-if="closeOnButton"
                        type="button"
                        class="btn btn-sm btn-circle btn-ghost absolute top-0 right-0 -mt-2 -mr-2 z-10 cursor-pointer"
                        @click="closeModal"
                        aria-label="Fermer le modal"
                    >
                        <Icon 
                            source="fa-xmark" 
                            pack="solid"
                            alt="Fermer"
                            size="sm"
                        />
                    </button>
                </div>
            </header>
            
            <div class="cursor-default">
                <slot />
            </div>
            
            <footer v-if="$slots.actions" class="modal-action mt-4">
                <slot name="actions" />
            </footer>
            
            <!-- Handle de redimensionnement -->
            <div 
                v-if="resizable"
                ref="resizeHandleRef"
                class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize bg-base-300/50 hover:bg-base-300"
            >
                <div class="absolute bottom-1 right-1 w-2 h-2 border-r-2 border-b-2 border-base-content/50"></div>
            </div>
        </div>
        
        <form 
            v-if="overlay"
            method="dialog" 
            :class="overlayClasses"
            @click.stop
        >
            <slot name="backdrop">
                <button type="submit" class="sr-only">close</button>
            </slot>
        </form>
    </dialog>
</template>

<style scoped lang="scss">

.modal{
    // Animations
    &-fade {
        opacity: 0;
    }
    &-open &-fade {
        opacity: 1;
    }
    &-zoom {
        transform: scale(0.95);
    }
    &-open &-zoom {
        transform: scale(1);
    }
    &-slide {
        transform: translateY(1rem);
    }
    &-open &-slide {
        transform: translateY(0);
    }

    // Placement
    &-top {
        top: 0;
    }
    &-middle {
        top: 50%;
        transform: translateY(-50%);
    }
    &-bottom {
        bottom: 0;
    }
    &-start {
        left: 0;
    }
    &-center {
        left: 50%;
        transform: translateX(-50%);
    }
    &-end {
        right: 0;
    }

}

/* Placement X géré via flexbox inline sur le dialog */
dialog.modal {
    display: flex;
    align-items: center;
}

/* S'assurer que le contenu du modal a un curseur normal */
.modal-box > div:not(header):not(footer) {
    cursor: default;
}

/* Le header draggable a le curseur grab/grabbing */
.modal-box header.cursor-grab {
    cursor: grab;
}

.modal-box header.cursor-grab:active,
.modal-box header.cursor-grabbing {
    cursor: grabbing;
}

/* Le bouton de fermeture garde son curseur pointer */
.modal-box header button {
    cursor: pointer !important;
}
</style>
