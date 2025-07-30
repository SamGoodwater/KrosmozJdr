<script setup>
defineOptions({ inheritAttrs: false });

/**
 * InputCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs input, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : type, v-model, placeholder, disabled, readonly, color, size, variant, style, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputClasses pour les classes DaisyUI/Tailwind
 * - Slot par défaut : input natif
 * - Gère tous les types d'input : text, email, password, number, url, tel, search, date, etc.
 * - Utilise le système de labels inline de DaisyUI pour éviter les divs englobantes
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputCore type="text" v-model="name" placeholder="Nom" />
 * <InputCore type="password" v-model="password" placeholder="Mot de passe" />
 * <InputCore type="email" v-model="email" color="primary" size="lg" />
 * <InputCore type="text" v-model="search" variant="glass" rounded="full" />
 * 
 * // Avec objet style
 * <InputCore 
 *   type="text" 
 *   v-model="name" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {String} type - Type d'input (text, email, password, number, url, tel, search, date, etc.)
 * @props {String} modelValue - v-model
 * @props {String} placeholder
 * @props {Boolean} disabled, readonly
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} labelInStart - Label inline à gauche (dans la balise label)
 * @props {String} labelInEnd - Label inline à droite (dans la balise label)
 * @props {Boolean} labelFloating - Active le mode floating label
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot labelInStart - Slot pour label inline à gauche
 * @slot labelInEnd - Slot pour label inline à droite
 * @slot floatingLabel - Slot pour label flottant
 * @slot default - input natif (optionnel)
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
const props = defineProps(getInputPropsDefinition('input', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'input', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
    getInputStyle(props.type || 'text', {
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
// 🏷️ Détection de labels inline (inStart / inEnd)
// ------------------------------------------
const shouldShowInlineLabels = computed(() =>
  !props.labelFloating && (props.labelInEnd || props.labelInStart)
)

const labelClasses = computed(() =>
  props.labelFloating
    ? mergeClasses(atomClasses.value, 'floating-label')
    : atomClasses.value
)
</script>

<template>
  <!-- 🔤 Label inline (inStart / inEnd) -->
  <label v-if="shouldShowInlineLabels" :class="labelClasses">
    <!-- ⬅️ Label inStart -->
    <span v-if="labelInStart || $slots.labelInStart" class="label-text">
      <slot name="labelInStart">{{ labelInStart }}</slot>
    </span>

    <!-- 🧱 Input principal -->
    <input
      v-bind="inputAttrs"
      v-on="listeners"
      :class="atomClasses"
    />

    <!-- ➡️ Label inEnd -->
    <span v-if="labelInEnd || $slots.labelInEnd" class="label-text">
      <slot name="labelInEnd">{{ labelInEnd }}</slot>
    </span>
  </label>

  <!-- 💬 Floating label -->
  <label v-else-if="props.labelFloating" :class="labelClasses">
    <input
      v-bind="inputAttrs"
      v-on="listeners"
      :class="atomClasses"
    />
    <span class="label-text">
      <slot name="floatingLabel">{{ props.placeholder || 'Label' }}</slot>
    </span>
  </label>

  <!-- 🧱 Input simple sans label -->
  <input
    v-else
    v-bind="inputAttrs"
    v-on="listeners"
    :class="atomClasses"
  />
</template>

<style scoped lang="scss">
// Styles spécifiques pour InputCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.input {
    // Styles de base pour tous les inputs
    outline: none;
    transition: all 0.2s ease-in-out;
    
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
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        background: rgba(255, 255, 255, 0.05);
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    }
    
    // Variant Outline - Bordure avec effet
    &.border-2.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    }
    
    // Variant Ghost - Fond invisible
    &.border.border-transparent.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
    }
    
    // Variant Soft - Style doux
    &.border-b-2.border-gray-300.bg-transparent.rounded-none {
        background: rgba(255, 255, 255, 0.05);
        border-bottom-width: 2px;
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    }
}

// Styles spécifiques pour les types d'input spéciaux
.range {
    // Range inputs
    &.range-primary {
        &::-webkit-slider-thumb {
            transition: all 0.2s ease-in-out;
        }
        
        &::-moz-range-thumb {
            transition: all 0.2s ease-in-out;
        }
    }
}

.checkbox, .radio {
    // Checkbox et Radio
    &.checkbox-primary, &.radio-primary {
        transition: all 0.2s ease-in-out;
        
        &:checked {
            transform: scale(1.1);
        }
    }
}

.toggle {
    // Toggle switches
    &.toggle-primary {
        transition: all 0.3s ease-in-out;
        
        &:checked {
            transform: translateX(1.5rem);
        }
    }
}
</style> 