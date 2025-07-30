<script setup>
/**
 * RangeField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour slider range complet, orchestrant RangeCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux ranges : min/max, step, affichage de la valeur, pourcentage
 *
 * @see https://daisyui.com/components/range/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <RangeField label="Volume" v-model="volume" min="0" max="100" />
 * 
 * // Label simple avec position par défaut différente
 * <RangeField label="Température" v-model="temperature" defaultLabelPosition="start" min="0" max="50" />
 * 
 * // Label avec positions spécifiques
 * <RangeField :label="{ start: 'Vitesse', end: 'km/h' }" v-model="speed" min="0" max="200" />
 * 
 * // Label complexe avec slots
 * <RangeField :label="{ start: 'Niveau' }" v-model="level" min="1" max="10">
 *   <template #labelStart>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-sliders"></i>
 *       Niveau de difficulté
 *     </span>
 *   </template>
 * </RangeField>
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <RangeField label="Préférences" v-model="preferences" min="0" max="100" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <RangeField label="Seuil" v-model="threshold" min="0" max="100">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-minus"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="resetToDefault">
 *       <i class="fa-solid fa-undo"></i>
 *     </Btn>
 *   </template>
 * </RangeField>
 *
 * // Validation locale uniquement
 * <RangeField 
 *   label="Score" 
 *   v-model="score"
 *   min="0"
 *   max="100"
 *   :validation="{ state: 'error', message: 'Le score doit être entre 0 et 100' }"
 * />
 *
 * // Validation avec notification
 * <RangeField 
 *   label="Qualité" 
 *   v-model="quality"
 *   min="1"
 *   max="5"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Qualité excellente !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <RangeField 
 *   label="Intensité" 
 *   v-model="intensity"
 *   min="0"
 *   max="10"
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
 * @props {Number} min - Valeur minimale
 * @props {Number} max - Valeur maximale
 * @props {Number} step - Pas d'incrémentation
 * @props {String} placeholder - Placeholder du range
 * @props {Boolean} showValue - Afficher la valeur actuelle
 * @props {Boolean} showPercentage - Afficher le pourcentage
 * @props {String} valueFormat - Format d'affichage de la valeur
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot valueDisplay - Slot pour affichage personnalisé de la valeur
 * @slot default - range natif (optionnel)
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import RangeCore from '@/Pages/Atoms/data-input/RangeCore.vue';
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
const props = defineProps(getInputPropsDefinition('range', 'field'));


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
  type: 'range', // Type spécifique pour les ranges
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

const rangeFieldId = computed(
    () => props.id || `rangefield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('range', {
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

// --- Fonctionnalités spécifiques aux ranges ---

// Validation de la valeur
const validatedValue = computed(() => {
    const value = currentValue.value;
    const min = props.min;
    const max = props.max;
    
    if (value < min) return min;
    if (value > max) return max;
    return value;
});

// Pourcentage pour l'affichage visuel
const percentage = computed(() => {
    const min = props.min;
    const max = props.max;
    const value = validatedValue.value;
    
    if (max === min) return 0;
    return ((value - min) / (max - min)) * 100;
});

// Formatage de la valeur pour l'affichage
const formattedValue = computed(() => {
    const value = validatedValue.value;
    
    switch (props.valueFormat) {
        case 'percentage':
            return `${Math.round(percentage.value)}%`;
        case 'custom':
            return value; // Laisse le slot valueDisplay gérer
        default:
            return value.toString();
    }
});

// Couleur de la valeur selon sa position
const valueColor = computed(() => {
    const percent = percentage.value;
    
    if (percent < 25) return 'error';
    if (percent < 50) return 'warning';
    if (percent < 75) return 'info';
    return 'success';
});

// Classe CSS pour la couleur de la valeur
const valueColorClass = computed(() => {
    return `text-${valueColor.value}`;
});

// Icône selon la valeur
const valueIcon = computed(() => {
    const percent = percentage.value;
    
    if (percent < 25) return 'fa-solid fa-arrow-down';
    if (percent < 50) return 'fa-solid fa-minus';
    if (percent < 75) return 'fa-solid fa-arrow-up';
    return 'fa-solid fa-arrow-up';
});

// Badge de statut
const statusBadge = computed(() => {
    const percent = percentage.value;
    
    if (percent < 25) return { text: 'Faible', color: 'error' };
    if (percent < 50) return { text: 'Moyen', color: 'warning' };
    if (percent < 75) return { text: 'Bon', color: 'info' };
    return { text: 'Excellent', color: 'success' };
});
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="rangeFieldId"
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
                :for="rangeFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le range et les éléments over -->
            <div class="relative flex-1">
                <!-- Range principal -->
                <RangeCore 
                    v-bind="inputProps"
                    v-model="currentValue"
                    :aria-invalid="processedValidation?.state === 'error'"
                >
                    <template v-if="slots.default" #default>
                        <slot />
                    </template>
                </RangeCore>

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
                :for="rangeFieldId"
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
            :for="rangeFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage de la valeur (optionnel) -->
        <div v-if="showValue && validatedValue !== null" class="mt-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-base-content/70">Valeur :</span>
                <slot name="valueDisplay">
                    <span :class="valueColorClass" class="flex items-center gap-1 font-bold">
                        <i :class="valueIcon"></i>
                        {{ formattedValue }}
                    </span>
                </slot>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Pourcentage -->
                <span v-if="showPercentage" class="text-xs text-base-content/50">
                    {{ Math.round(percentage) }}%
                </span>
                
                <!-- Badge de statut -->
                <span :class="`badge badge-${statusBadge.color} badge-xs`">
                    {{ statusBadge.text }}
                </span>
            </div>
        </div>
        
        <!-- Barre de progression visuelle -->
        <div v-if="showValue" class="mt-1 w-full bg-gray-200 rounded-full h-2">
            <div 
                :class="`bg-${valueColor} h-2 rounded-full transition-all duration-300 ease-in-out`"
                :style="{ width: `${percentage}%` }"
            ></div>
        </div>
        
        <!-- Plage min/max -->
        <div v-if="showValue" class="mt-1 flex justify-between text-xs text-base-content/50">
            <span>{{ min }}</span>
            <span>{{ max }}</span>
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
// Styles spécifiques pour RangeField
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
    // Boutons d'action dans les ranges
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

// Styles pour l'affichage de la valeur
.text-sm {
    // Affichage de la valeur actuelle
    transition: all 0.2s ease-in-out;
    
    .font-medium {
        font-weight: 500;
    }
    
    .text-error {
        color: var(--color-error, #ef4444);
    }
    
    .text-warning {
        color: var(--color-warning, #f59e0b);
    }
    
    .text-info {
        color: var(--color-info, #06b6d4);
    }
    
    .text-success {
        color: var(--color-success, #10b981);
    }
    
    .font-bold {
        font-weight: 700;
    }
}

// Styles pour les badges de statut
.badge {
    // Badge de statut
    transition: all 0.2s ease-in-out;
    
    &.badge-error {
        background-color: var(--color-error, #ef4444);
        color: white;
    }
    
    &.badge-warning {
        background-color: var(--color-warning, #f59e0b);
        color: white;
    }
    
    &.badge-info {
        background-color: var(--color-info, #06b6d4);
        color: white;
    }
    
    &.badge-success {
        background-color: var(--color-success, #10b981);
        color: white;
    }
}

// Styles pour la barre de progression
.bg-gray-200 {
    // Barre de progression de base
    transition: all 0.2s ease-in-out;
    
    .bg-error {
        background-color: var(--color-error, #ef4444);
    }
    
    .bg-warning {
        background-color: var(--color-warning, #f59e0b);
    }
    
    .bg-info {
        background-color: var(--color-info, #06b6d4);
    }
    
    .bg-success {
        background-color: var(--color-success, #10b981);
    }
}

// Styles pour les icônes de valeur
.fa-arrow-down {
    color: var(--color-error, #ef4444);
}

.fa-minus {
    color: var(--color-warning, #f59e0b);
}

.fa-arrow-up {
    color: var(--color-success, #10b981);
}
</style> 