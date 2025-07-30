<script setup>
/**
 * FileField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour champ de fichier complet, orchestrant FileCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux fichiers : preview, drag & drop, progress
 *
 * @see https://daisyui.com/components/file-input/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <FileField label="Document" v-model="document" accept=".pdf,.doc,.docx" />
 * 
 * // Label simple avec position par défaut différente
 * <FileField label="Images" v-model="images" defaultLabelPosition="top" multiple />
 * 
 * // Label avec positions spécifiques
 * <FileField :label="{ top: 'Photos', inStart: '📷' }" v-model="photos" accept="image/*" multiple />
 * 
 * // Label complexe avec slots
 * <FileField :label="{ top: 'Fichiers' }" v-model="files">
 *   <template #labelTop>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-upload"></i>
 *       Glissez vos fichiers ici
 *     </span>
 *   </template>
 * </FileField>
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <FileField label="Fichiers" v-model="files" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <FileField label="Images" v-model="images" accept="image/*">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-camera"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="compressImages">
 *       <i class="fa-solid fa-compress"></i>
 *     </Btn>
 *   </template>
 * </FileField>
 *
 * // Validation locale uniquement
 * <FileField 
 *   label="Document" 
 *   v-model="document"
 *   :validation="{ state: 'error', message: 'Fichier trop volumineux' }"
 * />
 *
 * // Validation avec notification
 * <FileField 
 *   label="Images" 
 *   v-model="images"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Images uploadées !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <FileField 
 *   label="Fichiers" 
 *   v-model="files"
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }"
 * />
 *
 * @props {String|Object} label - Label simple (string) ou objet avec positions
 * @props {String} defaultLabelPosition - Position par défaut pour les strings ('floating', 'top', 'bottom', 'start', 'end', 'inStart', 'inEnd')
 * @props {Object|String|Boolean} validation - Configuration de validation (nouvelle API)
 * @props {String} helper, errorMessage
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {Boolean} useFieldComposable, showPasswordToggle
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String} accept - Types MIME acceptés (ex: "image/*", ".pdf,.doc")
 * @props {Boolean} multiple - Sélection multiple de fichiers
 * @props {String} capture - Capture média ("user", "environment")
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot preview - Slot pour preview des fichiers sélectionnés
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import FileCore from '@/Pages/Atoms/data-input/FileCore.vue';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import Helper from '@/Pages/Atoms/data-input/Helper.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import useInputActions from '@/Composables/form/useInputActions';
import { 
    getCustomUtilityClasses,
    mergeClasses 
} from '@/Utils/atomic-design/uiHelper';
import { 
    getInputPropsDefinition, 
} from '@/Utils/atomic-design/inputHelper';
import { 
    processLabelConfig 
} from '@/Utils/atomic-design/labelManager';
import { 
    processValidation
} from '@/Utils/atomic-design/validationManager';
import { 
    getInputStyleProperties
} from '@/Composables/form/useInputStyle';

// ------------------------------------------
// 🔧 Définition des props
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('file', 'field'));

const $attrs = useAttrs();
const slots = useSlots();
const notificationStore = inject('notificationStore', null);
const labelConfig = computed(() => processLabelConfig(props.label, props.defaultLabelPosition));

// ------------------------------------------
// ⚙️ Utilisation du composable universel pour les actions contextuelles
// ------------------------------------------
const {
  currentValue,
  actionsToDisplay,
  inputProps,
  focus,
  isModified,
  isReadonly,
  reset,
  back,
  clear,
  copy,
  toggleEdit,
  inputRef,
} = useInputActions({
  modelValue: props.modelValue,
  type: 'file', // Type spécifique pour les fichiers
  actions: props.actions,
  readonly: props.readonly,
  debounce: props.debounceTime,
  autofocus: props.autofocus,
});

// ------------------------------------------
// 🔄 v-model : émettre update:modelValue quand la valeur change
// ------------------------------------------
const emit = defineEmits(['update:modelValue']);
watch(currentValue, (val) => {
  emit('update:modelValue', val);
});

// ------------------------------------------
// ✅ Validation et autres logiques existantes
// ------------------------------------------
const notificationStoreInjected = notificationStore;
const processedValidation = computed(() => {
    if (!props.validation) {
        return null;
    }
    return processValidation(props.validation, notificationStoreInjected);
});

const hasValidationState = computed(() => {
    return processedValidation.value !== null || slots.validator;
});

const fileFieldId = computed(
    () => props.id || `filefield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('file', {
        variant: props.variant,
        color: props.color,
        size: props.size,
        animation: props.animation,
              ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
      ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
    })
);

const containerClasses = computed(() => 
    mergeClasses(
        'form-control w-full',
        getCustomUtilityClasses(props)
    )
);

function getValidatorState() {
    if (!processedValidation.value) return '';
    return processedValidation.value.state;
}

function getValidatorMessage() {
    if (!processedValidation.value) return '';
    return processedValidation.value.message;
}

// --- Fonctionnalités spécifiques aux fichiers ---

// Preview des fichiers sélectionnés
const selectedFiles = computed(() => {
    if (!currentValue.value) return [];
    
    if (props.multiple && currentValue.value instanceof FileList) {
        return Array.from(currentValue.value);
    } else if (!props.multiple && currentValue.value instanceof File) {
        return [currentValue.value];
    }
    
    return [];
});

// Formatage de la taille des fichiers
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Vérification du type de fichier
function isValidFileType(file) {
    if (!props.accept) return true;
    
    const acceptTypes = props.accept.split(',').map(type => type.trim());
    
    return acceptTypes.some(type => {
        if (type.startsWith('.')) {
            // Extension de fichier
            return file.name.toLowerCase().endsWith(type.toLowerCase());
        } else if (type.includes('/*')) {
            // Type MIME générique
            const baseType = type.split('/')[0];
            return file.type.startsWith(baseType + '/');
        } else {
            // Type MIME spécifique
            return file.type === type;
        }
    });
}
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="fileFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
        >
            <slot name="labelTop" />
        </InputLabel>
        
        <div class="relative flex items-center w-full">
            <!-- Label start -->
            <InputLabel
                v-if="labelConfig.start || slots.labelStart"
                :value="labelConfig.start"
                :for="fileFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le file input et les éléments over -->
            <div class="relative flex-1">
                <!-- File input principal -->
                <FileCore 
                    v-bind="inputProps"
                    v-model="currentValue"
                    :aria-invalid="processedValidation?.state === 'error'"
                >
                    <template v-if="slots.labelInStart" #labelInStart>
                        <slot name="labelInStart" />
                    </template>
                    <template v-if="slots.labelInEnd" #labelInEnd>
                        <slot name="labelInEnd" />
                    </template>
                    <template v-if="labelConfig.floating && (labelConfig.floating || slots.labelFloating)" #floatingLabel>
                        <slot name="labelFloating">{{ labelConfig.floating }}</slot>
                    </template>
                </FileCore>

                <!-- Slot overStart (positionné en absolute à gauche) -->
                <div v-if="slots.overStart" class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10 flex gap-1">
                    <slot name="overStart" />
                </div>
                <!-- Slot overEnd (positionné en absolute à droite) + actions contextuelles -->
                <div v-if="slots.overEnd || actionsToDisplay.length" class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 flex items-center gap-1">
                    <slot name="overEnd" />
                    <Btn
                        v-for="action in actionsToDisplay"
                        :key="action.key"
                        :variant="action.variant"
                        :color="action.color"
                        :size="action.size"
                        circle
                        :aria-label="action.ariaLabel"
                        :title="action.tooltip"
                        :disabled="action.disabled"
                        @click.stop="action.onClick"
                    >
                        <i :class="action.icon" class="text-sm"></i>
                        </Btn>
                    </div>
            </div>

            <!-- Label end -->
            <InputLabel
                v-if="labelConfig.end || slots.labelEnd"
                :value="labelConfig.end"
                :for="fileFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="ml-2"
            >
                <slot name="labelEnd" />
            </InputLabel>
        </div>
        
        <!-- Label bottom -->
        <InputLabel
            v-if="labelConfig.bottom || slots.labelBottom"
            :value="labelConfig.bottom"
            :for="fileFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Preview des fichiers sélectionnés -->
        <div v-if="selectedFiles.length > 0" class="mt-2">
            <slot name="preview">
                <div class="space-y-2">
                    <div 
                        v-for="(file, index) in selectedFiles" 
                        :key="index"
                        class="flex items-center justify-between p-2 bg-base-200 rounded-lg"
                    >
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file text-primary"></i>
                            <span class="text-sm font-medium">{{ file.name }}</span>
                            <span class="text-xs text-base-content/60">({{ formatFileSize(file.size) }})</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span 
                                v-if="!isValidFileType(file)" 
                                class="badge badge-warning badge-xs"
                                title="Type de fichier non supporté"
                            >
                                <i class="fa-solid fa-exclamation-triangle"></i>
                            </span>
                            <Btn 
                                variant="ghost" 
                                size="xs" 
                                circle
                                @click="clear"
                                title="Supprimer ce fichier"
                            >
                                <i class="fa-solid fa-times"></i>
                            </Btn>
                        </div>
            </div>
            </div>
            </slot>
        </div>
        
        <!-- Validator -->
        <div v-if="hasValidationState" class="mt-1">
            <slot name="validator">
                <Validator
                    v-if="processedValidation"
                    :state="getValidatorState()"
                    :message="getValidatorMessage()"
                />
            </slot>
        </div>
        
        <!-- Helper -->
        <div v-if="helper || slots.helper" class="mt-1">
            <slot name="helper">
                <Helper 
                    :helper="helper" 
                    :color="styleProperties.helperColor" 
                    :size="styleProperties.helperSize" 
                />
            </slot>
        </div>
    </div>
</template>

<style scoped lang="scss">
// Styles spécifiques pour FileField
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

// Styles pour les labels
.label {
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    // Tailles
    &.label-xs { font-size: 0.75rem; }
    &.label-sm { font-size: 0.875rem; }
    &.label-md { font-size: 1rem; }
    &.label-lg { font-size: 1.125rem; }
    &.label-xl { font-size: 1.25rem; }
    
    // Couleurs
    &.label-primary { color: var(--color-primary, #3b82f6); }
    &.label-secondary { color: var(--color-secondary, #8b5cf6); }
    &.label-accent { color: var(--color-accent, #f59e0b); }
    &.label-info { color: var(--color-info, #06b6d4); }
    &.label-success { color: var(--color-success, #10b981); }
    &.label-warning { color: var(--color-warning, #f59e0b); }
    &.label-error { color: var(--color-error, #ef4444); }
    &.label-neutral { color: var(--color-neutral, #6b7280); }
    
    // Effet hover subtil
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les helpers
.helper {
    transition: all 0.2s ease-in-out;
    font-size: 0.875rem;
    opacity: 0.8;
    
    // Tailles
    &.helper-xs { font-size: 0.75rem; }
    &.helper-sm { font-size: 0.875rem; }
    &.helper-md { font-size: 1rem; }
    &.helper-lg { font-size: 1.125rem; }
    &.helper-xl { font-size: 1.25rem; }
    
    // Couleurs
    &.helper-primary { color: var(--color-primary, #3b82f6); }
    &.helper-secondary { color: var(--color-secondary, #8b5cf6); }
    &.helper-accent { color: var(--color-accent, #f59e0b); }
    &.helper-info { color: var(--color-info, #06b6d4); }
    &.helper-success { color: var(--color-success, #10b981); }
    &.helper-warning { color: var(--color-warning, #f59e0b); }
    &.helper-error { color: var(--color-error, #ef4444); }
    &.helper-neutral { color: var(--color-neutral, #6b7280); }
}

// Styles pour les actions contextuelles
.btn {
    // Boutons d'action dans les file inputs
    &.btn-link {
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.1);
        }
    }
}

// Styles pour les slots overStart/overEnd
.absolute {
    // Positionnement des éléments absolus
    z-index: 10;
    
    .btn {
        // Boutons dans les slots over
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.05);
        }
    }
}

// Styles pour les validations
.validator {
    // Messages de validation
    transition: all 0.2s ease-in-out;
    
    &.error {
        color: var(--color-error, #ef4444);
    }
    
    &.success {
        color: var(--color-success, #10b981);
    }
    
    &.warning {
        color: var(--color-warning, #f59e0b);
    }
    
    &.info {
        color: var(--color-info, #06b6d4);
    }
}

// Styles pour la preview des fichiers
.badge {
    // Badges d'état des fichiers
    transition: all 0.2s ease-in-out;
    
    &.badge-warning {
        background-color: var(--color-warning, #f59e0b);
        color: white;
    }
}

// Animation pour les fichiers sélectionnés
.space-y-2 > div {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
