<script setup>
defineOptions({ inheritAttrs: false });

/**
 * RatingCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les étoiles de notation, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : v-model, max, value, disabled, readonly, required, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputStyle pour les classes DaisyUI/Tailwind
 * - Slot par défaut : rating natif avec étoiles
 * - Gestion des attributs spécifiques aux ratings : max, value, half-rating
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Support de la prop `style` (objet) et `variant` (string)
 * - RatingCore ne supporte pas les labels inline (pas de labelFloating, labelInStart, labelInEnd)
 *
 * @see https://daisyui.com/components/rating/
 * @version DaisyUI v5.x
 *
 * @example
 * <RatingCore v-model="rating" :max="5" />
 * <RatingCore v-model="rating" :max="5" color="primary" size="lg" />
 * <RatingCore v-model="rating" :max="5" variant="glass" rounded="lg" />
 * 
 * // Avec objet style
 * <RatingCore 
 *   v-model="rating" 
 *   :max="5"
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }" 
 * />
 *
 * @props {Number} modelValue - v-model (note actuelle)
 * @props {Number} max - Nombre maximum d'étoiles
 * @props {Boolean} half - Support des demi-étoiles
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - rating natif (optionnel)
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
const props = defineProps(getInputPropsDefinition('rating', 'core'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// ⚙️ Attributs HTML + événements natifs filtrés
// ------------------------------------------
const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'rating', 'core')

// ------------------------------------------
// 🎨 Style dynamique basé sur variant, color, etc.
// ------------------------------------------
const atomClasses = computed(() =>
  mergeClasses(
        getInputStyle('rating', {
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
// ⭐ Gestion du v-model pour rating
// ------------------------------------------
const currentRating = computed({
    get() {
        return props.modelValue || 0;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

// ------------------------------------------
// 🌟 Génération des étoiles
// ------------------------------------------
const stars = computed(() => {
    const count = props.number || 5;
    const rating = currentRating.value;
    const items = props.items || [];
    
    return Array.from({ length: count }, (_, index) => {
        const starIndex = index + 1;
        const isActive = starIndex <= rating;
        const isHalf = props.half && starIndex === Math.ceil(rating) && rating % 1 !== 0;
        
        return {
            index: starIndex,
            isActive,
            isHalf,
            content: items[index] || '★',
            class: isActive ? 'text-yellow-400' : 'text-gray-300'
        };
    });
});

const ratingRef = ref(null);

// ------------------------------------------
// 🎯 Gestion des événements
// ------------------------------------------
function onStarClick(index) {
    if (props.disabled || props.readonly) return;
    currentRating.value = index;
}

function onStarHover(index) {
    if (props.disabled || props.readonly) return;
    // Optionnel : prévisualisation au survol
}

function onStarLeave() {
    if (props.disabled || props.readonly) return;
    // Optionnel : réinitialisation de la prévisualisation
}
</script>

<template>
    <!-- ⭐ Rating simple sans label (RatingCore ne supporte pas les labels inline/floating) -->
    <div 
        ref="ratingRef"
        v-bind="inputAttrs"
        v-on="listeners"
        :class="atomClasses"
        role="radiogroup"
        :aria-label="props.ariaLabel || 'Notation'"
    >
        <input
            v-for="star in stars"
            :key="star.index"
            type="radio"
            :name="props.name || 'rating'"
            :value="star.index"
            :checked="star.index === currentRating"
            :disabled="props.disabled"
            :readonly="props.readonly"
            :class="star.class"
            @click="onStarClick(star.index)"
            @mouseenter="onStarHover(star.index)"
            @mouseleave="onStarLeave"
        />
    </div>
</template>

<style scoped lang="scss">
// Styles spécifiques pour RatingCore
// Utilisation des classes utilitaires glassmorphisme et var(--color) pour les couleurs

.rating {
    // Styles de base pour tous les ratings
    display: inline-flex;
    gap: 0.125rem;
    transition: all 0.2s ease-in-out;
    --color: var(--color-primary-500); // Couleur par défaut (sera surchargée par color-{name})
    
    // États disabled
    &:has(input:disabled) {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    // Inputs radio (étoiles) - utilise var(--color)
    input[type="radio"] {
        appearance: none;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        
        &:disabled {
            cursor: not-allowed;
        }
        
        // Masque d'étoile
        &.mask-star {
            background-color: color-mix(in srgb, var(--color) 20%, transparent);
            mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            -webkit-mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            
            &:checked {
                background-color: var(--color);
            }
            
            &:hover:not(:disabled) {
                transform: scale(1.1);
                background-color: color-mix(in srgb, var(--color) 80%, transparent);
            }
        }
        
        // Masque d'étoile pour demi-rating
        &.mask-star-2 {
            background-color: color-mix(in srgb, var(--color) 20%, transparent);
            mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            -webkit-mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            
            &.mask-half-1 {
                background-color: var(--color);
            }
            
            &.mask-half-2 {
                background-color: color-mix(in srgb, var(--color) 20%, transparent);
            }
            
            &:hover:not(:disabled) {
                transform: scale(1.1);
            }
        }
        
        // Radio caché
        &.rating-hidden {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
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
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                background-color: color-mix(in srgb, var(--color) 30%, transparent);
                
                &:checked {
                    background-color: var(--color);
                }
                
                &:hover:not(:disabled) {
                    background-color: color-mix(in srgb, var(--color) 80%, transparent);
                }
            }
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
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background-color: var(--color);
                }
                
                &:hover:not(:disabled) {
                    background-color: color-mix(in srgb, var(--color) 80%, transparent);
                }
            }
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
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background-color: var(--color);
                }
                
                &:hover:not(:disabled) {
                    background-color: color-mix(in srgb, var(--color) 80%, transparent);
                }
            }
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
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background-color: var(--color);
                }
                
                &:hover:not(:disabled) {
                    background-color: color-mix(in srgb, var(--color) 80%, transparent);
                }
            }
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
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background-color: var(--color);
                }
                
                &:hover:not(:disabled) {
                    background-color: color-mix(in srgb, var(--color) 80%, transparent);
                }
            }
        }
    }
    
    // Animations
    &.hover\\:scale-105:hover {
        transform: scale(1.05);
    }
    
    &.focus\\:scale-105:focus {
        transform: scale(1.05);
    }
    
    &.transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    
    &.duration-200 {
        transition-duration: 200ms;
    }
}

// Styles pour les tailles DaisyUI (conservés car spécifiques au rating)
.rating-xs {
    gap: 0.0625rem;
    
    input[type="radio"] {
        width: 1rem;
        height: 1rem;
    }
}

.rating-sm {
    gap: 0.125rem;
    
    input[type="radio"] {
        width: 1.25rem;
        height: 1.25rem;
    }
}

.rating-md {
    gap: 0.125rem;
    
    input[type="radio"] {
        width: 1.5rem;
        height: 1.5rem;
    }
}

.rating-lg {
    gap: 0.25rem;
    
    input[type="radio"] {
        width: 2rem;
        height: 2rem;
    }
}

.rating-xl {
    gap: 0.25rem;
    
    input[type="radio"] {
        width: 2.5rem;
        height: 2.5rem;
    }
}

// Application des classes color-* pour définir --color
.color-primary { --color: var(--color-primary-500); }
.color-secondary { --color: var(--color-secondary-500); }
.color-accent { --color: var(--color-accent-500); }
.color-info { --color: var(--color-info-500); }
.color-success { --color: var(--color-success-500); }
.color-warning { --color: var(--color-warning-500); }
.color-error { --color: var(--color-error-500); }
.color-neutral { --color: var(--color-neutral-500); }
</style>
