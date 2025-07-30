<script setup>
defineOptions({ inheritAttrs: false });

/**
 * SelectCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs select, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, options, multiple, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : options natives
 * - Gestion des attributs spécifiques aux selects : multiple, size
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - SelectCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/select/
 * @version DaisyUI v5.x
 *
 * @example
 * <SelectCore v-model="selected" :options="['Option 1', 'Option 2', 'Option 3']" />
 * <SelectCore v-model="selected" :options="options" multiple color="primary" size="lg" />
 * <SelectCore v-model="selected" variant="glass" rounded="lg">
 *   <option value="">Choisir...</option>
 *   <option value="1">Option 1</option>
 *   <option value="2">Option 2</option>
 * </SelectCore>
 * 
 * // Avec objet style
 * <SelectCore 
 *   v-model="selected" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {String|Array|Number} modelValue - v-model (string, array pour multiple, ou number)
 * @props {Array} options - Options du select (array de strings ou objets {value, label, disabled})
 * @props {Boolean} multiple - Sélection multiple
 * @props {Number} size - Nombre d'options visibles (pour multiple)
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - options natives (optionnel)
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
const props = defineProps(getInputPropsDefinition('select', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'select', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('select', {
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
// 📋 Traitement des options pour l'affichage
// ------------------------------------------
const processedOptions = computed(() => {
    if (!props.options || !Array.isArray(props.options)) return [];
    
    return props.options.map(option => {
        if (typeof option === 'string') {
            return { value: option, label: option, disabled: false };
        } else if (typeof option === 'object' && option !== null) {
            return {
                value: option.value ?? option,
                label: option.label ?? option.value ?? option,
                disabled: option.disabled ?? false
            };
        }
        return { value: option, label: String(option), disabled: false };
    });
});

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onInput(e) {
    const value = e.target.value;
    if (props.multiple) {
        // Pour multiple, on gère un array
        const selectedOptions = Array.from(e.target.selectedOptions).map(option => option.value);
        emit('update:modelValue', selectedOptions);
    } else {
        // Pour single, on émet la valeur directement
        emit('update:modelValue', value);
    }
}
</script>

<template>
    <!-- 🧱 Select simple sans label (SelectCore ne supporte pas les labels inline/floating) -->
    <select
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses"
        @input="onInput"
    >
        <slot>
            <option v-if="!multiple" value="" disabled selected>
                {{ props.placeholder || 'Choisir...' }}
            </option>
            <option
                v-for="option in processedOptions"
                :key="option.value"
                :value="option.value"
                :disabled="option.disabled"
            >
                {{ option.label }}
            </option>
        </slot>
    </select>
</template>

<style scoped lang="scss">
// Styles spécifiques pour SelectCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.select {
    // Styles de base pour tous les selects
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
    
    // Personnalisation de la flèche
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    
    // Variant Glass - Effet de verre
    &.bg-transparent.border.border-gray-300 {
        background-color: rgba(255, 255, 255, 0.1);
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
        
        &:focus {
            border-color: var(--color-primary, #3b82f6);
            box-shadow: 
                0 0 0 3px rgba(59, 130, 246, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        background-color: rgba(255, 255, 255, 0.05);
        
        &:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        &:focus {
            background-color: white;
            border-color: var(--color-secondary, #8b5cf6);
        }
    }
    
    // Variant Outline - Bordure avec effet
    &.border-2.bg-transparent {
        &:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        &:focus {
            border-color: var(--color-success, #10b981);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
    }
    
    // Variant Ghost - Fond invisible
    &.border.border-transparent.bg-transparent {
        &:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        &:focus {
            background-color: white;
            border-color: var(--color-neutral, #6b7280);
        }
    }
    
    // Variant Soft - Style doux
    &.border-b-2.border-gray-300.bg-transparent.rounded-none {
        background-color: rgba(255, 255, 255, 0.05);
        border-bottom-width: 2px;
        
        &:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        &:focus {
            border-bottom-color: var(--color-accent, #f59e0b);
            box-shadow: none;
        }
    }
    
    // Styles pour les couleurs DaisyUI
    &.select-primary {
        &:focus {
            border-color: var(--color-primary, #3b82f6);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    }
    
    &.select-secondary {
        &:focus {
            border-color: var(--color-secondary, #8b5cf6);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
    }
    
    &.select-accent {
        &:focus {
            border-color: var(--color-accent, #f59e0b);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
    }
    
    &.select-info {
        &:focus {
            border-color: var(--color-info, #06b6d4);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }
    }
    
    &.select-success {
        &:focus {
            border-color: var(--color-success, #10b981);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
    }
    
    &.select-warning {
        &:focus {
            border-color: var(--color-warning, #f59e0b);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
    }
    
    &.select-error {
        &:focus {
            border-color: var(--color-error, #ef4444);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
    }
    
    &.select-neutral {
        &:focus {
            border-color: var(--color-neutral, #6b7280);
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.1);
        }
    }
}

// Styles pour les options
option {
    // Options du select
    padding: 0.5rem;
    transition: all 0.2s ease-in-out;
    
    &:disabled {
        opacity: 0.5;
        font-style: italic;
    }
    
    &:checked {
        background-color: var(--color-primary, #3b82f6);
        color: white;
    }
}

// Styles pour les tailles DaisyUI
.select-xs {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    padding-right: 2rem;
}

.select-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    padding-right: 2.25rem;
}

.select-md {
    font-size: 1rem;
    padding: 0.5rem 1rem;
    padding-right: 2.5rem;
}

.select-lg {
    font-size: 1.125rem;
    padding: 0.75rem 1.5rem;
    padding-right: 3rem;
}

.select-xl {
    font-size: 1.25rem;
    padding: 1rem 2rem;
    padding-right: 3.5rem;
}
</style>
