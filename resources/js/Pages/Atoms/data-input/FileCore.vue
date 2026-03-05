<script setup>
defineOptions({ inheritAttrs: false });

/**
 * FileCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs de fichier, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, accept, multiple, capture, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : input file natif
 * - Gestion des attributs spécifiques aux fichiers : accept, multiple, capture
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Utilise le système de labels inline de DaisyUI pour éviter les divs englobantes
 *
 * @see https://daisyui.com/components/file-input/
 * @version DaisyUI v5.x
 *
 * @example
 * <FileCore v-model="files" accept="image/*" multiple />
 * <FileCore v-model="document" accept=".pdf,.doc,.docx" color="primary" size="lg" />
 * <FileCore v-model="photo" capture="environment" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <FileCore 
 *   v-model="files" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {FileList|Array} modelValue - v-model (FileList ou Array de File)
 * @props {String} accept - Types MIME acceptés (ex: "image/*", ".pdf,.doc")
 * @props {Boolean} multiple - Sélection multiple de fichiers
 * @props {String} capture - Capture média ("user", "environment")
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
 * @slot default - input file natif (optionnel)
 */

// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

// ------------------------------------------
// 🔧 Définition des props + emits
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('file', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'file', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('file', {
            variant: props.variant,
            color: props.color,
            size: props.size,
            animation: props.animation,
      ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
      ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
    }, false)
  )
)

// Classes de style basées sur Btn pour harmonisation
const variantBtnFileClasses = computed(() =>
  mergeClasses(
    [
        // Variants (même logique que Btn)
        props.variant === "outline" && "btn-outline-custom border-glass-lg hover:border-glass-xl",
        props.variant === "ghost" && "btn-ghost-custom",
        props.variant === "link" && "btn-link",
        props.variant === "soft" && "btn-soft",
        props.variant === "dash" && "btn-dash",
        props.variant === "glass" && "btn-glass-custom box-glass-sm hover:box-glass-md",
        // Couleurs (même logique que Btn)
        props.color === "primary" && "btn-custom-primary color-primary",
        props.color === "secondary" && "btn-custom-secondary color-secondary",
        props.color === "accent" && "btn-custom-accent color-accent",
        props.color === "info" && "btn-custom-info color-info",
        props.color === "success" && "btn-custom-success color-success",
        props.color === "warning" && "btn-custom-warning color-warning",
        props.color === "error" && "btn-custom-error color-error",
        props.color === "neutral" && "btn-custom-neutral color-neutral",
        // Tailles (même logique que Btn)
        props.size === "xs" && "btn-xs",
        props.size === "sm" && "btn-sm",
        props.size === "md" && "btn-md",
        props.size === "lg" && "btn-lg",
        props.size === "xl" && "btn-xl",
    ].filter(Boolean),
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

// ------------------------------------------
// 📁 Gestion du v-model pour file
// ------------------------------------------
const selectedFiles = computed({
    get() {
        return props.modelValue || [];
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

const fileRef = ref(null);

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onInput(e) {
    const files = e.target.files;
    if (props.multiple) {
        selectedFiles.value = Array.from(files);
    } else {
        selectedFiles.value = files.length > 0 ? files[0] : null;
        }
}

function onKeydown(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        fileRef.value?.click();
    }
}
</script>

<template>
  <!-- 🔤 Label inline (inStart / inEnd) -->
  <label v-if="shouldShowInlineLabels" :class="labelClasses">
    <!-- ⬅️ Label inStart -->
        <span v-if="labelInStart || $slots.labelInStart" class="label-text">
            <slot name="labelInStart">{{ labelInStart }}</slot>
        </span>

    <!-- 📁 Input file principal -->
        <input
            ref="fileRef"
            type="file"
            v-bind="inputAttrs"
            v-on="listeners"
            :class="atomClasses + ' ' + variantBtnFileClasses"
            @input="onInput"
      @keydown="onKeydown"
        />

    <!-- ➡️ Label inEnd -->
        <span v-if="labelInEnd || $slots.labelInEnd" class="label-text">
            <slot name="labelInEnd">{{ labelInEnd }}</slot>
        </span>
    </label>
    
  <!-- 💬 Floating label -->
  <label v-else-if="props.labelFloating" :class="labelClasses">
        <input
            ref="fileRef"
            type="file"
            v-bind="inputAttrs"
            v-on="listeners"
            :class="atomClasses + ' ' + variantBtnFileClasses"
            @input="onInput"
      @keydown="onKeydown"
        />
        <span class="label-text">
      <slot name="floatingLabel">{{ props.placeholder || 'Sélectionner un fichier' }}</slot>
        </span>
    </label>
    
  <!-- 📁 Input file simple sans label -->
    <input
        v-else
        ref="fileRef"
        type="file"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses + ' ' + variantBtnFileClasses"
        @input="onInput"
    @keydown="onKeydown"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour FileCore
// Utilisation des classes de Btn et des classes utilitaires glassmorphisme
// Utilisation de var(--color) pour les couleurs

input[type="file"] {
    // Styles de base pour tous les inputs file
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
    
    // Personnalisation du bouton de sélection de fichier (utilise les styles de Btn)
    &::file-selector-button {
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        margin-right: 0.5rem;
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-field, 0.1rem);
        border: 1px solid color-mix(in srgb, var(--color) 30%, transparent);
        background-color: var(--color);
        color: white;
        
        &:hover {
            transform: translateY(-1px);
            background-color: color-mix(in srgb, var(--color) 90%, transparent);
            box-shadow: 0 4px 6px -1px color-mix(in srgb, var(--color) 20%, transparent);
        }
        
        &:active {
            transform: translateY(0);
        }
    }
    
    // Variant Glass - Effet glassmorphisme (utilise les classes de Btn)
    &.btn-glass-custom {
        border-radius: var(--radius-field, 0.1rem);
        backdrop-filter: blur(24px);
        box-shadow:
            0 0 10px 0 color-mix(in srgb, var(--color) 20%, transparent) inset,
            0 10px 30px -18px rgba(0, 0, 0, 0.35);
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
            box-shadow:
                0 0 14px 0 color-mix(in srgb, var(--color) 28%, transparent) inset,
                0 14px 34px -18px rgba(0, 0, 0, 0.42);
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
        }
        
        &::file-selector-button {
            background-color: color-mix(in srgb, var(--color) 80%, transparent);
            border-color: color-mix(in srgb, var(--color) 40%, transparent);
            
            &:hover {
                background-color: color-mix(in srgb, var(--color) 90%, transparent);
            }
        }
    }
    
    // Variant Dash - Style pointillé
    &.btn-dash {
        border-radius: var(--radius-field, 0.1rem);
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &::file-selector-button {
            background-color: color-mix(in srgb, var(--color) 80%, transparent);
            border-style: dashed;
            border-color: color-mix(in srgb, var(--color) 40%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.btn-outline-custom {
        border-radius: var(--radius-field, 0.1rem);
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &::file-selector-button {
            background-color: var(--color);
            border-color: var(--color);
        }
    }
    
    // Variant Ghost - Transparent
    &.btn-ghost-custom {
        background-color: transparent;
        border-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            border-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
        
        &::file-selector-button {
            background-color: color-mix(in srgb, var(--color) 80%, transparent);
            border-color: color-mix(in srgb, var(--color) 30%, transparent);
        }
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.btn-soft {
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
        
        &::file-selector-button {
            background-color: var(--color);
            border-bottom-color: var(--color);
        }
    }
    
    // Variant Link - Style lien
    &.btn-link {
        background-color: transparent;
        border-color: transparent;
        text-decoration: underline;
        
        &:hover {
            text-decoration: none;
        }
        
        &::file-selector-button {
            background-color: transparent;
            border-color: transparent;
            color: var(--color);
            text-decoration: underline;
            
            &:hover {
                text-decoration: none;
            }
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
