<script setup>
/**
 * FilterField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour champ de filtrage complet, orchestrant FilterCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux filtres : radio buttons, reset automatique, compteurs
 *
 * @see https://daisyui.com/components/filter/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <FilterField label="Statut" v-model="status" :options="['Tous', 'Actifs', 'Inactifs']" />
 * 
 * // Label simple avec position par défaut différente
 * <FilterField label="Catégorie" v-model="category" defaultLabelPosition="top" :options="categories" />
 * 
 * // Label avec positions spécifiques
 * <FilterField :label="{ top: 'Filtres', inStart: '🔍' }" v-model="filter" :options="filters" />
 * 
 * // Label complexe avec slots
 * <FilterField :label="{ top: 'Options' }" v-model="selected" :options="options">
 *   <template #labelTop>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-filter"></i>
 *       Filtrer les résultats
 *     </span>
 *   </template>
 * </FilterField>
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <FilterField label="Filtres" v-model="filters" :options="filterOptions" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <FilterField label="Filtres" v-model="filter" :options="filters">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-sort"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="clearAllFilters">
 *       <i class="fa-solid fa-times"></i>
 *     </Btn>
 *   </template>
 * </FilterField>
 *
 * // Validation locale uniquement
 * <FilterField 
 *   label="Filtre" 
 *   v-model="filter"
 *   :validation="{ state: 'error', message: 'Filtre requis' }"
 * />
 *
 * // Validation avec notification
 * <FilterField 
 *   label="Filtres" 
 *   v-model="filters"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Filtres appliqués !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <FilterField 
 *   label="Filtres" 
 *   v-model="filters"
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
 * @props {Array} options - Options du filter (array de strings ou objets {value, label, disabled})
 * @props {Boolean} multiple - Sélection multiple
 * @props {String} name - Nom du groupe de radio buttons
 * @props {String} placeholder - Placeholder du filter
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot default - radio buttons natives (optionnel)
 */
/**
 * [MIGRATION 2024-06] Ce composant utilise désormais inputHelper.js pour la gestion factorisée des props/attrs input (voir /Utils/atomic-design/inputHelper.js)
 */
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import FilterCore from '@/Pages/Atoms/data-input/FilterCore.vue';
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

const props = defineProps(getInputPropsDefinition('filter', 'field'));

const $attrs = useAttrs();
const slots = useSlots();
const notificationStore = inject('notificationStore', null);
const labelConfig = computed(() => processLabelConfig(props.label, props.defaultLabelPosition));

// --- Utilisation du composable universel pour les actions contextuelles ---
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
  type: 'filter', // Type spécifique pour les filtres
  actions: props.actions,
  readonly: props.readonly,
  debounce: props.debounceTime,
  autofocus: props.autofocus,
});

// --- v-model : émettre update:modelValue quand la valeur change ---
const emit = defineEmits(['update:modelValue']);
watch(currentValue, (val) => {
  emit('update:modelValue', val);
});

// --- Validation et autres logiques existantes (inchangées) ---
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

// Props à transmettre au Core
const coreProps = computed(() => ({
    ...inputProps.value,
    modelValue: currentValue.value,
    ref: inputRef
}));

const filterFieldId = computed(
    () => props.id || `filterfield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('filter', {
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

// --- Fonctionnalités spécifiques aux filtres ---

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

// Récupération du label de l'option sélectionnée
const selectedOptionLabel = computed(() => {
    if (!currentValue.value) return props.placeholder || 'Tous';
    
    if (props.multiple && Array.isArray(currentValue.value)) {
        // Pour multiple, on affiche le nombre d'éléments sélectionnés
        const count = currentValue.value.length;
        if (count === 0) return props.placeholder || 'Tous';
        if (count === 1) {
            const option = processedOptions.value.find(opt => opt.value === currentValue.value[0]);
            return option ? option.label : currentValue.value[0];
        }
        return `${count} filtre(s) actif(s)`;
    } else {
        // Pour single, on affiche le label de l'option
        const option = processedOptions.value.find(opt => opt.value === currentValue.value);
        return option ? option.label : currentValue.value;
    }
});

// Vérification si un filtre est actif
const hasActiveFilter = computed(() => {
    if (!currentValue.value) return false;
    
    if (props.multiple && Array.isArray(currentValue.value)) {
        return currentValue.value.length > 0;
    }
    
    return currentValue.value !== '' && currentValue.value !== null && currentValue.value !== undefined;
});

// Compteur d'options actives
const activeFilterCount = computed(() => {
    if (!currentValue.value) return 0;
    
    if (props.multiple && Array.isArray(currentValue.value)) {
        return currentValue.value.length;
    }
    
    return hasActiveFilter.value ? 1 : 0;
});
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="filterFieldId"
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
                :for="filterFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le filter et les éléments over -->
            <div class="relative flex-1">
                <!-- Filter principal -->
                <FilterCore 
                    v-bind="coreProps"
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
                    <template v-if="slots.default" #default>
                        <slot />
                    </template>
                </FilterCore>

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
                :for="filterFieldId"
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
            :for="filterFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage du filtre actif (optionnel) -->
        <div v-if="hasActiveFilter" class="mt-1 text-sm text-base-content/70">
            <span class="font-medium">Filtre actif :</span> {{ selectedOptionLabel }}
            <span v-if="activeFilterCount > 1" class="ml-2 badge badge-primary badge-xs">
                {{ activeFilterCount }}
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
// Styles spécifiques pour FilterField
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
    // Boutons d'action dans les filtres
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

// Styles pour l'affichage du filtre actif
.text-sm {
    // Affichage du filtre actuel
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
    }
}
</style> 