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
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.color-picker-container {
    // Styles de base pour tous les color pickers
    display: inline-block;
    transition: all 0.2s ease-in-out;
    
    // États disabled
    &:has([disabled]) {
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

// Styles pour les couleurs DaisyUI
.color-picker-container-primary {
    --color-picker-primary: var(--color-primary, #3b82f6);
}

.color-picker-container-secondary {
    --color-picker-primary: var(--color-secondary, #8b5cf6);
}

.color-picker-container-accent {
    --color-picker-primary: var(--color-accent, #f59e0b);
}

.color-picker-container-info {
    --color-picker-primary: var(--color-info, #06b6d4);
}

.color-picker-container-success {
    --color-picker-primary: var(--color-success, #10b981);
}

.color-picker-container-warning {
    --color-picker-primary: var(--color-warning, #f59e0b);
}

.color-picker-container-error {
    --color-picker-primary: var(--color-error, #ef4444);
}

.color-picker-container-neutral {
    --color-picker-primary: var(--color-neutral, #6b7280);
}

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
            border-color: var(--color-picker-primary, var(--color-primary, #3b82f6));
            box-shadow: 0 0 0 2px rgba(var(--color-picker-primary, var(--color-primary, #3b82f6)), 0.2);
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
                border-color: var(--color-picker-primary, var(--color-primary, #3b82f6));
            }
            
            &.active {
                border-color: var(--color-picker-primary, var(--color-primary, #3b82f6));
                box-shadow: 0 0 0 2px rgba(var(--color-picker-primary, var(--color-primary, #3b82f6)), 0.3);
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
            background: linear-gradient(to right, transparent, var(--color-picker-primary, var(--color-primary, #3b82f6)));
            
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