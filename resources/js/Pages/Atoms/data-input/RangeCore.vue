<script setup>
defineOptions({ inheritAttrs: false });

/**
 * RangeCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les sliders range, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, min, max, step, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : range natif
 * - Gestion des attributs spécifiques aux ranges : min, max, step, value
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - RangeCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/range/
 * @version DaisyUI v5.x
 *
 * @example
 * <RangeCore v-model="value" min="0" max="100" />
 * <RangeCore v-model="value" min="0" max="100" color="primary" size="lg" />
 * <RangeCore v-model="value" min="0" max="100" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <RangeCore 
 *   v-model="value" 
 *   min="0" 
 *   max="100"
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {Number} modelValue - v-model (valeur numérique)
 * @props {Number} min - Valeur minimale
 * @props {Number} max - Valeur maximale
 * @props {Number} step - Pas d'incrémentation
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - range natif (optionnel)
 */
/**
 * [MIGRATION 2024-06] Ce composant utilise désormais inputHelper.js pour la gestion factorisée des props/attrs input (voir /Utils/atomic-design/inputHelper.js)
 */
import { computed, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

const props = defineProps(getInputPropsDefinition('range', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'range', 'core')

const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('range', {
            variant: props.variant,
            color: props.color,
            size: props.size,
            animation: props.animation,
      ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
      ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
    }, false)
  )
)

function onInput(e) {
    emit('update:modelValue', parseFloat(e.target.value));
}
</script>

<template>
    <!-- 🧱 Range simple sans label (RangeCore ne supporte pas les labels inline/floating) -->
    <input
        type="range"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses"
        @input="onInput"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour RangeCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

input[type="range"].range {
    // Styles de base pour tous les ranges
    outline: none;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    appearance: none;
    width: 100%;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
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
    
    // Thumb (curseur)
    &::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--color-primary, #3b82f6);
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        &:active {
            transform: scale(1.05);
        }
    }
    
    &::-moz-range-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--color-primary, #3b82f6);
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        &:active {
            transform: scale(1.05);
        }
    }
    
    // Track (piste)
    &::-webkit-slider-track {
        background: #e5e7eb;
        border-radius: 3px;
        height: 6px;
    }
    
    &::-moz-range-track {
        background: #e5e7eb;
        border-radius: 3px;
        height: 6px;
        border: none;
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
        
        &::-webkit-slider-thumb {
            background: var(--color-primary, #3b82f6);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        &::-moz-range-thumb {
            background: var(--color-primary, #3b82f6);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        background: rgba(255, 255, 255, 0.05);
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        &::-webkit-slider-thumb {
            background: var(--color-secondary, #8b5cf6);
        }
        
        &::-moz-range-thumb {
            background: var(--color-secondary, #8b5cf6);
        }
    }
    
    // Variant Outline - Bordure avec effet
    &.border-2.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        &::-webkit-slider-thumb {
            background: var(--color-success, #10b981);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        &::-moz-range-thumb {
            background: var(--color-success, #10b981);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
    }
    
    // Variant Ghost - Fond invisible
    &.border.border-transparent.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        &::-webkit-slider-thumb {
            background: var(--color-neutral, #6b7280);
        }
        
        &::-moz-range-thumb {
            background: var(--color-neutral, #6b7280);
        }
    }
    
    // Variant Soft - Style doux
    &.border-b-2.border-gray-300.bg-transparent.rounded-none {
        background: rgba(255, 255, 255, 0.05);
        border-bottom-width: 2px;
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        &::-webkit-slider-thumb {
            background: var(--color-accent, #f59e0b);
        }
        
        &::-moz-range-thumb {
            background: var(--color-accent, #f59e0b);
        }
    }
    
    // Styles pour les couleurs DaisyUI
    &.range-primary {
        &::-webkit-slider-thumb {
            background: var(--color-primary, #3b82f6);
        }
        
        &::-moz-range-thumb {
            background: var(--color-primary, #3b82f6);
        }
    }
    
    &.range-secondary {
        &::-webkit-slider-thumb {
            background: var(--color-secondary, #8b5cf6);
        }
        
        &::-moz-range-thumb {
            background: var(--color-secondary, #8b5cf6);
        }
    }
    
    &.range-accent {
        &::-webkit-slider-thumb {
            background: var(--color-accent, #f59e0b);
        }
        
        &::-moz-range-thumb {
            background: var(--color-accent, #f59e0b);
        }
    }
    
    &.range-info {
        &::-webkit-slider-thumb {
            background: var(--color-info, #06b6d4);
        }
        
        &::-moz-range-thumb {
            background: var(--color-info, #06b6d4);
        }
    }
    
    &.range-success {
        &::-webkit-slider-thumb {
            background: var(--color-success, #10b981);
        }
        
        &::-moz-range-thumb {
            background: var(--color-success, #10b981);
        }
    }
    
    &.range-warning {
        &::-webkit-slider-thumb {
            background: var(--color-warning, #f59e0b);
        }
        
        &::-moz-range-thumb {
            background: var(--color-warning, #f59e0b);
        }
    }
    
    &.range-error {
        &::-webkit-slider-thumb {
            background: var(--color-error, #ef4444);
        }
        
        &::-moz-range-thumb {
            background: var(--color-error, #ef4444);
        }
    }
    
    &.range-neutral {
        &::-webkit-slider-thumb {
            background: var(--color-neutral, #6b7280);
        }
        
        &::-moz-range-thumb {
            background: var(--color-neutral, #6b7280);
        }
    }
    
    // Animations
    &.hover\\:scale-105:hover {
        transform: scale(1.05);
    }
    
    &.focus\\:scale-105:focus {
        transform: scale(1.05);
    }
    
    &.transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    
    &.duration-200 {
        transition-duration: 200ms;
    }
}

// Styles pour les tailles DaisyUI
.range-xs {
    height: 4px;
    
    &::-webkit-slider-thumb {
        width: 16px;
        height: 16px;
    }
    
    &::-moz-range-thumb {
        width: 16px;
        height: 16px;
    }
}

.range-sm {
    height: 5px;
    
    &::-webkit-slider-thumb {
        width: 18px;
        height: 18px;
    }
    
    &::-moz-range-thumb {
        width: 18px;
        height: 18px;
    }
}

.range-md {
    height: 6px;
    
    &::-webkit-slider-thumb {
        width: 20px;
        height: 20px;
    }
    
    &::-moz-range-thumb {
        width: 20px;
        height: 20px;
    }
}

.range-lg {
    height: 8px;
    
    &::-webkit-slider-thumb {
        width: 24px;
        height: 24px;
    }
    
    &::-moz-range-thumb {
        width: 24px;
        height: 24px;
    }
}

.range-xl {
    height: 10px;
    
    &::-webkit-slider-thumb {
        width: 28px;
        height: 28px;
    }
    
    &::-moz-range-thumb {
        width: 28px;
        height: 28px;
    }
}

// Styles pour les labels inline
.label-text {
    // Labels inline pour les ranges
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les labels flottants
.floating-label {
    // Label flottant pour les ranges
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
