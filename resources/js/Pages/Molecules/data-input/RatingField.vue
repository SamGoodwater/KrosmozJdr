<script setup>
/**
 * RatingField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour étoiles de notation complet, orchestrant RatingCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux ratings : max, half-rating, affichage de la note, labels
 *
 * @see https://daisyui.com/components/rating/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <RatingField label="Note du film" v-model="movieRating" :max="5" />
 * 
 * // Label simple avec position par défaut différente
 * <RatingField label="Qualité" v-model="quality" defaultLabelPosition="start" :max="5" />
 * 
 * // Label avec positions spécifiques
 * <RatingField :label="{ start: 'Satisfaction', end: '/5' }" v-model="satisfaction" :max="5" />
 * 
 * // Label complexe avec slots
 * <RatingField :label="{ start: 'Évaluation' }" v-model="rating" :max="5">
 *   <template #labelStart>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-star"></i>
 *       Note globale
 *     </span>
 *   </template>
 * </RatingField>
 * 
 * // Avec demi-étoiles
 * <RatingField label="Précision" v-model="precision" :max="5" :half="true" />
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <RatingField label="Appréciation" v-model="appreciation" :max="5" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <RatingField label="Score" v-model="score" :max="10">
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
 * </RatingField>
 *
 * // Validation locale uniquement
 * <RatingField 
 *   label="Note" 
 *   v-model="rating"
 *   :max="5"
 *   :validation="{ state: 'error', message: 'Veuillez donner une note' }"
 * />
 *
 * // Validation avec notification
 * <RatingField 
 *   label="Évaluation" 
 *   v-model="evaluation"
 *   :max="5"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Merci pour votre évaluation !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <RatingField 
 *   label="Score" 
 *   v-model="score"
 *   :max="10"
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
 * @props {Number} max - Nombre maximum d'étoiles
 * @props {Boolean} half - Support des demi-étoiles
 * @props {String} placeholder - Placeholder du rating
 * @props {Boolean} showValue - Afficher la note actuelle
 * @props {Boolean} showPercentage - Afficher le pourcentage
 * @props {String} valueFormat - Format d'affichage de la valeur
 * @props {Array} labels - Labels pour chaque étoile
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot valueDisplay - Slot pour affichage personnalisé de la valeur
 * @slot default - rating natif (optionnel)
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import RatingCore from '@/Pages/Atoms/data-input/RatingCore.vue';
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
const props = defineProps(getInputPropsDefinition('rating', 'field'));

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
  type: 'rating', // Type spécifique pour les ratings
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

const ratingFieldId = computed(
    () => props.id || `ratingfield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('rating', {
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

// --- Fonctionnalités spécifiques aux ratings ---

// Validation de la valeur
const validatedValue = computed(() => {
    const value = currentValue.value;
    const max = props.max;
    
    if (value < 0) return 0;
    if (value > max) return max;
    return value;
});

// Pourcentage pour l'affichage visuel
const percentage = computed(() => {
    const max = props.max;
    const value = validatedValue.value;
    
    if (max === 0) return 0;
    return (value / max) * 100;
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
    
    if (percent < 20) return 'error';
    if (percent < 40) return 'warning';
    if (percent < 60) return 'info';
    if (percent < 80) return 'success';
    return 'success';
});

// Classe CSS pour la couleur de la valeur
const valueColorClass = computed(() => {
    return `text-${valueColor.value}`;
});

// Icône selon la valeur
const valueIcon = computed(() => {
    const percent = percentage.value;
    
    if (percent < 20) return 'fa-solid fa-star';
    if (percent < 40) return 'fa-solid fa-star';
    if (percent < 60) return 'fa-solid fa-star';
    if (percent < 80) return 'fa-solid fa-star';
    return 'fa-solid fa-star';
});

// Badge de statut
const statusBadge = computed(() => {
    const percent = percentage.value;
    
    if (percent < 20) return { text: 'Mauvais', color: 'error' };
    if (percent < 40) return { text: 'Moyen', color: 'warning' };
    if (percent < 60) return { text: 'Bon', color: 'info' };
    if (percent < 80) return { text: 'Très bon', color: 'success' };
    return { text: 'Excellent', color: 'success' };
});

// Label de l'étoile actuelle
const currentStarLabel = computed(() => {
    const value = Math.ceil(validatedValue.value);
    if (value > 0 && value <= props.labels.length) {
        return props.labels[value - 1];
    }
    return null;
});

// Texte descriptif de la note
const ratingDescription = computed(() => {
    const value = validatedValue.value;
    const max = props.max;
    
    if (value === 0) return 'Aucune note';
    if (value === 1) return '1 étoile';
    if (value === max) return `${max} étoiles`;
    
    if (props.half && value % 1 !== 0) {
        const whole = Math.floor(value);
        return `${whole} étoile${whole > 1 ? 's' : ''} et demi`;
    }
    
    return `${value} étoile${value > 1 ? 's' : ''}`;
});
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="ratingFieldId"
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
                :for="ratingFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le rating et les éléments over -->
            <div class="relative flex-1">
                <!-- Rating principal -->
                <RatingCore 
                    v-bind="inputProps"
                    v-model="currentValue"
                    :aria-invalid="processedValidation?.state === 'error'"
                >
                    <template v-if="slots.default" #default>
                        <slot />
                    </template>
                </RatingCore>

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
                :for="ratingFieldId"
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
            :for="ratingFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage de la valeur (optionnel) -->
        <div v-if="showValue && validatedValue !== null" class="mt-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-base-content/70">Note :</span>
                <slot name="valueDisplay">
                    <span :class="valueColorClass" class="flex items-center gap-1 font-bold">
                        <i :class="valueIcon"></i>
                        {{ formattedValue }}/{{ max }}
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
        
        <!-- Description de la note -->
        <div v-if="showValue && validatedValue !== null" class="mt-1 text-sm text-base-content/70">
            <span class="font-medium">Description :</span> 
            <span :class="valueColorClass" class="flex items-center gap-1">
                <i class="fa-solid fa-star"></i>
                {{ ratingDescription }}
                <span v-if="currentStarLabel" class="ml-1 text-xs">
                    ({{ currentStarLabel }})
                </span>
            </span>
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
            <span>0</span>
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
// Styles spécifiques pour RatingField
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
    // Boutons d'action dans les ratings
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
.fa-star {
    color: var(--color-primary, #3b82f6);
    
    &.text-error {
        color: var(--color-error, #ef4444);
    }
    
    &.text-warning {
        color: var(--color-warning, #f59e0b);
    }
    
    &.text-info {
        color: var(--color-info, #06b6d4);
    }
    
    &.text-success {
        color: var(--color-success, #10b981);
    }
}
</style> 