<script setup>
/**
 * RadioField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour groupe de boutons radio complet, orchestrant RadioCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux radios : groupe, sélection exclusive, options
 *
 * @see https://daisyui.com/components/radio/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <RadioField label="Genre" v-model="gender" :options="genderOptions" />
 * 
 * // Label simple avec position par défaut différente
 * <RadioField label="Thème" v-model="theme" :options="themeOptions" defaultLabelPosition="start" />
 * 
 * // Label avec positions spécifiques
 * <RadioField :label="{ start: 'Préférence', end: 'de contact' }" v-model="contact" :options="contactOptions" />
 * 
 * // Label complexe avec slots
 * <RadioField :label="{ start: 'Options' }" v-model="selected" :options="options">
 *   <template #labelStart>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-circle-dot"></i>
 *       Choisir une option
 *     </span>
 *   </template>
 * </RadioField>
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <RadioField label="Préférences" v-model="preferences" :options="prefOptions" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <RadioField label="Permissions" v-model="permissions" :options="permOptions">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-lock"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="selectDefault">
 *       <i class="fa-solid fa-undo"></i>
 *     </Btn>
 *   </template>
 * </RadioField>
 *
 * // Validation locale uniquement
 * <RadioField 
 *   label="Genre" 
 *   v-model="gender"
 *   :validation="{ state: 'error', message: 'Veuillez sélectionner un genre' }"
 * />
 *
 * // Validation avec notification
 * <RadioField 
 *   label="Thème" 
 *   v-model="theme"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Thème appliqué !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <RadioField 
 *   label="Option" 
 *   v-model="option"
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
 * @props {Array} options - Options du radio (array de strings ou objets {value, label, disabled})
 * @props {String} name - Nom du groupe radio (généré automatiquement si non fourni)
 * @props {String} placeholder - Placeholder du radio
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot default - radio natif (optionnel)
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import RadioCore from '@/Pages/Atoms/data-input/RadioCore.vue';
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
const props = defineProps(getInputPropsDefinition('radio', 'field'));

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
  type: 'radio', // Type spécifique pour les radios
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

const radioFieldId = computed(
    () => props.id || `radiofield-${Math.random().toString(36).substr(2, 9)}`,
);

// Nom du groupe radio (généré automatiquement si non fourni)
const radioGroupName = computed(() => 
    props.name || `radio-group-${Math.random().toString(36).substr(2, 9)}`
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('radio', {
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

// --- Fonctionnalités spécifiques aux radios ---

// Traitement des options pour l'affichage
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

// Vérification si une option est sélectionnée
const hasSelection = computed(() => {
    return !!currentValue.value;
});

// Récupération du label de l'option sélectionnée
const selectedOptionLabel = computed(() => {
    if (!hasSelection.value) return props.placeholder || 'Non sélectionné';
    
    const option = processedOptions.value.find(opt => opt.value === currentValue.value);
    return option ? option.label : currentValue.value;
});

// Nombre total d'options
const totalOptions = computed(() => processedOptions.value.length);

// Nombre d'options désactivées
const disabledOptions = computed(() => 
    processedOptions.value.filter(opt => opt.disabled).length
);

// Nombre d'options disponibles
const availableOptions = computed(() => 
    totalOptions.value - disabledOptions.value
);
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="radioFieldId"
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
                :for="radioFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour les radios et les éléments over -->
            <div class="relative flex-1">
                <!-- Groupe de radios -->
                <div class="flex flex-wrap gap-2">
                    <RadioCore 
                        v-for="option in processedOptions"
                        :key="option.value"
                        v-bind="inputProps"
                        :value="option.value"
                        :name="radioGroupName"
                        :id="`${radioFieldId}-${option.value}`"
                        :aria-label="option.label"
                        :aria-invalid="processedValidation?.state === 'error'"
                    >
                        <template v-if="slots.default" #default>
                            <slot />
                        </template>
                    </RadioCore>
                </div>

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
                :for="radioFieldId"
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
            :for="radioFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage de la sélection (optionnel) -->
        <div v-if="hasSelection" class="mt-1 text-sm text-base-content/70">
            <span class="font-medium">Sélectionné :</span> {{ selectedOptionLabel }}
            <span class="ml-2 badge badge-primary badge-xs">
                {{ totalOptions }} option(s)
            </span>
            <span v-if="disabledOptions > 0" class="ml-2 badge badge-neutral badge-xs">
                {{ disabledOptions }} désactivée(s)
            </span>
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
// Styles spécifiques pour RadioField
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
    // Boutons d'action dans les radios
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

// Styles pour l'affichage de la sélection
.text-sm {
    // Affichage de la sélection actuelle
    transition: all 0.2s ease-in-out;
    
    .font-medium {
        font-weight: 500;
    }
    
    .badge {
        // Badge de compteur
        transition: all 0.2s ease-in-out;
        
        &.badge-primary {
            background-color: var(--color-primary, #3b82f6);
            color: white;
        }
        
        &.badge-neutral {
            background-color: var(--color-neutral, #6b7280);
            color: white;
        }
    }
}

// Styles pour le groupe de radios
.flex {
    // Groupe de boutons radio
    &.flex-wrap {
        // Wrapping des radios
        gap: 0.5rem;
        
        .radio-core {
            // Chaque radio dans le groupe
            transition: all 0.2s ease-in-out;
            
            &:hover {
                transform: scale(1.05);
            }
            
            &.opacity-50 {
                // Radio désactivé
                cursor: not-allowed;
                
                &:hover {
                    transform: none;
                }
            }
        }
    }
}
</style> 