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
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

input[type="range"].range {
    // Styles de base pour tous les ranges
    outline: none;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    appearance: none;
    width: 100%;
    height: 6px;
    border-radius: 3px;
    border: none;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    background-color: color-mix(in srgb, var(--color) 10%, transparent);
    
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
    
    // Thumb (curseur) - utilise var(--color)
    &::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--color);
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px color-mix(in srgb, var(--color) 30%, transparent);
        transition: all 0.2s ease-in-out;
        
        &:hover {
            box-shadow: 0 4px 8px color-mix(in srgb, var(--color) 40%, transparent);
            transform: scale(1.1);
        }
    }
    
    &::-moz-range-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--color);
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px color-mix(in srgb, var(--color) 30%, transparent);
        transition: all 0.2s ease-in-out;
        
        &:hover {
            box-shadow: 0 4px 8px color-mix(in srgb, var(--color) 40%, transparent);
            transform: scale(1.1);
        }
    }
    
    // Track (piste) - utilise var(--color) avec transparence
    &::-webkit-slider-track {
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        border-radius: 3px;
        height: 6px;
    }
    
    &::-moz-range-track {
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        border-radius: 3px;
        height: 6px;
        border: none;
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
        
        &::-webkit-slider-thumb {
            background: var(--color);
            border-color: color-mix(in srgb, white 80%, transparent);
            box-shadow: 
                0 2px 4px color-mix(in srgb, var(--color) 30%, transparent),
                inset 0 1px 0 color-mix(in srgb, white 20%, transparent);
        }
        
        &::-moz-range-thumb {
            background: var(--color);
            border-color: color-mix(in srgb, white 80%, transparent);
            box-shadow: 
                0 2px 4px color-mix(in srgb, var(--color) 30%, transparent),
                inset 0 1px 0 color-mix(in srgb, white 20%, transparent);
        }
        
        &::-webkit-slider-track {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &::-moz-range-track {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed {
        @apply border-glass-sm;
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &::-webkit-slider-thumb {
            background: var(--color);
        }
        
        &::-moz-range-thumb {
            background: var(--color);
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
        
        &::-webkit-slider-thumb {
            background: var(--color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
        }
        
        &::-moz-range-thumb {
            background: var(--color);
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
        
        &::-webkit-slider-thumb {
            background: var(--color);
        }
        
        &::-moz-range-thumb {
            background: var(--color);
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
        
        &::-webkit-slider-thumb {
            background: var(--color);
        }
        
        &::-moz-range-thumb {
            background: var(--color);
        }
    }
    
    // Animations
    &.transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    
    &.duration-200 {
        transition-duration: 200ms;
    }
}

// Styles pour les tailles DaisyUI (conservés car spécifiques au range)
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
