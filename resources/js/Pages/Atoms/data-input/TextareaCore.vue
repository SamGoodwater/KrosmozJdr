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
const emit = defineEmits(['update:modelValue', 'update:model-value'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'textarea', 'core')

/**
 * Valeur effective (compat):
 * - en usage "core" avec v-model : on reçoit `props.modelValue`
 * - en usage via les Molecules (TextareaField/FieldTemplate) : la valeur arrive souvent via `inputAttrs.value`
 */
const effectiveValue = computed(() => {
  // Priorité au v-model standard
  if (props.modelValue !== null && props.modelValue !== undefined) {
    return props.modelValue;
  }
  // Fallback: valeur HTML passée via v-bind="inputAttrs"
  const v = inputAttrs?.value?.value;
  return (v !== null && v !== undefined) ? v : '';
});

/**
 * Listener safe pour éviter de gérer deux fois `input` (nous l'utilisons pour v-model).
 */
const safeListeners = computed(() => {
  const l = (listeners && typeof listeners === 'object' && 'value' in listeners)
    ? (listeners.value || {})
    : (listeners || {});
  // eslint-disable-next-line no-unused-vars
  const { input, ...rest } = l;
  return rest;
});

function onInput(e) {
  const next = e?.target?.value ?? '';
  emit('update:modelValue', next);
  emit('update:model-value', next);

  // relayer un éventuel listener `input` passé via $attrs (utilisé par TextareaField/useInputField)
  const l = (listeners && typeof listeners === 'object' && 'value' in listeners)
    ? (listeners.value || {})
    : (listeners || {});
  if (typeof l?.input === 'function') {
    l.input(e);
  }
}

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
            v-on="safeListeners"
            :class="atomClasses"
            :value="effectiveValue"
            @input="onInput"
        />
        <span v-if="labelInEnd || $slots.labelInEnd" class="label-text">
            <slot name="labelInEnd">{{ labelInEnd }}</slot>
        </span>
    </label>
    
    <!-- Structure pour floating label -->
    <label :class="labelClasses" v-else-if="labelFloating || $slots.floatingLabel">
        <textarea
            v-bind="inputAttrs"
            v-on="safeListeners"
            :class="atomClasses"
            :value="effectiveValue"
            @input="onInput"
        />
        <span class="label-text">
            <slot name="floatingLabel">{{ props.placeholder || 'Label' }}</slot>
        </span>
    </label>
    
    <!-- Textarea simple sans label -->
    <textarea
        v-else
        v-bind="inputAttrs"
        v-on="safeListeners"
        :class="atomClasses"
        :value="effectiveValue"
        @input="onInput"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour TextareaCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

textarea.textarea {
    // Styles de base pour tous les textareas
    outline: none;
    transition: all 0.2s ease-in-out;
    resize: vertical; // Permet le redimensionnement vertical uniquement
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
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
    
    // Variant Glass - Effet glassmorphisme
    &.bg-transparent.border {
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
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
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 60%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 15%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
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
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:focus {
            border-bottom-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: none;
        }
    }
    
    // Personnalisation de la scrollbar pour les textareas (utilise var(--color))
    &::-webkit-scrollbar {
        width: 8px;
    }
    
    &::-webkit-scrollbar-track {
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        border-radius: 4px;
    }
    
    &::-webkit-scrollbar-thumb {
        background-color: color-mix(in srgb, var(--color) 30%, transparent);
        border-radius: 4px;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 50%, transparent);
        }
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
