<script setup>
/**
 * Dropdown Atom (DaisyUI)
 *
 * @description
 * Composant atomique Dropdown conforme DaisyUI et Atomic Design.
 * - Slot par défaut : trigger (bouton, icône, etc.) ou prop label
 * - Slot #content : contenu déroulant (libre, liste, card, etc.)
 * - Props DaisyUI : placement, open, hover, label, contentClass
 * - Props d'accessibilité et HTML natif héritées de commonProps (dont disabled)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (aria-haspopup, aria-expanded, tabindex, etc.)
 * - Gestion de l'ouverture/fermeture (clic, hover, esc, clic extérieur)
 * - Tooltip intégré comme pour tous les atoms
 *
 * @example
 * <Dropdown label="Menu" placement="bottom-end" content-class="backdrop-blur-lg opacity-80">
 *   <template #content>
 *     <div>Contenu libre (liste, card, etc.)</div>
 *   </template>
 * </Dropdown>
 *
 * <Dropdown placement="bottom-end" :content-class="['bg-base-100', 'rounded-box', 'backdrop-blur-md']">
 *   <template #default>
 *     <Btn icon="fa-bars" />
 *   </template>
 *   <template #content>
 *     <div>Profil</div>
 *     <div>Déconnexion</div>
 *   </template>
 * </Dropdown>
 *
 * @props {String} placement - Position du menu (start, end, top, bottom, left, right, center)
 * @props {Boolean} open - Force l'ouverture du menu (contrôle externe)
 * @props {Boolean} hover - Ouvre le menu au survol
 * @props {String} label - Texte du trigger (optionnel, sinon slot)
 * @props {String|Array} contentClass - Classes custom à ajouter au content (fusionnées sans doublon)
 * @props {Boolean} disabled - Désactive le trigger (hérité de commonProps)
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - hérités de commonProps
 * @slot default - Trigger du dropdown (bouton, icône, etc.)
 * @slot content - Contenu déroulant (libre)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Le composant gère l'accessibilité et la navigation clavier.
 */


import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, mergeClasses, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    placement: {
        type: String,
        default: 'bottom-end',
        validator: v => ['start', 'end', 'top', 'bottom', 'left', 'right', 'center', 'bottom-end', 'top-end', 'left-end', 'right-end'].includes(v),
    },
    open: {
        type: Boolean,
        default: false,
    },
    hover: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
    contentClass: {
        type: [String, Array],
        default: '',
    },
});

const isOpen = ref(false);
const triggerRef = ref(null);
const contentRef = ref(null);

// Gestion ouverture/fermeture
const openDropdown = () => {
    if (!props.disabled) isOpen.value = true;
};
const closeDropdown = () => {
    isOpen.value = false;
};
const toggleDropdown = () => {
    if (!props.disabled) isOpen.value = !isOpen.value;
};

// Clic extérieur
function handleClickOutside(e) {
    if (!isOpen.value) return;
    if (
        triggerRef.value && !triggerRef.value.$el.contains(e.target) &&
        contentRef.value && !contentRef.value.contains(e.target)
    ) {
        closeDropdown();
    }
}

// Esc pour fermer
function handleEscape(e) {
    if (isOpen.value && e.key === 'Escape') {
        closeDropdown();
    }
}

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
    document.addEventListener('keydown', handleEscape);
});
onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});

// Contrôle externe
const effectiveOpen = computed(() => props.open || isOpen.value);

// Classes DaisyUI explicites
function getDropdownClasses() {
    const classes = ['dropdown'];
    // Placement horizontal
    if (props.placement === 'start' || props.placement === 'bottom-start' || props.placement === 'top-start' || props.placement === 'left-start' || props.placement === 'right-start') {
        classes.push('dropdown-start');
    } else if (props.placement === 'center') {
        classes.push('dropdown-center');
    } else if (props.placement === 'end' || props.placement === 'bottom-end' || props.placement === 'top-end' || props.placement === 'left-end' || props.placement === 'right-end') {
        classes.push('dropdown-end');
    }
    // Placement vertical
    if (props.placement.startsWith('top')) {
        classes.push('dropdown-top');
    } else if (props.placement.startsWith('bottom')) {
        classes.push('dropdown-bottom');
    } else if (props.placement.startsWith('left')) {
        classes.push('dropdown-left');
    } else if (props.placement.startsWith('right')) {
        classes.push('dropdown-right');
    }
    // Hover
    if (props.hover) classes.push('dropdown-hover');
    // Open forcé
    if (effectiveOpen.value) classes.push('dropdown-open');
    return classes.join(' ');
}

function getContentClasses() {
    // Classes DaisyUI pour le contenu déroulant
    const defaultClasses = [
        'dropdown-content',
        'rounded-box',
        'z-[1]',
        'w-52',
        'p-2',
        'shadow-sm',
        'bg-base-100',
        'backdrop-blur-2xl',
        'opacity-90',
    ];
    // Ajoute les utilitaires custom au content
    return mergeClasses([...defaultClasses, ...getCustomUtilityClasses(props)], props.contentClass);
}

const dropdownClasses = computed(getDropdownClasses);
const contentClasses = computed(getContentClasses);
const attrs = computed(() => getCommonAttrs(props));

</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="dropdownClasses" v-bind="attrs">
            <!-- Trigger -->
            <Btn ref="triggerRef" tabindex="0" role="button" :aria-haspopup="true" :aria-expanded="effectiveOpen"
                @click="props.hover ? undefined : toggleDropdown" @mouseenter="props.hover ? openDropdown() : undefined"
                @mouseleave="props.hover ? closeDropdown() : undefined" :disabled="props.disabled"
                :class="['btn', { 'btn-disabled': props.disabled }]">
                <span v-if="props.label && !$slots.default">{{ props.label }}</span>
                <slot name="label" v-else />
            </Btn>
            <!-- Contenu déroulant -->
            <div v-show="effectiveOpen" ref="contentRef" :class="contentClasses" tabindex="-1"
                @mouseenter="props.hover ? openDropdown() : undefined"
                @mouseleave="props.hover ? closeDropdown() : undefined">
                <slot name="content" />
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped>
.dropdown-content {
    min-width: 8rem;
}
</style>
