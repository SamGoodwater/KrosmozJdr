<script setup>
defineOptions({ inheritAttrs: false });

/**
 * ToggleCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les switches toggle, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : toggle natif
 * - Gestion des attributs spécifiques aux toggles : checked, on/off states
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - ToggleCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/toggle/
 * @version DaisyUI v5.x
 *
 * @example
 * <ToggleCore v-model="enabled" />
 * <ToggleCore v-model="enabled" color="primary" size="lg" />
 * <ToggleCore v-model="enabled" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <ToggleCore 
 *   v-model="enabled" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {Boolean} modelValue - v-model (état activé/désactivé)
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - toggle natif (optionnel)
 */

// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, watch, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

// ------------------------------------------
// 🔧 Définition des props + emits
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('toggle', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'toggle', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('toggle', {
            variant: props.variant,
            color: props.color,
            size: props.size,
            animation: props.animation,
      ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
      ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
    }, false)
  )
)

// ------------------------------------------
// 🎯 Gestion du v-model pour toggle
// ------------------------------------------
const isChecked = computed({
    get() {
        return !!props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

// ------------------------------------------
// 🔄 Gestion de l'état indeterminate
// ------------------------------------------
const isIndeterminate = computed(() => {
    return props.indeterminate || false;
});

const toggleRef = ref(null);

// Mise à jour de l'état indeterminate sur le DOM
watch(isIndeterminate, (value) => {
    if (toggleRef.value) {
        toggleRef.value.indeterminate = value;
    }
}, { immediate: true });

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onInput(e) {
    isChecked.value = e.target.checked;
}

function onKeydown(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        isChecked.value = !isChecked.value;
    }
}
</script>

<template>
    <!-- 🧱 Toggle simple sans label (ToggleCore ne supporte pas les labels inline/floating) -->
    <input
        ref="toggleRef"
        type="checkbox"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses"
        :checked="isChecked"
        :indeterminate="isIndeterminate"
        @input="onInput"
        @keydown="onKeydown"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour ToggleCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

input[type="checkbox"].toggle {
    // Styles de base pour tous les toggles
    outline: none;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    appearance: none;
    position: relative;
    display: inline-block;
    width: 3rem;
    height: 1.5rem;
    border-radius: 9999px;
    border: none;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    background-color: color-mix(in srgb, var(--color) 20%, transparent);
    
    // États de focus
    &:focus {
        outline: none;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
    }
    
    // États disabled
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    // Indicateur (boule blanche)
    &::before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: calc(1.5rem - 4px);
        height: calc(1.5rem - 4px);
        background-color: white;
        border-radius: 50%;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 2px 4px color-mix(in srgb, var(--color) 20%, transparent);
    }
    
    // État activé
    &:checked {
        background-color: var(--color);
        
        &::before {
            transform: translateX(1.5rem);
            box-shadow: 0 2px 4px color-mix(in srgb, var(--color) 30%, transparent);
        }
    }
    
    // Variant Glass - Effet glassmorphisme
    &.bg-transparent.border {
        @apply border-glass-md box-glass-md;
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
            box-shadow: 
                0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        @apply border-glass-sm;
        border-style: dashed;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        @apply border-glass-md;
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
        }
    }
    
    // Variant Ghost - Transparent
    &.border.border-transparent.bg-transparent {
        background-color: transparent;
        border-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            border-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
        }
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.border-b-2.bg-transparent.rounded-none {
        @apply border-glass-b-md;
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-b-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-bottom-color: var(--color);
        }
    }
    
    // Animations
    &.checked\\:translate-x-6:checked {
        &::before {
            transform: translateX(1.5rem);
        }
    }
    
    &.transition-all {
        transition: all 0.3s ease-in-out;
    }
    
    &.duration-300 {
        transition-duration: 300ms;
    }
}

// Styles pour les tailles DaisyUI (conservés car spécifiques au toggle)
.toggle-xs {
    width: 2rem;
    height: 1rem;
    
    &::before {
        width: calc(1rem - 4px);
        height: calc(1rem - 4px);
    }
    
    &:checked::before {
        transform: translateX(1rem);
    }
}

.toggle-sm {
    width: 2.5rem;
    height: 1.25rem;
    
    &::before {
        width: calc(1.25rem - 4px);
        height: calc(1.25rem - 4px);
    }
    
    &:checked::before {
        transform: translateX(1.25rem);
    }
}

.toggle-md {
    width: 3rem;
    height: 1.5rem;
    
    &::before {
        width: calc(1.5rem - 4px);
        height: calc(1.5rem - 4px);
    }
    
    &:checked::before {
        transform: translateX(1.5rem);
    }
}

.toggle-lg {
    width: 3.5rem;
    height: 1.75rem;
    
    &::before {
        width: calc(1.75rem - 4px);
        height: calc(1.75rem - 4px);
    }
    
    &:checked::before {
        transform: translateX(1.75rem);
    }
}

.toggle-xl {
    width: 4rem;
    height: 2rem;
    
    &::before {
        width: calc(2rem - 4px);
        height: calc(2rem - 4px);
    }
    
    &:checked::before {
        transform: translateX(2rem);
    }
}

// Application des classes color-* pour définir --color
.color-primary { --color: var(--color-primary-500); }
.color-secondary { --color: var(--color-secondary-500); }
.color-accent { --color: var(--color-accent-500); }
.color-info { --color: var(--color-info-500); }
.color-success { --color: var(--color-success-500); }
.color-warning { --color: var(--color-warning-500); }
.color-error { --color: var(--color-error-500); }
.color-neutral { --color: var(--color-neutral-500); }
</style>
