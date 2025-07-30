<script setup>
defineOptions({ inheritAttrs: false });

/**
 * TextareaCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les zones de texte, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, placeholder, rows, cols, maxlength, minlength, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : textarea natif
 * - Gestion de la validation visuelle avec aria-invalid
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 *
 * @see https://daisyui.com/components/textarea/
 * @version DaisyUI v5.x
 *
 * @example
 * <TextareaCore v-model="bio" placeholder="Votre biographie" rows="4" />
 * <TextareaCore v-model="description" color="primary" size="lg" />
 * <TextareaCore v-model="notes" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <TextareaCore 
 *   v-model="content" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {String} modelValue - v-model
 * @props {String} placeholder
 * @props {Number} rows, cols, maxlength, minlength
 * @props {Boolean} disabled, readonly, required
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
 * @slot default - textarea natif (optionnel)
 */
/**
 * [MIGRATION 2024-06] Ce composant utilise désormais inputHelper.js pour la gestion factorisée des props/attrs input (voir /Utils/atomic-design/inputHelper.js)
 */
import { computed, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

// ------------------------------------------
// 🔧 Définition des props + emits
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('textarea', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'textarea', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
    getInputStyle(props.type || 'textarea', {
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
    <!-- Structure pour labels inline (labelInStart/labelInEnd) -->
    <label :class="labelClasses" v-if="shouldShowInlineLabels">
        <span v-if="labelInStart || $slots.labelInStart" class="label-text">
            <slot name="labelInStart">{{ labelInStart }}</slot>
        </span>
        <textarea
            v-bind="inputAttrs"
            v-on="listeners"
            :class="atomClasses"
        />
        <span v-if="labelInEnd || $slots.labelInEnd" class="label-text">
            <slot name="labelInEnd">{{ labelInEnd }}</slot>
        </span>
    </label>
    
    <!-- Structure pour floating label -->
    <label :class="labelClasses" v-else-if="labelFloating || $slots.floatingLabel">
        <textarea
            v-bind="inputAttrs"
            v-on="listeners"
            :class="atomClasses"
        />
        <span class="label-text">
            <slot name="floatingLabel">{{ props.placeholder || 'Label' }}</slot>
        </span>
    </label>
    
    <!-- Textarea simple sans label -->
    <textarea
        v-else
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour TextareaCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.textarea {
    // Styles de base pour tous les textareas
    outline: none;
    transition: all 0.2s ease-in-out;
    resize: vertical; // Permet le redimensionnement vertical uniquement
    
    // États de focus
    &:focus {
        outline: none;
    }
    
    // États disabled
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        resize: none; // Désactive le redimensionnement si disabled
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
    
    // Personnalisation de la scrollbar pour les textareas
    &::-webkit-scrollbar {
        width: 8px;
    }
    
    &::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
    
    &::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
        
        &:hover {
            background: rgba(0, 0, 0, 0.5);
        }
    }
}
</style>
