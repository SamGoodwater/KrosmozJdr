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
const emit = defineEmits(['update:modelValue', 'update:model-value'])
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
    emit('update:model-value', e.target.value);
}

function onKeydown(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        emit('update:modelValue', props.value);
        emit('update:model-value', props.value);
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
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

input[type="radio"] {
    // Styles de base pour tous les radios
    outline: none;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
    // États de focus
    &:focus {
        outline: none;
    }
    
    // États disabled
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    // Variant Glass - Effet glassmorphisme
    &.bg-transparent.border {
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
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
        border-style: dashed;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
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
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-bottom-color: var(--color);
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

// Styles pour les tailles DaisyUI (conservés car spécifiques au radio)
.radio-xs, .radio.imput-xs{
    width: 0.70rem;
    height: 0.70rem;
}

.radio-sm, .radio.input-sm {
    width: 0.85rem;
    height: 0.85rem;
}

.radio-md, .radio.input-md {
    width: 1.25rem;
    height: 1.25rem;
}

.radio-lg, .radio.input-lg {
    width: 1.5rem;
    height: 1.5rem;
}

.radio-xl, .radio.input-xl {
    width: 2rem;
    height: 2rem;
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
