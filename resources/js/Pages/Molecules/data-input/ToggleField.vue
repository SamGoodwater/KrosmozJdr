<script setup>
/**
 * ToggleField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour switch toggle complet, orchestrant ToggleCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux toggles : on/off states, icônes, animations
 *
 * @see https://daisyui.com/components/toggle/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <ToggleField label="Activer les notifications" v-model="notifications" />
 * 
 * // Label simple avec position par défaut différente
 * <ToggleField label="Mode sombre" v-model="darkMode" defaultLabelPosition="start" />
 * 
 * // Label avec positions spécifiques
 * <ToggleField :label="{ start: 'Fonctionnalité', end: 'avancée' }" v-model="advanced" />
 * 
 * // Label complexe avec slots
 * <ToggleField :label="{ start: 'Options' }" v-model="enabled">
 *   <template #labelStart>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-toggle-on"></i>
 *       Activer les options
 *     </span>
 *   </template>
 * </ToggleField>
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <ToggleField label="Préférences" v-model="preferences" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <ToggleField label="Permissions" v-model="permissions">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-lock"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="resetToDefault">
 *       <i class="fa-solid fa-undo"></i>
 *     </Btn>
 *   </template>
 * </ToggleField>
 *
 * // Validation locale uniquement
 * <ToggleField 
 *   label="Conditions" 
 *   v-model="accepted"
 *   :validation="{ state: 'error', message: 'Vous devez accepter les conditions' }"
 * />
 *
 * // Validation avec notification
 * <ToggleField 
 *   label="Newsletter" 
 *   v-model="newsletter"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Inscription réussie !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <ToggleField 
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
 * @props {String} onLabel - Label pour l'état activé
 * @props {String} offLabel - Label pour l'état désactivé
 * @props {String} placeholder - Placeholder du toggle
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot iconOn, iconOff - Slots pour icônes on/off
 * @slot default - toggle natif (optionnel)
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue';
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
const props = defineProps(getInputPropsDefinition('toggle', 'field'));

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
  type: 'toggle', // Type spécifique pour les toggles
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

const toggleFieldId = computed(
    () => props.id || `togglefield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('toggle', {
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

// --- Fonctionnalités spécifiques aux toggles ---

// Vérification si le toggle est activé
const isEnabled = computed(() => {
    return !!currentValue.value;
});

// Récupération du label de l'état actuel
const currentStateLabel = computed(() => {
    if (isEnabled.value) {
        return props.onLabel || 'Activé';
    } else {
        return props.offLabel || 'Désactivé';
    }
});

// Icône de l'état actuel
const currentStateIcon = computed(() => {
    if (isEnabled.value) {
        return 'fa-solid fa-toggle-on';
    } else {
        return 'fa-solid fa-toggle-off';
    }
});

// Couleur de l'état actuel
const currentStateColor = computed(() => {
    if (isEnabled.value) {
        return 'success';
    } else {
        return 'neutral';
    }
});

// Classe CSS pour l'état actuel
const currentStateClass = computed(() => {
    if (isEnabled.value) {
        return 'text-success';
    } else {
        return 'text-neutral';
    }
});
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="toggleFieldId"
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
                :for="toggleFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le toggle et les éléments over -->
            <div class="relative flex-1">
                <!-- Toggle principal -->
                <ToggleCore 
                    v-bind="inputProps"
                    v-model="currentValue"
                    :aria-invalid="processedValidation?.state === 'error'"
                >
                    <template v-if="slots.default" #default>
                        <slot />
                    </template>
                </ToggleCore>

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
                :for="toggleFieldId"
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
            :for="toggleFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage de l'état (optionnel) -->
        <div v-if="isEnabled !== null" class="mt-1 text-sm text-base-content/70">
            <span class="font-medium">État :</span> 
            <span :class="currentStateClass" class="flex items-center gap-1">
                <i :class="currentStateIcon"></i>
                {{ currentStateLabel }}
            </span>
            <span :class="`ml-2 badge badge-${currentStateColor} badge-xs`">
                {{ isEnabled ? 'ON' : 'OFF' }}
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
// Styles spécifiques pour ToggleField
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
    // Boutons d'action dans les toggles
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

// Styles pour l'affichage de l'état
.text-sm {
    // Affichage de l'état actuel
    transition: all 0.2s ease-in-out;
    
    .font-medium {
        font-weight: 500;
    }
    
    .text-success {
        color: var(--color-success, #10b981);
    }
    
    .text-neutral {
        color: var(--color-neutral, #6b7280);
    }
    
    .badge {
        // Badge de statut
        transition: all 0.2s ease-in-out;
        
        &.badge-success {
            background-color: var(--color-success, #10b981);
            color: white;
        }
        
        &.badge-neutral {
            background-color: var(--color-neutral, #6b7280);
            color: white;
        }
    }
}

// Styles pour les icônes d'état
.fa-toggle-on {
    color: var(--color-success, #10b981);
}

.fa-toggle-off {
    color: var(--color-neutral, #6b7280);
}
</style> 