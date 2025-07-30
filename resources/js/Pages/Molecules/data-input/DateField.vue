<script setup>
/**
 * DateField Molecule (DaisyUI + Cally, Atomic Design)
 *
 * @description
 * Molecule pour sélecteur de date complet, orchestrant DateCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 5 positions de labels : top, bottom, start, end, floating (pas de inStart/inEnd pour les dates)
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux dates : min/max, format, locale, affichage de la date
 *
 * @see https://daisyui.com/components/calendar/
 * @see https://cally.js.org/
 * @version DaisyUI v5.x + Cally
 *
 * @example
 * // Label simple (floating par défaut)
 * <DateField label="Date de naissance" v-model="birthDate" />
 * 
 * // Label simple avec position par défaut différente
 * <DateField label="Date" v-model="date" defaultLabelPosition="start" />
 * 
 * // Label avec positions spécifiques
 * <DateField :label="{ start: 'Date de début', end: 'Format: DD/MM/YYYY' }" v-model="startDate" />
 * 
 * // Label complexe avec slots
 * <DateField :label="{ start: 'Date' }" v-model="date">
 *   <template #labelStart>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-calendar"></i>
 *       Date de rendez-vous
 *     </span>
 *   </template>
 * </DateField>
 * 
 * // Avec min/max dates
 * <DateField label="Date de naissance" v-model="birthDate" :min="'1900-01-01'" :max="'2024-12-31'" />
 * 
 * // Avec format personnalisé
 * <DateField label="Date" v-model="date" format="DD/MM/YYYY" locale="fr" />
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <DateField label="Date" v-model="date" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <DateField label="Date" v-model="date">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-calendar-day"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="setToday">
 *       <i class="fa-solid fa-calendar-check"></i>
 *     </Btn>
 *   </template>
 * </DateField>
 *
 * // Validation locale uniquement
 * <DateField 
 *   label="Date" 
 *   v-model="date"
 *   :validation="{ state: 'error', message: 'Date invalide' }"
 * />
 *
 * // Validation avec notification
 * <DateField 
 *   label="Date de naissance" 
 *   v-model="birthDate"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Date valide !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <DateField 
 *   label="Date" 
 *   v-model="date"
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }"
 * />
 *
 * @props {String|Object} label - Label simple (string) ou objet avec positions
 * @props {String} defaultLabelPosition - Position par défaut pour les strings ('floating', 'top', 'bottom', 'start', 'end')
 * @props {Object|String|Boolean} validation - Configuration de validation (nouvelle API)
 * @props {String} helper, errorMessage
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {Boolean} useFieldComposable, showPasswordToggle
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {Date|String} min - Date minimum
 * @props {Date|String} max - Date maximum
 * @props {String} format - Format d'affichage (YYYY-MM-DD par défaut)
 * @props {String} locale - Locale pour l'affichage (fr par défaut)
 * @props {String} placeholder - Placeholder du calendrier
 * @props {Boolean} showValue - Afficher la date actuelle
 * @props {String} valueFormat - Format d'affichage de la valeur
 * @props {Boolean} showToday - Afficher le bouton "Aujourd'hui"
 * @props {Boolean} showClear - Afficher le bouton "Effacer"
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, today, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot valueDisplay - Slot pour affichage personnalisé de la valeur
 * @slot previous, next - Slots pour icônes de navigation
 * @slot default - Contenu personnalisé du calendrier
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import DateCore from '@/Pages/Atoms/data-input/DateCore.vue';
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
const props = defineProps(getInputPropsDefinition('date', 'field'));

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
  type: 'date', // Type spécifique pour les dates
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

const dateFieldId = computed(
    () => props.id || `datefield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('date', {
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

// --- Fonctionnalités spécifiques aux dates ---

// Validation de la valeur
const validatedValue = computed(() => {
    const value = currentValue.value;
    
    if (!value) return null;
    
    // Convertir en Date si c'est une string
    const dateValue = typeof value === 'string' ? new Date(value) : value;
    
    // Validation min/max
    if (props.min) {
        const minDate = typeof props.min === 'string' ? new Date(props.min) : props.min;
        if (dateValue < minDate) return minDate;
    }
    
    if (props.max) {
        const maxDate = typeof props.max === 'string' ? new Date(props.max) : props.max;
        if (dateValue > maxDate) return maxDate;
    }
    
    return dateValue;
});

// Formatage de la valeur pour l'affichage
const formattedValue = computed(() => {
    const value = validatedValue.value;
    if (!value) return '';
    
    const date = typeof value === 'string' ? new Date(value) : value;
    
    switch (props.valueFormat) {
        case 'iso':
            return date.toISOString().split('T')[0];
        case 'custom':
            return value; // Laisse le slot valueDisplay gérer
        default:
            // Format localisé
            return date.toLocaleDateString(props.locale, {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'long'
            });
    }
});

// Couleur de la valeur selon sa position
const valueColor = computed(() => {
    const value = validatedValue.value;
    if (!value) return 'neutral';
    
    const date = typeof value === 'string' ? new Date(value) : value;
    const today = new Date();
    const diffTime = date.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return 'error'; // Passé
    if (diffDays === 0) return 'success'; // Aujourd'hui
    if (diffDays <= 7) return 'warning'; // Cette semaine
    return 'info'; // Futur
});

// Classe CSS pour la couleur de la valeur
const valueColorClass = computed(() => {
    return `text-${valueColor.value}`;
});

// Icône selon la date
const valueIcon = computed(() => {
    const value = validatedValue.value;
    if (!value) return 'fa-solid fa-calendar';
    
    const date = typeof value === 'string' ? new Date(value) : value;
    const today = new Date();
    const diffTime = date.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return 'fa-solid fa-calendar-minus'; // Passé
    if (diffDays === 0) return 'fa-solid fa-calendar-check'; // Aujourd'hui
    if (diffDays <= 7) return 'fa-solid fa-calendar-week'; // Cette semaine
    return 'fa-solid fa-calendar-plus'; // Futur
});

// Badge de statut
const statusBadge = computed(() => {
    const value = validatedValue.value;
    if (!value) return { text: 'Aucune date', color: 'neutral' };
    
    const date = typeof value === 'string' ? new Date(value) : value;
    const today = new Date();
    const diffTime = date.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return { text: 'Passé', color: 'error' };
    if (diffDays === 0) return { text: 'Aujourd\'hui', color: 'success' };
    if (diffDays <= 7) return { text: 'Cette semaine', color: 'warning' };
    return { text: 'Futur', color: 'info' };
});

// Actions spécifiques aux dates
function setToday() {
    const today = new Date();
    currentValue.value = today;
}

function setYesterday() {
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    currentValue.value = yesterday;
}

function setTomorrow() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    currentValue.value = tomorrow;
}

// Actions contextuelles personnalisées pour les dates
const dateActions = computed(() => {
    const actions = [];
    
    if (props.showToday) {
        actions.push({
            key: 'today',
            icon: 'fa-solid fa-calendar-check',
            ariaLabel: 'Définir à aujourd\'hui',
            tooltip: 'Définir à aujourd\'hui',
            color: 'success',
            variant: 'ghost',
            size: 'xs',
            onClick: setToday
        });
    }
    
    if (props.showClear && currentValue.value) {
        actions.push({
            key: 'clear',
            icon: 'fa-solid fa-calendar-xmark',
            ariaLabel: 'Effacer la date',
            tooltip: 'Effacer la date',
            color: 'neutral',
            variant: 'ghost',
            size: 'xs',
            onClick: clear
        });
    }
    
    return actions;
});

// Actions à afficher (combinaison des actions automatiques et spécifiques)
const allActionsToDisplay = computed(() => {
    return [...actionsToDisplay.value, ...dateActions.value];
});
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="dateFieldId"
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
                :for="dateFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le calendrier et les éléments over -->
            <div class="relative flex-1">
                <!-- Calendrier principal -->
                <DateCore 
                    v-bind="inputProps"
                    v-model="currentValue"
                    :aria-invalid="processedValidation?.state === 'error'"
                >
                    <template v-if="slots.previous" #previous>
                        <slot name="previous" />
                    </template>
                    <template v-if="slots.next" #next>
                        <slot name="next" />
                    </template>
                    <template v-if="slots.default" #default>
                        <slot />
                    </template>
                </DateCore>

                <!-- Slot overStart (positionné en absolute à gauche) -->
                <div v-if="slots.overStart" class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10 flex gap-1">
                    <slot name="overStart" />
                </div>
                <!-- Slot overEnd (positionné en absolute à droite) + actions contextuelles -->
                <div v-if="slots.overEnd || allActionsToDisplay.length" class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 flex items-center gap-1">
                    <slot name="overEnd" />
                    <Btn
                        v-for="action in allActionsToDisplay"
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
                :for="dateFieldId"
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
            :for="dateFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage de la valeur (optionnel) -->
        <div v-if="props.showValue && validatedValue !== null" class="mt-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-base-content/70">Date :</span>
                <slot name="valueDisplay">
                    <span :class="valueColorClass" class="flex items-center gap-1 font-bold">
                        <i :class="valueIcon"></i>
                        {{ formattedValue }}
                    </span>
                </slot>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Badge de statut -->
                <span :class="`badge badge-${statusBadge.color} badge-xs`">
                    {{ statusBadge.text }}
                </span>
            </div>
        </div>
        
        <!-- Informations sur la date -->
        <div v-if="props.showValue && validatedValue !== null" class="mt-1 text-sm text-base-content/70">
            <span class="font-medium">Informations :</span> 
            <span :class="valueColorClass" class="flex items-center gap-1">
                <i class="fa-solid fa-calendar"></i>
                {{ statusBadge.text }}
                <span v-if="validatedValue" class="ml-1 text-xs">
                    ({{ validatedValue.toLocaleDateString(props.locale, { year: 'numeric', month: 'short', day: 'numeric' }) }})
                </span>
            </span>
        </div>
        
        <!-- Plage min/max -->
        <div v-if="props.showValue && (min || max)" class="mt-1 flex justify-between text-xs text-base-content/50">
            <span v-if="min">
                Min: {{ typeof min === 'string' ? new Date(min).toLocaleDateString(props.locale) : min.toLocaleDateString(props.locale) }}
            </span>
            <span v-if="max">
                Max: {{ typeof max === 'string' ? new Date(max).toLocaleDateString(props.locale) : max.toLocaleDateString(props.locale) }}
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
// Styles spécifiques pour DateField
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
    // Boutons d'action dans les dates
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
    
    .text-neutral {
        color: var(--color-neutral, #6b7280);
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
    
    &.badge-neutral {
        background-color: var(--color-neutral, #6b7280);
        color: white;
    }
}

// Styles pour les icônes de valeur
.fa-calendar, .fa-calendar-check, .fa-calendar-minus, .fa-calendar-plus, .fa-calendar-week, .fa-calendar-xmark {
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
    
    &.text-neutral {
        color: var(--color-neutral, #6b7280);
    }
}
</style> 