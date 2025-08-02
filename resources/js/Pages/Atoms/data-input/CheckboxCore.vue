<script setup>
defineOptions({ inheritAttrs: false });

/**
 * CheckboxCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs checkbox, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : checkbox natif
 * - Gestion des attributs spécifiques aux checkboxes : indeterminate, checked
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - CheckboxCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/checkbox/
 * @version DaisyUI v5.x
 *
 * @example
 * <CheckboxCore v-model="checked" />
 * <CheckboxCore v-model="checked" color="primary" size="lg" />
 * <CheckboxCore v-model="checked" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <CheckboxCore 
 *   v-model="checked" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {Boolean|Array} modelValue - v-model (boolean pour single, array pour multiple)
 * @props {String} value - Valeur du checkbox (pour les groupes)
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - checkbox natif (optionnel)
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
const props = defineProps(getInputPropsDefinition('checkbox', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'checkbox', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('checkbox', {
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
// 🎯 Gestion du v-model pour checkbox
// ------------------------------------------
const isChecked = computed({
    get() {
        if (Array.isArray(props.modelValue)) {
            // Mode multiple : vérifier si la valeur est dans l'array
            return props.modelValue.includes(props.value);
        } else {
            // Mode single : valeur booléenne
            return !!props.modelValue;
        }
    },
    set(value) {
        if (Array.isArray(props.modelValue)) {
            // Mode multiple : ajouter/retirer de l'array
            const newValue = [...props.modelValue];
            if (value) {
                if (!newValue.includes(props.value)) {
                    newValue.push(props.value);
                }
            } else {
                const index = newValue.indexOf(props.value);
                if (index > -1) {
                    newValue.splice(index, 1);
                }
            }
            emit('update:modelValue', newValue);
        } else {
            // Mode single : émettre la valeur booléenne
            emit('update:modelValue', value);
        }
    }
});

// ------------------------------------------
// 🔄 Gestion de l'état indeterminate
// ------------------------------------------
const isIndeterminate = computed(() => {
    if (Array.isArray(props.modelValue)) {
        // Pour les arrays, indeterminate si certains éléments sont sélectionnés mais pas tous
        const allValues = props.options || [];
        const selectedCount = props.modelValue.length;
        return selectedCount > 0 && selectedCount < allValues.length;
    }
    return false;
});

const checkboxRef = ref(null);

// Mise à jour de l'état indeterminate sur le DOM
watch(isIndeterminate, (value) => {
    if (checkboxRef.value) {
        checkboxRef.value.indeterminate = value;
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
    <!-- 🧱 Checkbox simple sans label (CheckboxCore ne supporte pas les labels inline/floating) -->
    <input
        ref="checkboxRef"
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
// Styles spécifiques pour CheckboxCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

input[type="checkbox"] {
    // Styles de base pour tous les checkboxes
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
    &.checkbox-primary {
        &:checked {
            background-color: var(--color-primary, #3b82f6);
            border-color: var(--color-primary, #3b82f6);
        }
    }
    
    &.checkbox-secondary {
        &:checked {
            background-color: var(--color-secondary, #8b5cf6);
            border-color: var(--color-secondary, #8b5cf6);
        }
    }
    
    &.checkbox-accent {
        &:checked {
            background-color: var(--color-accent, #f59e0b);
            border-color: var(--color-accent, #f59e0b);
        }
    }
    
    &.checkbox-info {
        &:checked {
            background-color: var(--color-info, #06b6d4);
            border-color: var(--color-info, #06b6d4);
        }
    }
    
    &.checkbox-success {
        &:checked {
            background-color: var(--color-success, #10b981);
            border-color: var(--color-success, #10b981);
        }
    }
    
    &.checkbox-warning {
        &:checked {
            background-color: var(--color-warning, #f59e0b);
            border-color: var(--color-warning, #f59e0b);
        }
    }
    
    &.checkbox-error {
        &:checked {
            background-color: var(--color-error, #ef4444);
            border-color: var(--color-error, #ef4444);
        }
    }
    
    &.checkbox-neutral {
        &:checked {
            background-color: var(--color-neutral, #6b7280);
            border-color: var(--color-neutral, #6b7280);
        }
    }
    
    // États spéciaux
    &:indeterminate {
        background-color: var(--color-warning, #f59e0b);
        border-color: var(--color-warning, #f59e0b);
        
        &::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            height: 2px;
            background-color: white;
            border-radius: 1px;
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

// Styles pour les tailles DaisyUI
.checkbox-xs {
    width: 1rem;
    height: 1rem;
}

.checkbox-sm {
    width: 1.25rem;
    height: 1.25rem;
}

.checkbox-md {
    width: 1.5rem;
    height: 1.5rem;
}

.checkbox-lg {
    width: 1.75rem;
    height: 1.75rem;
}

.checkbox-xl {
    width: 2rem;
    height: 2rem;
}

// Styles pour les labels inline
.label-text {
    // Labels inline pour les checkboxes
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les labels flottants
.floating-label {
    // Label flottant pour les checkboxes
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
