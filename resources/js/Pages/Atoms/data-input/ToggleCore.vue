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
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

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
    background-color: #d1d5db;
    border-radius: 9999px;
    border: none;
    
    // États de focus
    &:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    // État activé
    &:checked {
        background-color: var(--color-primary, #3b82f6);
        
        &::before {
            transform: translateX(1.5rem);
        }
    }
    
    // Variant Glass - Effet de verre
    &.bg-transparent.border.border-gray-300 {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-color: rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        
        &:hover {
            box-shadow: 
                0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        &:checked {
            background-color: var(--color-primary, #3b82f6);
            border-color: var(--color-primary, #3b82f6);
            box-shadow: 
                0 0 0 3px rgba(59, 130, 246, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        background: rgba(255, 255, 255, 0.05);
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        &:checked {
            background-color: var(--color-secondary, #8b5cf6);
            border-color: var(--color-secondary, #8b5cf6);
        }
    }
    
    // Variant Outline - Bordure avec effet
    &.border-2.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        &:checked {
            background-color: var(--color-success, #10b981);
            border-color: var(--color-success, #10b981);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
    }
    
    // Variant Ghost - Fond invisible
    &.border.border-transparent.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        &:checked {
            background-color: var(--color-neutral, #6b7280);
            border-color: var(--color-neutral, #6b7280);
        }
    }
    
    // Variant Soft - Style doux
    &.border-b-2.border-gray-300.bg-transparent.rounded-none {
        background: rgba(255, 255, 255, 0.05);
        border-bottom-width: 2px;
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        &:checked {
            background-color: var(--color-accent, #f59e0b);
            border-bottom-color: var(--color-accent, #f59e0b);
        }
    }
    
    // Styles pour les couleurs DaisyUI
    &.toggle-primary {
        &:checked {
            background-color: var(--color-primary, #3b82f6);
        }
    }
    
    &.toggle-secondary {
        &:checked {
            background-color: var(--color-secondary, #8b5cf6);
        }
    }
    
    &.toggle-accent {
        &:checked {
            background-color: var(--color-accent, #f59e0b);
        }
    }
    
    &.toggle-info {
        &:checked {
            background-color: var(--color-info, #06b6d4);
        }
    }
    
    &.toggle-success {
        &:checked {
            background-color: var(--color-success, #10b981);
        }
    }
    
    &.toggle-warning {
        &:checked {
            background-color: var(--color-warning, #f59e0b);
        }
    }
    
    &.toggle-error {
        &:checked {
            background-color: var(--color-error, #ef4444);
        }
    }
    
    &.toggle-neutral {
        &:checked {
            background-color: var(--color-neutral, #6b7280);
        }
    }
    
    // Animations
    &.hover\\:scale-105:hover {
        transform: scale(1.05);
    }
    
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

// Styles pour les tailles DaisyUI
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

// Styles pour les labels inline
.label-text {
    // Labels inline pour les toggles
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les labels flottants
.floating-label {
    // Label flottant pour les toggles
    position: relative;
    
    .label-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        transition: all 0.2s ease-in-out;
    }
}
</style>
