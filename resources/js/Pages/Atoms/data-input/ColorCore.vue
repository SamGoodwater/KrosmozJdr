<script setup>
defineOptions({ inheritAttrs: false });

/**
 * ColorCore Atom (DaisyUI + vue-color-kit, Atomic Design)
 *
 * @description
 * Atom de base pour les sélecteurs de couleur, utilisant le composant vue-color-kit avec styles DaisyUI.
 * - Props : v-model, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : ColorPicker natif avec vue-color-kit
 * - Gestion des attributs spécifiques aux couleurs : format, theme, palette
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Intégration avec le composant vue-color-kit pour Vue 3
 * - Fallback vers input HTML natif type="color" si vue-color-kit n'est pas disponible
 * - ColorCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://www.vuescript.com/color-picker-kit/
 * @version DaisyUI v5.x + vue-color-kit
 *
 * @example
 * <ColorCore v-model="color" />
 * <ColorCore v-model="color" color="primary" size="lg" />
 * <ColorCore v-model="color" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <ColorCore 
 *   v-model="color" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {String} modelValue - v-model (couleur actuelle)
 * @props {String} format - Format de couleur (hex, rgb, rgba, hsl, hsla)
 * @props {String} theme - Thème du color picker (light, dark)
 * @props {Array} colorsDefault - Palette de couleurs par défaut
 * @props {String} colorsHistoryKey - Clé pour l'historique des couleurs
 * @props {Boolean} suckerHide - Masquer le pipette
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - Contenu personnalisé du color picker
 */

// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { ref, computed, onMounted, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

// ------------------------------------------
// 🔧 Définition des props + emits
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('color', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'color', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
    getInputStyle('color', {
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
// 🎨 Import dynamique de vue-color-kit
// ------------------------------------------
let ColorPicker = null;
const isColorKitReady = ref(false);

// Fonction pour charger vue-color-kit
async function loadColorKit() {
    try {
        const colorKit = await import('vue-color-kit');
        ColorPicker = colorKit.ColorPicker;
        isColorKitReady.value = true;
    } catch (error) {
        console.warn('vue-color-kit non disponible, utilisation du fallback HTML natif');
        isColorKitReady.value = false;
    }
}

// Charger vue-color-kit au montage
onMounted(() => {
    loadColorKit();
});

// ------------------------------------------
// 🎨 Gestion du v-model pour color
// ------------------------------------------
const currentColor = computed({
    get() {
        return props.modelValue || '#000000';
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

// ------------------------------------------
// ⚙️ Configuration du color picker
// ------------------------------------------
const colorPickerConfig = computed(() => ({
    format: props.format || 'hex',
    theme: props.theme || 'dark',
    colorsDefault: props.colorsDefault || [
        '#000000', '#FFFFFF', '#FF1900', '#F47365', '#FFB243', '#FFE623',
        '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF', '#5D61FF', '#FF89CF',
        '#FC3CAD', '#BF3DCE', '#8E00A7', 'rgba(0,0,0,0)'
    ],
    colorsHistoryKey: props.colorsHistoryKey || 'vue-colorpicker-history',
    suckerHide: props.suckerHide !== false,
    showValue: props.showValue !== false,
    showPreview: props.showPreview !== false,
    showFormat: props.showFormat !== false,
    showRandom: props.showRandom !== false,
    showClear: props.showClear !== false,
}));

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onColorChange(color) {
    currentColor.value = color;
}

function onInput(e) {
    const value = e.target.value;
    currentColor.value = value;
}

const colorRef = ref(null);
</script>

<template>
    <!-- 🎨 Fallback vers input color HTML natif si vue-color-kit n'est pas disponible -->
    <input
        v-if="!isColorKitReady"
        ref="colorRef"
        type="color"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="['input', atomClasses]"
        :value="currentColor"
        @input="onInput"
        @change="onColorChange"
    />
    
    <!-- 🎨 Composant ColorPicker avec styles DaisyUI -->
    <div 
        v-else
        ref="colorRef"
        :class="['color-picker-container', atomClasses]"
        v-bind="inputAttrs"
        v-on="listeners"
    >
        <ColorPicker
            v-if="ColorPicker"
            v-bind="colorPickerConfig"
            @changeColor="onColorChange"
            @input="onInput"
        >
            <slot />
        </ColorPicker>
    </div>
</template>

<style scoped lang="scss">
// Styles spécifiques pour ColorCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

input[type="color"].input {
    // Styles pour le fallback input color HTML natif
    outline: none;
    transition: all 0.2s ease-in-out;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
    // États de focus
    &:focus {
        outline: none;
        border-color: color-mix(in srgb, var(--color) 80%, transparent);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
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
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed {
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
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
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.border-b-2.bg-transparent.rounded-none {
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
    }
}

.color-picker-container {
    // Styles de base pour tous les color pickers
    display: inline-block;
    transition: all 0.2s ease-in-out;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    --color-picker-primary: var(--color); // Utilise --color pour le color picker
    
    // États disabled
    &:has([disabled]) {
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
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed {
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
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
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.border-b-2.bg-transparent.rounded-none {
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
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
.color-picker-container-xs {
    font-size: 0.75rem;
    
    :deep(.color-picker) {
        transform: scale(0.8);
        transform-origin: top left;
    }
}

.color-picker-container-sm {
    font-size: 0.875rem;
    
    :deep(.color-picker) {
        transform: scale(0.9);
        transform-origin: top left;
    }
}

.color-picker-container-md {
    font-size: 1rem;
    
    :deep(.color-picker) {
        transform: scale(1);
        transform-origin: top left;
    }
}

.color-picker-container-lg {
    font-size: 1.125rem;
    
    :deep(.color-picker) {
        transform: scale(1.1);
        transform-origin: top left;
    }
}

.color-picker-container-xl {
    font-size: 1.25rem;
    
    :deep(.color-picker) {
        transform: scale(1.2);
        transform-origin: top left;
    }
}

// Application des classes color-* pour définir --color
.color-primary { --color: var(--color-primary-500); --color-picker-primary: var(--color-primary-500); }
.color-secondary { --color: var(--color-secondary-500); --color-picker-primary: var(--color-secondary-500); }
.color-accent { --color: var(--color-accent-500); --color-picker-primary: var(--color-accent-500); }
.color-info { --color: var(--color-info-500); --color-picker-primary: var(--color-info-500); }
.color-success { --color: var(--color-success-500); --color-picker-primary: var(--color-success-500); }
.color-warning { --color: var(--color-warning-500); --color-picker-primary: var(--color-warning-500); }
.color-error { --color: var(--color-error-500); --color-picker-primary: var(--color-error-500); }
.color-neutral { --color: var(--color-neutral-500); --color-picker-primary: var(--color-neutral-500); }

// Styles pour le composant ColorPicker intégré
:deep(.color-picker) {
    // Styles pour le conteneur principal du color picker
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    
    // Styles pour les boutons
    button {
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.05);
        }
    }
    
    // Styles pour les inputs
    input {
        border-radius: 0.375rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        color: inherit;
        
        &:focus {
            outline: none;
            border-color: var(--color-picker-primary);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--color-picker-primary) 20%, transparent);
        }
    }
    
    // Styles pour la palette de couleurs
    .color-palette {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(2rem, 1fr));
        gap: 0.25rem;
        padding: 0.5rem;
        
        .color-item {
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: 2px solid transparent;
            
            &:hover {
                transform: scale(1.1);
                border-color: var(--color-picker-primary);
            }
            
            &.active {
                border-color: var(--color-picker-primary);
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--color-picker-primary) 30%, transparent);
            }
        }
    }
    
    // Styles pour le sélecteur de couleur
    .color-selector {
        border-radius: 0.5rem;
        overflow: hidden;
        
        .color-canvas {
            border-radius: 0.5rem;
        }
    }
    
    // Styles pour les sliders
    .color-slider {
        border-radius: 0.375rem;
        overflow: hidden;
        
        input[type="range"] {
            border-radius: 0.375rem;
            background: linear-gradient(to right, transparent, var(--color-picker-primary));
            
            &::-webkit-slider-thumb {
                border-radius: 50%;
                background: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }
            
            &::-moz-range-thumb {
                border-radius: 50%;
                background: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }
        }
    }
}

// Styles pour les thèmes
.color-picker-container[data-theme="light"] {
    :deep(.color-picker) {
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
}

.color-picker-container[data-theme="dark"] {
    :deep(.color-picker) {
        background: #1f2937;
        color: white;
        border: 1px solid #4b5563;
    }
}
</style> 