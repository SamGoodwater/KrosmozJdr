<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Modal Molecule (DaisyUI + Custom Utility, dialog native)
 *
 * @description
 * Molécule Modal stylée DaisyUI, conforme Atomic Design et KrosmozJDR, version dialog native (accessible).
 * - Utilise <dialog class="modal"> pour l'accessibilité
 * - Props : open (bool, contrôle l'ouverture), placement (top, middle, bottom, start, end), id (string), animation (fade, zoom, slide, none), size (xs, sm, md, lg, xl, full), customUtility, accessibilité, etc.
 * - Slots : header (titre), default (contenu principal), actions (boutons), backdrop (optionnel)
 * - mergeClasses pour les classes DaisyUI explicites (modal, modal-open, modal-top, etc.)
 * - getCommonAttrs pour l'accessibilité
 * - Gère ouverture/fermeture via props ou JS (showModal/close)
 * - Émet l'événement @close quand le modal se ferme
 *
 * @see https://daisyui.com/components/modal/
 *
 * @example
 * <Modal :open="showModal" placement="middle" size="lg" animation="fade" @close="showModal = false">
 *   <template #header><h3 class="text-lg font-bold">Titre</h3></template>
 *   <p>Contenu…</p>
 *   <template #actions><Btn @click="showModal = false">Fermer</Btn></template>
 * </Modal>
 *
 * @props {Boolean} open - Contrôle l'ouverture du modal (optionnel, sinon contrôle JS)
 * @props {String} placement - Position DaisyUI ('top', 'middle', 'bottom', 'start', 'end'), défaut 'middle'
 * @props {String} id - Identifiant unique (pour contrôle JS)
 * @props {String} animation - Animation d'ouverture ('none', 'fade', 'zoom', 'slide'), défaut 'none'
 * @props {String} size - Taille du modal ('xs', 'sm', 'md', 'lg', 'xl', 'full'), défaut 'md'
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot header - Titre ou header custom
 * @slot default - Contenu principal du modal
 * @slot actions - Boutons d'action (dans .modal-action)
 * @slot backdrop - Contenu custom du backdrop (optionnel)
 */
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const emit = defineEmits(['close', 'open']);
const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    open: { type: Boolean, default: false },
    placement: {
        type: String,
        default: 'middle',
        validator: v => ['top', 'middle', 'bottom', 'start', 'end'].includes(v),
    },
    id: { type: String, default: '' },
    animation: {
        type: String,
        default: 'none',
        validator: v => ['none', 'fade', 'zoom', 'slide'].includes(v),
    },
    size: {
        type: String,
        default: 'md',
        validator: v => ['xs', 'sm', 'md', 'lg', 'xl', 'full'].includes(v),
    },
});

const dialogRef = ref(null);

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'modal',
            props.open && 'modal-open',
            props.placement === 'top' && 'modal-top',
            props.placement === 'middle' && 'modal-middle',
            props.placement === 'bottom' && 'modal-bottom',
            props.placement === 'start' && 'modal-start',
            props.placement === 'end' && 'modal-end',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));

// Mapping des tailles
const sizeClassMap = {
    xs: 'max-w-xs',
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    full: 'w-full h-full max-w-full max-h-full',
};

// Mapping des animations
const animationClassMap = {
    none: '',
    fade: 'transition-opacity duration-200 opacity-0 modal-fade',
    zoom: 'transition-transform duration-200 scale-95 modal-zoom',
    slide: 'transition-transform duration-200 translate-y-4 modal-slide',
};
const animationActiveClassMap = {
    none: '',
    fade: 'opacity-100',
    zoom: 'scale-100',
    slide: 'translate-y-0',
};

const modalBoxClasses = computed(() => {
    const base = [
        'modal-box',
        sizeClassMap[props.size] || sizeClassMap.md,
    ];
    // Animation classes
    if (props.animation !== 'none') {
        base.push(animationClassMap[props.animation]);
        if (props.open) {
            base.push(animationActiveClassMap[props.animation]);
        }
    }
    return mergeClasses(base);
});

function closeModal() {
    emit('close');
    if (dialogRef.value && dialogRef.value.open) {
        dialogRef.value.close();
    }
}

watch(() => props.open, (val) => {
    if (dialogRef.value) {
        if (val && !dialogRef.value.open) {
            dialogRef.value.showModal();
            emit('open');
        } else if (!val && dialogRef.value.open) {
            dialogRef.value.close();
        }
    }
});

onMounted(() => {
    if (props.open && dialogRef.value && !dialogRef.value.open) {
        dialogRef.value.showModal();
    }
    if (dialogRef.value) {
        dialogRef.value.addEventListener('close', closeModal);
    }
});
onBeforeUnmount(() => {
    if (dialogRef.value) {
        dialogRef.value.removeEventListener('close', closeModal);
    }
});
</script>

<template>
    <dialog ref="dialogRef" :id="id" :class="moleculeClasses" v-bind="attrs">
        <div :class="modalBoxClasses">
            <header v-if="$slots.header" class="mb-2">
                <slot name="header" />
            </header>
            <div>
                <slot />
            </div>
            <footer v-if="$slots.actions" class="modal-action mt-4">
                <slot name="actions" />
            </footer>
        </div>
        <form method="dialog" class="modal-backdrop bd-glass-lg bg-base-900/30">
            <slot name="backdrop">
                <button>close</button>
            </slot>
        </form>
    </dialog>
</template>

<style scoped>
/* Animation fade */
.modal-fade {
    opacity: 0;
}

.modal-open .modal-fade {
    opacity: 1;
}

/* Animation zoom */
.modal-zoom {
    transform: scale(0.95);
}

.modal-open .modal-zoom {
    transform: scale(1);
}

/* Animation slide */
.modal-slide {
    transform: translateY(1rem);
}

.modal-open .modal-slide {
    transform: translateY(0);
}

/* Glassmorphism pour le backdrop */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(16px) brightness(1.2);
    -webkit-backdrop-filter: blur(16px) brightness(1.2);
    transition: backdrop-filter 0.3s ease, background-color 0.3s ease;
}

/* Amélioration du glassmorphism quand le modal est ouvert */
.modal-open .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(20px) brightness(1.25);
    -webkit-backdrop-filter: blur(20px) brightness(1.25);
}
</style>
