<script setup>
defineOptions({ inheritAttrs: false });

/**
 * DateCore Atom (DaisyUI + Cally, Atomic Design)
 *
 * @description
 * Atom de base pour les sélecteurs de date, utilisant le web component Cally avec styles DaisyUI.
 * - Props : v-model, min, max, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : calendar-date natif avec Cally
 * - Gestion des attributs spécifiques aux dates : min, max, format, locale
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - Intégration avec le web component Cally pour Vue 3
 * - Fallback automatique vers input date HTML natif si Cally n'est pas disponible
 * - DateCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/calendar/
 * @see https://github.com/WickyNilliams/cally
 * @version DaisyUI v5.x + Cally
 *
 * @example
 * <DateCore v-model="date" />
 * <DateCore v-model="date" color="primary" size="lg" />
 * <DateCore v-model="date" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <DateCore 
 *   v-model="date" 
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {Date|String} modelValue - v-model (date actuelle)
 * @props {Date|String} min - Date minimum
 * @props {Date|String} max - Date maximum
 * @props {String} format - Format d'affichage (YYYY-MM-DD par défaut)
 * @props {String} locale - Locale pour l'affichage (fr par défaut)
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot previous - Icône pour le bouton précédent
 * @slot next - Icône pour le bouton suivant
 * @slot default - Contenu personnalisé du calendrier
 */

// ------------------------------------------
// 📦 Import des outils
// ------------------------------------------
import { ref, computed, onMounted, useAttrs } from 'vue'
import { getInputStyle } from '@/Composables/form/useInputStyle'
import useInputProps from '@/Composables/form/useInputProps'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import { mergeClasses } from '@/Utils/atomic-design/uiHelper'

// ------------------------------------------
// 🔧 Définition des props + emits
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('date', 'core'))
const emit = defineEmits(['update:modelValue', 'change', 'select', 'clear', 'open', 'close'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'date', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
    getInputStyle('date', {
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
// 📅 Vérification de la disponibilité de Cally
// ------------------------------------------
const isCallyAvailable = ref(false);

// Vérification immédiate si possible
if (typeof window !== 'undefined') {
    try {
        const isRegistered = window.customElements?.get('calendar-date');
        const testElement = document.createElement('calendar-date');
        const isCustomElement = testElement.constructor !== HTMLElement;
        
        isCallyAvailable.value = isRegistered || isCustomElement;
        
        if (!isCallyAvailable.value) {
            console.warn('DateCore: Cally n\'est pas disponible, utilisation du fallback input date HTML natif');
        }
    } catch (error) {
        console.warn('DateCore: Erreur lors de la vérification de Cally, utilisation du fallback:', error);
        isCallyAvailable.value = false;
    }
}

onMounted(() => {
    // Vérification supplémentaire après le montage pour les cas de chargement asynchrone
    const checkCallyAvailability = () => {
        try {
            const isRegistered = window.customElements?.get('calendar-date');
            const testElement = document.createElement('calendar-date');
            const isCustomElement = testElement.constructor !== HTMLElement;
            
            const isAvailable = isRegistered || isCustomElement;
            
            if (isAvailable && !isCallyAvailable.value) {
                isCallyAvailable.value = true;
            } else if (!isAvailable && isCallyAvailable.value) {
                isCallyAvailable.value = false;
            }
        } catch (error) {
            isCallyAvailable.value = false;
        }
    };
    
    // Vérifier à nouveau après un délai pour les cas où Cally se charge de manière asynchrone
    setTimeout(checkCallyAvailability, 100);
    setTimeout(checkCallyAvailability, 500);
});

// ------------------------------------------
// 📅 Gestion du v-model pour date
// ------------------------------------------
const currentDate = computed({
    get() {
        return props.modelValue || '';
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

// ------------------------------------------
// 📅 Formatage de la date pour l'affichage
// ------------------------------------------
const formattedDate = computed(() => {
    if (!currentDate.value) return '';
    
    try {
        const date = new Date(currentDate.value);
        if (isNaN(date.getTime())) return '';
        
        return date.toISOString().split('T')[0]; // Format YYYY-MM-DD
    } catch (error) {
        return '';
    }
});

// ------------------------------------------
// 📅 Formatage des dates min/max pour input HTML natif
// ------------------------------------------
const formattedMinDate = computed(() => {
    if (!props.min) return '';
    try {
        const date = new Date(props.min);
        if (isNaN(date.getTime())) return '';
        return date.toISOString().split('T')[0];
    } catch (error) {
        return '';
    }
});

const formattedMaxDate = computed(() => {
    if (!props.max) return '';
    try {
        const date = new Date(props.max);
        if (isNaN(date.getTime())) return '';
        return date.toISOString().split('T')[0];
    } catch (error) {
        return '';
    }
});

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onInput(e) {
    const value = e.target.value;
    currentDate.value = value;
    emit('change', value);
}

function onSelect(e) {
    const value = e.target.value;
    if (value) {
        currentDate.value = value;
        emit('select', value);
    }
}

function onClear() {
    currentDate.value = '';
    emit('clear');
}

function onOpen() {
    emit('open');
}

function onClose() {
    emit('close');
}

// ------------------------------------------
// 🎨 Icônes par défaut pour les boutons précédent/suivant
// ------------------------------------------
const defaultPreviousIcon = '<svg aria-label="Précédent" class="fill-current size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M15.75 19.5 8.25 12l7.5-7.5"></path></svg>';

const defaultNextIcon = '<svg aria-label="Suivant" class="fill-current size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path></svg>';

const dateRef = ref(null);
</script>

<template>
    <!-- 📅 Fallback vers input date HTML natif si Cally n'est pas disponible -->
    <input
        v-if="!isCallyAvailable"
        ref="dateRef"
        type="date"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="['input', atomClasses]"
        :value="formattedDate"
        :min="formattedMinDate"
        :max="formattedMaxDate"
        @input="onInput"
        @change="onSelect"
    />
    
    <!-- 📅 Web component Cally avec styles DaisyUI -->
    <component
        v-else
        :is="'calendar-date'"
        ref="dateRef"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="['cally', atomClasses]"
        :value="formattedDate"
        :min="min"
        :max="max"
        :format="format"
        :locale="locale"
        :placeholder="placeholder"
        :clearable="clearable"
        :week-start="weekStart"
        :first-day-of-week="firstDayOfWeek"
        :show-week-numbers="showWeekNumbers"
        :show-today="showToday"
        :today-label="todayLabel"
        :clear-label="clearLabel"
        :previous-label="previousLabel"
        :next-label="nextLabel"
        :month-label="monthLabel"
        :year-label="yearLabel"
        :disabled-dates="disabledDates"
        :enabled-dates="enabledDates"
        :range="range"
        :multiple="multiple"
        :auto-close="autoClose"
        :position="position"
        :theme="theme"
        @input="onInput"
        @change="onSelect"
        @clear="onClear"
        @open="onOpen"
        @close="onClose"
    >
        <!-- Bouton précédent -->
        <template #previous>
            <!-- eslint-disable-next-line vue/no-v-html -- icône interne (HTML statique) -->
            <slot name="previous" v-html="defaultPreviousIcon"></slot>
        </template>
        
        <!-- Bouton suivant -->
        <template #next>
            <!-- eslint-disable-next-line vue/no-v-html -- icône interne (HTML statique) -->
            <slot name="next" v-html="defaultNextIcon"></slot>
        </template>
        
        <!-- Contenu personnalisé -->
        <slot>
            <component :is="'calendar-month'"></component>
        </slot>
    </component>
</template>

<style scoped lang="scss">
// Styles spécifiques pour DateCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

input[type="date"].input {
    // Styles pour le fallback input date HTML natif
    outline: none;
    transition: all 0.2s ease-in-out;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
    // États de focus
    &:focus {
        outline: none;
        border-color: color-mix(in srgb, var(--color) 80%, transparent);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
    }
    
    // États disabled
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    // Variant Glass - Effet glassmorphisme
    &.bg-transparent.border {
        @apply border-glass-md box-glass-md;
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
        }
        
        &:focus {
            border-color: color-mix(in srgb, var(--color) 80%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color) 20%, transparent);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed {
        @apply border-glass-sm;
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        @apply border-glass-md;
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
    }
    
    // Variant Ghost - Transparent
    &.border.border-transparent.bg-transparent {
        background-color: transparent;
        border-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            border-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.border-b-2.bg-transparent.rounded-none {
        @apply border-glass-b-md;
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-b-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
    }
}

.cally {
    // Styles de base pour tous les calendriers Cally
    display: inline-block;
    transition: all 0.2s ease-in-out;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    --cally-color: var(--color); // Utilise --color pour Cally
    
    // États disabled
    &:has([disabled]) {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    // Variant Glass - Effet glassmorphisme
    &.bg-transparent.border {
        @apply border-glass-md box-glass-md;
        border-color: color-mix(in srgb, var(--color) 30%, transparent);
        background-color: color-mix(in srgb, var(--color) 10%, transparent);
        
        &:hover {
            @apply border-glass-lg box-glass-lg;
            border-color: color-mix(in srgb, var(--color) 50%, transparent);
            background-color: color-mix(in srgb, var(--color) 15%, transparent);
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed {
        @apply border-glass-sm;
        border-style: dashed;
        border-width: 2px;
        background-color: color-mix(in srgb, var(--color) 5%, transparent);
        
        &:hover {
            @apply border-glass-md;
            background-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Outline - Bordure visible
    &.border-2.bg-transparent {
        @apply border-glass-md;
        border-width: 2px;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
    }
    
    // Variant Ghost - Transparent
    &.border.border-transparent.bg-transparent {
        background-color: transparent;
        border-color: transparent;
        
        &:hover {
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
            border-color: color-mix(in srgb, var(--color) 10%, transparent);
        }
    }
    
    // Variant Soft - Bordure inférieure uniquement
    &.border-b-2.bg-transparent.rounded-none {
        @apply border-glass-b-md;
        border-bottom-width: 2px;
        border-radius: 0;
        background-color: transparent;
        
        &:hover {
            @apply border-glass-b-lg;
            background-color: color-mix(in srgb, var(--color) 5%, transparent);
        }
    }
    
    // Animations
    &.transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    
    &.duration-200 {
        transition-duration: 200ms;
    }
}

// Styles pour les tailles DaisyUI
.cally-xs {
    font-size: 0.75rem;
    
    svg {
        width: 0.75rem;
        height: 0.75rem;
    }
}

.cally-sm {
    font-size: 0.875rem;
    
    svg {
        width: 1rem;
        height: 1rem;
    }
}

.cally-md {
    font-size: 1rem;
    
    svg {
        width: 1.25rem;
        height: 1.25rem;
    }
}

.cally-lg {
    font-size: 1.125rem;
    
    svg {
        width: 1.5rem;
        height: 1.5rem;
    }
}

.cally-xl {
    font-size: 1.25rem;
    
    svg {
        width: 1.75rem;
        height: 1.75rem;
    }
}

// Styles pour les icônes de navigation
svg {
    transition: all 0.2s ease-in-out;
    
    &:hover {
        transform: scale(1.1);
    }
}

// Styles pour les jours du calendrier
calendar-month {
    // Styles pour le mois du calendrier
    display: block;
    
    // Jours sélectionnés - utilise var(--color)
    [selected] {
        background-color: var(--cally-color);
        color: white;
        border-radius: 0.375rem;
    }
    
    // Jours hover - utilise var(--color) avec transparence
    [hover] {
        background-color: color-mix(in srgb, var(--cally-color) 20%, transparent);
        border-radius: 0.375rem;
    }
    
    // Jours désactivés
    [disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }
}

// Styles pour les en-têtes de mois/année
[slot="month"], [slot="year"] {
    font-weight: 600;
    color: var(--color-base-content, #374151);
}

// Styles pour les boutons de navigation - utilise var(--color)
[slot="previous"], [slot="next"] {
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    
    &:hover:not([disabled]) {
        background-color: color-mix(in srgb, var(--cally-color) 20%, transparent);
    }
    
    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
}

// Styles pour les jours de la semaine
[role="columnheader"] {
    font-weight: 600;
    color: var(--color-base-content, #374151);
    text-align: center;
    padding: 0.5rem;
}

// Styles pour les cellules de jours - utilise var(--color)
[role="gridcell"] {
    text-align: center;
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    
    &:hover:not([disabled]) {
        background-color: color-mix(in srgb, var(--cally-color) 20%, transparent);
    }
    
    &[aria-selected="true"] {
        background-color: var(--cally-color);
        color: white;
    }
    
    &[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }
}

// Application des classes color-* pour définir --color
.color-primary { --color: var(--color-primary-500); --cally-color: var(--color-primary-500); }
.color-secondary { --color: var(--color-secondary-500); --cally-color: var(--color-secondary-500); }
.color-accent { --color: var(--color-accent-500); --cally-color: var(--color-accent-500); }
.color-info { --color: var(--color-info-500); --cally-color: var(--color-info-500); }
.color-success { --color: var(--color-success-500); --cally-color: var(--color-success-500); }
.color-warning { --color: var(--color-warning-500); --cally-color: var(--color-warning-500); }
.color-error { --color: var(--color-error-500); --cally-color: var(--color-error-500); }
.color-neutral { --color: var(--color-neutral-500); --cally-color: var(--color-neutral-500); }
</style> 