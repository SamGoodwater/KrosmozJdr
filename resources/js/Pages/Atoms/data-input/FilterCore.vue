<script setup>
defineOptions({ inheritAttrs: false });

/**
 * FilterCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs de filtrage, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, options, multiple, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : options natives
 * - Gestion des attributs spécifiques aux filtres : radio buttons, reset
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 *
 * @see https://daisyui.com/components/filter/
 * @version DaisyUI v5.x
 *
 * @example
 * <FilterCore v-model="filter" :options="['Tous', 'Actifs', 'Inactifs']" />
 * <FilterCore v-model="filter" :options="filterOptions" multiple color="primary" size="lg" />
 * <FilterCore v-model="filter" variant="glass" rounded="lg">
 *   <input type="radio" name="filter" value="all" aria-label="Tous" />
 *   <input type="radio" name="filter" value="active" aria-label="Actifs" />
 *   <input type="radio" name="filter" value="inactive" aria-label="Inactifs" />
 * </FilterCore>
 * 
 * // Avec objet style
 * <FilterCore 
 *   v-model="filter" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {String|Array|Number} modelValue - v-model (string, array pour multiple, ou number)
 * @props {Array} options - Options du filter (array de strings ou objets {value, label, disabled})
 * @props {Boolean} multiple - Sélection multiple
 * @props {String} name - Nom du groupe de radio buttons
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
 * @slot default - radio buttons natives (optionnel)
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
const props = defineProps(getInputPropsDefinition('filter', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'filter', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
    getInputStyle(props.type || 'filter', {
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

// Traitement des options
const processedOptions = computed(() => {
    if (!props.options) return [];
    return Array.isArray(props.options) ? props.options : Object.values(props.options);
});

// Gestion du v-model pour filter
const currentFilter = computed({
    get() {
        return props.modelValue || '';
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

// État de sélection
const isSelected = computed(() => {
    return props.checked || false;
});

function onReset() {
    emit('update:modelValue', '');
}

function onRadioChange(e) {
    emit('update:modelValue', e.target.value);
}
</script>

<template>
    <!-- Filter simple sans label (FilterCore ne supporte pas les labels inline/floating) -->
    <div :class="['filter-container', atomClasses]">
        <slot>
            <!-- Bouton reset -->
            <input 
                v-if="modelValue && !multiple"
                type="radio" 
                v-bind="inputAttrs"
                v-on="listeners"
                class="btn filter-reset" 
                value="" 
                aria-label="×"
                @change="onReset"
            />
            <!-- Options radio -->
            <input
                v-for="option in processedOptions"
                :key="option.value"
                type="radio"
                v-bind="inputAttrs"
                v-on="listeners"
                :value="option.value"
                :disabled="option.disabled"
                :checked="modelValue === option.value"
                class="btn"
                :aria-label="option.label"
                @change="onRadioChange"
            />
        </slot>
    </div>
</template>

<style scoped lang="scss">
// Styles spécifiques pour FilterCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

.filter-container {
    // Container pour les filtres
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    
    // Styles de base pour tous les filtres
    outline: none;
    transition: all 0.2s ease-in-out;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
    // États de focus
    &:focus-within {
        outline: none;
    }
    
    // États disabled
    &:has(input:disabled) {
        opacity: 0.6;
        pointer-events: none;
    }
    
    // Variant Glass - Effet glassmorphisme
    &.bg-transparent.border {
        @apply border-glass-md box-glass-md;
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        border-radius: 0.5rem;
        padding: 0.5rem;
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
        }
        
        &:focus-within {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: 
                0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed {
        @apply border-glass-sm;
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        border-radius: 0.5rem;
        padding: 0.5rem;
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:focus-within {
            border-color: color-mix(in srgb, var(--color) 60%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        @apply border-glass-md;
        border-width: 2px;
        background-color: transparent;
        border-radius: 0.5rem;
        padding: 0.5rem;
        
        &:hover {
            @apply border-glass-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:focus-within {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
        }
    }
    
    // Variant Ghost - Transparent
    &.border.border-transparent.bg-transparent {
        background-color: transparent;
        border-color: transparent;
        border-radius: 0.5rem;
        padding: 0.5rem;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            border-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:focus-within {
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
        padding: 0.5rem;
        
        &:hover {
            @apply border-glass-b-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:focus-within {
            border-bottom-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: none;
        }
    }
}

// Styles pour les boutons radio
.btn {
    // Boutons radio dans les filtres
    transition: all 0.2s ease-in-out;
    border-radius: 0.375rem;
    font-weight: 500;
    
    // Bouton reset - utilise var(--color-error)
    &.filter-reset {
        --color: var(--color-error-500);
        background-color: var(--color);
        color: white;
        border: 1px solid var(--color);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 90%, transparent);
            border-color: color-mix(in srgb, var(--color) 90%, transparent);
            transform: scale(1.05);
        }
        
        &:focus {
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
        }
    }
    
    // Boutons radio normaux - utilise var(--color)
    &:not(.filter-reset) {
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--color) 30%, transparent);
        color: var(--color-base-content, #374151);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 20%, transparent);
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            transform: translateY(-1px);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
            color: white;
            transform: scale(1.05);
        }
        
        &:focus {
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
        }
        
        &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            
            &:hover {
                transform: none;
            }
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

// Styles pour les tailles DaisyUI
.filter-xs .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.filter-sm .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.filter-md .btn {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.filter-lg .btn {
    font-size: 1.125rem;
    padding: 0.75rem 1.5rem;
}

.filter-xl .btn {
    font-size: 1.25rem;
    padding: 1rem 2rem;
}
</style>
