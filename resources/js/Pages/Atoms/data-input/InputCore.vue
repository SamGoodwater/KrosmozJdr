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
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

.input {
    // Styles de base pour tous les inputs
    outline: none;
    transition: all 0.2s ease-in-out;
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
    // Utilise les classes utilitaires border-glass-* et box-glass-*
    &.bg-transparent.border {
        @apply border-glass-md box-glass-md;
        
        // Utilise var(--color) pour les couleurs dynamiques
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
        }
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
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
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 60%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 15%, transparent);
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
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
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
        
        &:focus {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
            border-color: color-mix(in srgb, var(--color) 30%, transparent);
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
        
        &:focus {
            border-bottom-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: none;
        }
    }
}

// Application des classes color-* pour définir --color
// Ces classes sont générées par useInputStyle via getInputStyle
.color-primary { --color: var(--color-primary-500); }
.color-secondary { --color: var(--color-secondary-500); }
.color-accent { --color: var(--color-accent-500); }
.color-info { --color: var(--color-info-500); }
.color-success { --color: var(--color-success-500); }
.color-warning { --color: var(--color-warning-500); }
.color-error { --color: var(--color-error-500); }
.color-neutral { --color: var(--color-neutral-500); }
</style> 