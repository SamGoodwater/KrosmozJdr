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
            :class="atomClasses"
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
      :class="atomClasses"
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
        :class="atomClasses"
        @input="onInput"
    @keydown="onKeydown"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques pour FileCore
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.file-input {
    // Styles de base pour tous les inputs file
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
    
    // Personnalisation du bouton de sélection de fichier
    &::file-selector-button {
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        margin-right: 0.5rem;
        
        &:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
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
        
        &::file-selector-button {
            background: rgba(59, 130, 246, 0.8);
            color: white;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        background: rgba(255, 255, 255, 0.05);
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        &::file-selector-button {
            background: rgba(139, 92, 246, 0.8);
            color: white;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
    }
    
    // Variant Outline - Bordure avec effet
    &.border-2.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        &::file-selector-button {
            background: rgba(16, 185, 129, 0.8);
            color: white;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
    }
    
    // Variant Ghost - Fond invisible
    &.border.border-transparent.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        &::file-selector-button {
            background: rgba(107, 114, 128, 0.8);
            color: white;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }
    }
    
    // Variant Soft - Style doux
    &.border-b-2.border-gray-300.bg-transparent.rounded-none {
        background: rgba(255, 255, 255, 0.05);
        border-bottom-width: 2px;
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        &::file-selector-button {
            background: rgba(245, 158, 11, 0.8);
            color: white;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
    }
    
    // Styles pour les couleurs DaisyUI
    &.file-input-primary::file-selector-button {
        background: var(--color-primary, #3b82f6);
        color: white;
    }
    
    &.file-input-secondary::file-selector-button {
        background: var(--color-secondary, #8b5cf6);
        color: white;
    }
    
    &.file-input-accent::file-selector-button {
        background: var(--color-accent, #f59e0b);
        color: white;
    }
    
    &.file-input-info::file-selector-button {
        background: var(--color-info, #06b6d4);
        color: white;
    }
    
    &.file-input-success::file-selector-button {
        background: var(--color-success, #10b981);
        color: white;
    }
    
    &.file-input-warning::file-selector-button {
        background: var(--color-warning, #f59e0b);
        color: white;
    }
    
    &.file-input-error::file-selector-button {
        background: var(--color-error, #ef4444);
        color: white;
    }
    
    &.file-input-neutral::file-selector-button {
        background: var(--color-neutral, #6b7280);
        color: white;
    }
}

// Styles pour les tailles DaisyUI
.file-input-xs::file-selector-button {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.file-input-sm::file-selector-button {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.file-input-md::file-selector-button {
    padding: 0.5rem 1rem;
    font-size: 1rem;
}

.file-input-lg::file-selector-button {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}

.file-input-xl::file-selector-button {
    padding: 1rem 2rem;
    font-size: 1.25rem;
}
</style>
