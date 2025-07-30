<script setup>
/**
 * ColorField Molecule (DaisyUI + vue-color-kit, Atomic Design)
 *
 * @description
 * Molecule pour sélecteur de couleur complet, orchestrant ColorCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 5 positions de labels : top, bottom, start, end, floating (pas de inStart/inEnd pour les couleurs)
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Fonctionnalités spécifiques aux couleurs : format, palette, historique, pipette
 *
 * @see https://www.vuescript.com/color-picker-kit/
 * @version DaisyUI v5.x + vue-color-kit
 *
 * @example
 * // Label simple (floating par défaut)
 * <ColorField label="Couleur principale" v-model="primaryColor" />
 * 
 * // Label simple avec position par défaut différente
 * <ColorField label="Couleur" v-model="color" defaultLabelPosition="start" />
 * 
 * // Label avec positions spécifiques
 * <ColorField :label="{ start: 'Couleur', end: 'Format: HEX' }" v-model="color" />
 * 
 * // Label complexe avec slots
 * <ColorField :label="{ start: 'Couleur' }" v-model="color">
 *   <template #labelStart>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-palette"></i>
 *       Couleur du thème
 *     </span>
 *   </template>
 * </ColorField>
 * 
 * // Avec format personnalisé
 * <ColorField label="Couleur" v-model="color" format="rgb" />
 * 
 * // Avec palette personnalisée
 * <ColorField 
 *   label="Couleur" 
 *   v-model="color"
 *   :colorsDefault="['#FF0000', '#00FF00', '#0000FF']"
 * />
 * 
 * // Avec thème personnalisé
 * <ColorField label="Couleur" v-model="color" theme="light" />
 * 
 * // Avec actions automatiques (reset dans overEnd si useFieldComposable)
 * <ColorField label="Couleur" v-model="color" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <ColorField label="Couleur" v-model="color">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-palette"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="setRandomColor">
 *       <i class="fa-solid fa-dice"></i>
 *     </Btn>
 *   </template>
 * </ColorField>
 *
 * // Validation locale uniquement
 * <ColorField 
 *   label="Couleur" 
 *   v-model="color"
 *   :validation="{ state: 'error', message: 'Couleur invalide' }"
 * />
 *
 * // Validation avec notification
 * <ColorField 
 *   label="Couleur" 
 *   v-model="color"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Couleur valide !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <ColorField 
 *   label="Couleur" 
 *   v-model="color"
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
 * @props {String} format - Format de couleur (hex, rgb, rgba, hsl, hsla)
 * @props {String} theme - Thème du color picker (light, dark)
 * @props {Array} colorsDefault - Palette de couleurs par défaut
 * @props {String} colorsHistoryKey - Clé pour l'historique des couleurs
 * @props {Boolean} suckerHide - Masquer le pipette
 * @props {String} placeholder - Placeholder du color picker
 * @props {Boolean} showValue - Afficher la couleur actuelle
 * @props {Boolean} showPreview - Afficher l'aperçu de la couleur
 * @props {Boolean} showFormat - Afficher le format de la couleur
 * @props {Boolean} showRandom - Afficher le bouton couleur aléatoire
 * @props {Boolean} showClear - Afficher le bouton "Effacer"
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (reset, random, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 * @slot valueDisplay - Slot pour affichage personnalisé de la valeur
 * @slot default - Contenu personnalisé du color picker
 */
// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { computed, ref, useSlots, inject, watch, useAttrs } from 'vue';
import ColorCore from '@/Pages/Atoms/data-input/ColorCore.vue';
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
const props = defineProps(getInputPropsDefinition('color', 'field'));

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
  type: 'color', // Type spécifique pour les couleurs
  actions: props.actions,
  readonly: props.readonly,
  debounce: props.debounceTime,
  autofocus: props.autofocus,
});

// ------------------------------------------
// 🔄 v-model : émettre update:modelValue quand la valeur change
// ------------------------------------------
const emit = defineEmits(['update:modelValue', 'changeColor', 'openSucker']);
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

const colorFieldId = computed(
    () => props.id || `colorfield-${Math.random().toString(36).substr(2, 9)}`,
);

// Configuration de style pour transmission aux labels et helpers
const styleProperties = computed(() => 
    getInputStyleProperties('color', {
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

// --- Fonctionnalités spécifiques aux couleurs ---

// Validation de la couleur
const validatedColor = computed(() => {
    const value = currentValue.value;
    
    if (!value) return '#000000';
    
    // Validation basique de format hex
    if (props.format === 'hex' && !/^#[0-9A-F]{6}$/i.test(value)) {
        return '#000000';
    }
    
    return value;
});

// Formatage de la couleur pour l'affichage
const formattedColor = computed(() => {
    const value = validatedColor.value;
    if (!value) return '#000000';
    
    // Retourner la valeur telle quelle, le formatage est géré par le Core
    return value;
});

// Couleur de contraste pour le texte
const contrastColor = computed(() => {
    const value = validatedColor.value;
    if (!value || value === 'rgba(0,0,0,0)') return '#000000';
    
    // Calcul simple du contraste pour hex
    if (value.startsWith('#')) {
        const hex = value.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        return brightness > 128 ? '#000000' : '#FFFFFF';
    }
    
    // Pour les autres formats, utiliser une couleur par défaut
    return '#000000';
});

// Icône selon le format
const formatIcon = computed(() => {
    switch (props.format) {
        case 'hex': return 'fa-solid fa-hashtag';
        case 'rgb': return 'fa-solid fa-rgb';
        case 'rgba': return 'fa-solid fa-rgba';
        case 'hsl': return 'fa-solid fa-circle-half-stroke';
        case 'hsla': return 'fa-solid fa-circle-half-stroke';
        default: return 'fa-solid fa-palette';
    }
});

// Badge de format
const formatBadge = computed(() => {
    return {
        text: props.format.toUpperCase(),
        color: 'info'
    };
});

// Actions spécifiques aux couleurs
function setRandomColor() {
    const colors = [
        '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF',
        '#FFA500', '#800080', '#008000', '#FFC0CB', '#A52A2A', '#808080'
    ];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    currentValue.value = randomColor;
}

function setTransparent() {
    currentValue.value = 'rgba(0,0,0,0)';
}

function setBlack() {
    currentValue.value = '#000000';
}

function setWhite() {
    currentValue.value = '#FFFFFF';
}

// Actions contextuelles personnalisées pour les couleurs
const colorActions = computed(() => {
    const actions = [];
    
    if (props.showRandom) {
        actions.push({
            key: 'random',
            icon: 'fa-solid fa-dice',
            ariaLabel: 'Couleur aléatoire',
            tooltip: 'Couleur aléatoire',
            color: 'accent',
            variant: 'ghost',
            size: 'xs',
            onClick: setRandomColor
        });
    }
    
    if (props.showClear && currentValue.value && currentValue.value !== '#000000') {
        actions.push({
            key: 'clear',
            icon: 'fa-solid fa-palette-xmark',
            ariaLabel: 'Effacer la couleur',
            tooltip: 'Effacer la couleur',
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
    return [...actionsToDisplay.value, ...colorActions.value];
});

// Gestion des événements du ColorCore
function onColorChange(colorData) {
    emit('changeColor', colorData);
}

function onSuckerOpen(isOpen) {
    emit('openSucker', isOpen);
}
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="colorFieldId"
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
                :for="colorFieldId"
                :color="styleProperties.labelColor"
                :size="styleProperties.labelSize"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le color picker et les éléments over -->
            <div class="relative flex-1">
                <!-- Color picker principal -->
                <ColorCore 
                    v-bind="inputProps"
                    v-model="currentValue"
                    :aria-invalid="processedValidation?.state === 'error'"
                >
                    <slot />
                </ColorCore>

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
                :for="colorFieldId"
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
            :for="colorFieldId"
            :color="styleProperties.labelColor"
            :size="styleProperties.labelSize"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Affichage de la valeur (optionnel) -->
        <div v-if="props.showValue && validatedColor !== null" class="mt-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-base-content/70">Couleur :</span>
                <slot name="valueDisplay">
                    <span class="flex items-center gap-2 font-bold">
                        <!-- Aperçu de la couleur -->
                        <div 
                            v-if="props.showPreview"
                            class="w-6 h-6 rounded border-2 border-gray-300"
                            :style="{ backgroundColor: validatedColor }"
                        ></div>
                        
                        <!-- Valeur formatée -->
                        <span :style="{ color: contrastColor }" class="font-mono">
                            {{ formattedColor }}
                        </span>
                        
                        <!-- Icône de format -->
                        <i :class="formatIcon" class="text-sm opacity-70"></i>
                    </span>
                </slot>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Badge de format -->
                <span v-if="props.showFormat" :class="`badge badge-${formatBadge.color} badge-xs`">
                    {{ formatBadge.text }}
                </span>
            </div>
        </div>
        
        <!-- Informations sur la couleur -->
        <div v-if="props.showValue && validatedColor !== null" class="mt-1 text-sm text-base-content/70">
            <span class="font-medium">Informations :</span> 
            <span class="flex items-center gap-1">
                <i class="fa-solid fa-palette"></i>
                Format {{ props.format.toUpperCase() }}
                <span v-if="validatedColor" class="ml-1 text-xs">
                    ({{ validatedColor.length }} caractères)
                </span>
            </span>
        </div>
        
        <!-- Actions rapides pour les couleurs -->
        <div v-if="props.showValue && validatedColor !== null" class="mt-2 flex gap-2">
            <Btn 
                v-if="props.showRandom"
                size="xs" 
                variant="outline" 
                @click="setRandomColor"
            >
                <i class="fa-solid fa-dice mr-1"></i>
                Aléatoire
            </Btn>
            
            <Btn 
                size="xs" 
                variant="outline" 
                @click="setTransparent"
            >
                <i class="fa-solid fa-eye-slash mr-1"></i>
                Transparent
            </Btn>
            
            <Btn 
                size="xs" 
                variant="outline" 
                @click="setBlack"
            >
                <i class="fa-solid fa-circle mr-1"></i>
                Noir
            </Btn>
            
            <Btn 
                size="xs" 
                variant="outline" 
                @click="setWhite"
            >
                <i class="fa-solid fa-circle mr-1"></i>
                Blanc
            </Btn>
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
// Styles spécifiques pour ColorField
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
    // Boutons d'action dans les couleurs
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
    
    .font-bold {
        font-weight: 700;
    }
    
    .font-mono {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }
}

// Styles pour les badges de format
.badge {
    // Badge de format
    transition: all 0.2s ease-in-out;
    
    &.badge-info {
        background-color: var(--color-info, #06b6d4);
        color: white;
    }
    
    &.badge-success {
        background-color: var(--color-success, #10b981);
        color: white;
    }
    
    &.badge-warning {
        background-color: var(--color-warning, #f59e0b);
        color: white;
    }
    
    &.badge-error {
        background-color: var(--color-error, #ef4444);
        color: white;
    }
    
    &.badge-neutral {
        background-color: var(--color-neutral, #6b7280);
        color: white;
    }
}

// Styles pour les icônes de format
.fa-hashtag, .fa-rgb, .fa-rgba, .fa-circle-half-stroke, .fa-palette {
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

// Styles pour l'aperçu de couleur
.w-6.h-6 {
    // Aperçu de couleur
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    
    &:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
}

// Styles pour les actions rapides
.btn.btn-outline {
    // Boutons d'action rapide
    transition: all 0.2s ease-in-out;
    
    &:hover {
        transform: scale(1.05);
    }
    
    i {
        transition: all 0.2s ease-in-out;
    }
    
    &:hover i {
        transform: rotate(10deg);
    }
}
</style> 