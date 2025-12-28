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
const emit = defineEmits(['update:modelValue', 'update:model-value'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'checkbox', 'core')

/**
 * Important: CheckboxCore gère lui-même l'événement `input` pour émettre un booléen
 * via `e.target.checked`.
 *
 * Le système unifié `useInputField` attache aussi un listener `input` générique
 * (basé sur `e.target.value`) pour les inputs texte, ce qui provoque une double
 * émission (ex: "on") et casse la validation backend `boolean`.
 *
 * On filtre donc `input` (et `change` au besoin) des listeners reçus.
 */
const safeListeners = computed(() => {
    const l = (listeners && typeof listeners === 'object' && 'value' in listeners)
        ? (listeners.value || {})
        : (listeners || {});

    // eslint-disable-next-line no-unused-vars
    const { input, change, ...rest } = l;
    return rest;
});

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
            emit('update:model-value', newValue);
        } else {
            // Mode single : émettre la valeur booléenne
            emit('update:modelValue', value);
            emit('update:model-value', value);
        }
    }
});

// ------------------------------------------
// 🔄 Gestion de l'état indeterminate
// ------------------------------------------
const computedIndeterminate = computed(() => {
    if (Array.isArray(props.modelValue)) {
        // Pour les arrays, indeterminate si certains éléments sont sélectionnés mais pas tous
        const allValues = props.options || [];
        const selectedCount = props.modelValue.length;
        return selectedCount > 0 && selectedCount < allValues.length;
    }
    return false;
});

/**
 * Indeterminate effectif:
 * - support du mode "array" (calculé)
 * - support d'un override explicite via la prop `indeterminate` (utile pour des checkboxes contrôlées)
 */
const effectiveIndeterminate = computed(() => {
    return Boolean(props.indeterminate) || Boolean(computedIndeterminate.value);
});

const checkboxRef = ref(null);

// Mise à jour de l'état indeterminate sur le DOM
watch(effectiveIndeterminate, (value) => {
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
        v-on="safeListeners"
        :class="atomClasses"
        :checked="isChecked"
        :indeterminate="effectiveIndeterminate"
        @input="onInput"
        @keydown="onKeydown"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour CheckboxCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

input[type="checkbox"] {
    // Styles de base pour tous les checkboxes
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
        @apply border-glass-md box-glass-md;
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
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
        @apply border-glass-sm;
        border-style: dashed;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-color: var(--color);
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
        @apply border-glass-b-md;
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-b-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &:checked {
            background-color: var(--color);
            border-bottom-color: var(--color);
        }
    }
    
    // États spéciaux
    &:indeterminate {
        background-color: var(--color);
        border-color: var(--color);
        
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
