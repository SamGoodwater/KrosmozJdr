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
const props = defineProps({
    ...getInputPropsDefinition('select', 'core'),
    modelValue: {
        type: [String, Number, Array],
        default: null
    }
})
const emit = defineEmits(['update:modelValue', 'update:model-value'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'select', 'core')

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

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// Combine les classes DaisyUI natives (select-ghost, select-primary, etc.)
// avec les variants personnalisés (glass, dash, outline, soft) via getInputStyle
// https://daisyui.com/components/select/
// ------------------------------------------
const atomClasses = computed(() => {
    // Pour les variants personnalisés (glass, dash, outline, soft), utiliser getInputStyle
    // Pour ghost, utiliser directement select-ghost de DaisyUI
    const variant = props.variant || (typeof props.inputStyle === 'object' && props.inputStyle?.variant) || (typeof props.inputStyle === 'string' ? props.inputStyle : null);
    
    // Si c'est un variant personnalisé (non-ghost), utiliser getInputStyle
    if (variant && variant !== 'ghost' && ['glass', 'dash', 'outline', 'soft'].includes(variant)) {
        return mergeClasses(
            getInputStyle('select', {
                variant: variant,
                color: props.color,
                size: props.size,
                animation: props.animation,
                ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
                ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
            }, false)
        );
    }
    
    // Sinon, utiliser directement les classes DaisyUI
    const classes = ['select'];
    
    // Variant ghost (seul variant natif DaisyUI pour select)
    if (variant === 'ghost') {
        classes.push('select-ghost');
    }
    
    // Couleur (primary, secondary, accent, etc.) - classes DaisyUI natives
    const color = props.color || (typeof props.inputStyle === 'object' && props.inputStyle?.color);
    if (color) {
        classes.push(`select-${color}`);
    }
    
    // Taille (xs, sm, md, lg, xl) - classes DaisyUI natives
    const size = props.size || (typeof props.inputStyle === 'object' && props.inputStyle?.size) || 'md';
    if (size !== 'md') {
        classes.push(`select-${size}`);
    }
    
    return mergeClasses(classes);
})

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
        emit('update:model-value', selectedOptions);
    } else {
        // Pour single, on émet la valeur directement
        // - "" => null pour que Laravel "nullable" ignore correctement la règle integer
        const next = value === '' ? null : value;
        emit('update:modelValue', next);
        emit('update:model-value', next);
    }
}
</script>

<template>
    <!-- 🧱 Select simple sans label (SelectCore ne supporte pas les labels inline/floating) -->
    <select
        v-bind="inputAttrs"
        v-on="safeListeners"
        :class="atomClasses"
        :value="props.multiple ? undefined : (props.modelValue ?? '')"
        @change="onInput"
        @input="onInput"
    >
        <slot>
            <option
                v-if="!multiple && (inputAttrs.value === null || inputAttrs.value === undefined || inputAttrs.value === '')"
                value=""
                disabled
            >
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
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

select.select {
    // Styles de base pour tous les selects
    outline: none;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
    // IMPORTANT: Couleur de texte pour que la valeur sélectionnée soit visible
    color: hsl(var(--bc)); // Base-content (texte principal, s'adapte au thème)
    
    // Personnalisation de la flèche (appliquée à tous les variants)
    $arrow-svg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-image: $arrow-svg;
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    
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
    &.select-variant-glass {
        @apply border-glass-md box-glass-md;
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        background-image: $arrow-svg; // Réappliquer la flèche
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
            background-image: $arrow-svg;
        }
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
            background-image: $arrow-svg;
        }
    }
    
    // Variant Dash - Style pointillé
    &.select-variant-dash {
        @apply border-glass-sm;
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        background-image: $arrow-svg;
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
            background-image: $arrow-svg;
        }
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 60%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 15%, transparent);
            background-image: $arrow-svg;
        }
    }
    
    // Variant Outline - Bordure visible
    &.select-variant-outline {
        @apply border-glass-md;
        border-width: 2px;
        background-color: transparent;
        background-image: $arrow-svg;
        
        &:hover {
            @apply border-glass-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            background-image: $arrow-svg;
        }
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
            background-image: $arrow-svg;
        }
    }
    
    // Variant Ghost - Transparent
    &.select-variant-ghost {
        background-color: transparent;
        border-color: transparent;
        background-image: $arrow-svg;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            border-color: color-mix(in srgb, var(--color) 10%, transparent);
            background-image: $arrow-svg;
        }
        
        &:focus {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
            border-color: color-mix(in srgb, var(--color) 30%, transparent);
            background-image: $arrow-svg;
        }
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.select-variant-soft {
        @apply border-glass-b-md;
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        background-image: $arrow-svg;
        
        &:hover {
            @apply border-glass-b-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            background-image: $arrow-svg;
        }
        
        &:focus {
            border-bottom-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: none;
            background-image: $arrow-svg;
        }
    }
}

// Styles pour les options dans la liste déroulante
select {
    color-scheme: dark; // Force un thème sombre pour le menu déroulant
    
    option {
        padding: 0.75rem 1rem;
        background-color: hsl(var(--b1)); // Base-100 (fond principal du thème)
        color: hsl(var(--bc)); // Base-content (texte qui contraste avec le fond)
        transition: all 0.2s ease-in-out;
        min-height: 2.5rem;
        
        &:disabled {
            opacity: 0.5;
            font-style: italic;
            color: hsl(var(--bc) / 0.5);
            background-color: hsl(var(--b2)); // Base-200 (fond secondaire)
        }
        
        &:checked {
            background-color: hsl(var(--p));
            color: hsl(var(--pc));
            font-weight: 500;
        }
    }
    
    option[disabled] {
        color: hsl(var(--bc) / 0.5);
        font-style: italic;
        background-color: hsl(var(--b2));
    }
    
    &::-ms-expand {
        display: none; // Cache la flèche par défaut sur IE/Edge
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
