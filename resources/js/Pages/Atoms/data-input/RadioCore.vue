<script setup>
defineOptions({ inheritAttrs: false });

/**
 * RadioCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les boutons radio, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, name, value, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : radio natif
 * - Gestion des attributs spécifiques aux radios : name, value, checked
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - RadioCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/radio/
 * @version DaisyUI v5.x
 *
 * @example
 * <RadioCore name="gender" value="male" v-model="selectedGender" />
 * <RadioCore name="gender" value="female" v-model="selectedGender" color="primary" size="lg" />
 * <RadioCore name="theme" value="dark" v-model="theme" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <RadioCore 
 *   name="option" 
 *   value="a" 
 *   v-model="selected" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {String} modelValue - v-model (valeur sélectionnée du groupe)
 * @props {String} name - Nom du groupe radio (obligatoire)
 * @props {String} value - Valeur de ce bouton radio
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - radio natif (optionnel)
 */

// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

// ------------------------------------------
// 🔧 Définition des props + emits
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('radio', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'radio', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('radio', {
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
// 🎯 Vérifier si ce radio est sélectionné
// ------------------------------------------
const isChecked = computed(() => {
    return props.modelValue === props.value;
});

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onInput(e) {
    emit('update:modelValue', e.target.value);
}

function onKeydown(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        emit('update:modelValue', props.value);
    }
}
</script>

<template>
    <!-- 🧱 Radio simple sans label (RadioCore ne supporte pas les labels inline/floating) -->
    <input
        type="radio"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses"
        :checked="isChecked"
        @input="onInput"
        @keydown="onKeydown"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour RadioCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

input[type="radio"] {
    // Styles de base pour tous les radios
    outline: none;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    
    // États de focus
    &:focus {
        outline: none;
    }
    
    // États disabled
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
    &.radio-primary {
        &:checked {
            background-color: var(--color-primary, #3b82f6);
            border-color: var(--color-primary, #3b82f6);
        }
    }
    
    &.radio-secondary {
        &:checked {
            background-color: var(--color-secondary, #8b5cf6);
            border-color: var(--color-secondary, #8b5cf6);
        }
    }
    
    &.radio-accent {
        &:checked {
            background-color: var(--color-accent, #f59e0b);
            border-color: var(--color-accent, #f59e0b);
        }
    }
    
    &.radio-info {
        &:checked {
            background-color: var(--color-info, #06b6d4);
            border-color: var(--color-info, #06b6d4);
        }
    }
    
    &.radio-success {
        &:checked {
            background-color: var(--color-success, #10b981);
            border-color: var(--color-success, #10b981);
        }
    }
    
    &.radio-warning {
        &:checked {
            background-color: var(--color-warning, #f59e0b);
            border-color: var(--color-warning, #f59e0b);
        }
    }
    
    &.radio-error {
        &:checked {
            background-color: var(--color-error, #ef4444);
            border-color: var(--color-error, #ef4444);
        }
    }
    
    &.radio-neutral {
        &:checked {
            background-color: var(--color-neutral, #6b7280);
            border-color: var(--color-neutral, #6b7280);
        }
    }
    
    // Animations
    &.hover\\:scale-110:checked {
        transform: scale(1.1);
    }
    
    &.checked\\:scale-110:checked {
        transform: scale(1.1);
    }
    
    &.transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    
    &.duration-200 {
        transition-duration: 200ms;
    }
}

// Styles pour les tailles DaisyUI
.radio-xs {
    width: 1rem;
    height: 1rem;
}

.radio-sm {
    width: 1.25rem;
    height: 1.25rem;
}

.radio-md {
    width: 1.5rem;
    height: 1.5rem;
}

.radio-lg {
    width: 1.75rem;
    height: 1.75rem;
}

.radio-xl {
    width: 2rem;
    height: 2rem;
}

// Styles pour les labels inline
.label-text {
    // Labels inline pour les radios
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les labels flottants
.floating-label {
    // Label flottant pour les radios
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
