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
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.rating {
    // Styles de base pour tous les ratings
    display: inline-flex;
    gap: 0.125rem;
    transition: all 0.2s ease-in-out;
    
    // États disabled
    &:has(input:disabled) {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    // Inputs radio (étoiles)
    input[type="radio"] {
        appearance: none;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        
        &:disabled {
            cursor: not-allowed;
        }
        
        // Masque d'étoile
        &.mask-star {
            background: #e5e7eb;
            mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            -webkit-mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            
            &:checked {
                background: var(--color-primary, #3b82f6);
            }
            
            &:hover:not(:disabled) {
                transform: scale(1.1);
                background: var(--color-primary, #3b82f6);
            }
        }
        
        // Masque d'étoile pour demi-rating
        &.mask-star-2 {
            background: #e5e7eb;
            mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            -webkit-mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            
            &.mask-half-1 {
                background: var(--color-primary, #3b82f6);
                mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
                -webkit-mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
            }
            
            &.mask-half-2 {
                background: #e5e7eb;
                mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
                -webkit-mask: url("data:image/svg+xml,%3csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m9.05 3.86 1.78 5.14H18l-4.84 3.51 1.78 5.15-4.89-3.55-4.89 3.55 1.78-5.15L.05 9H7.27l1.78-5.14z'/%3e%3c/svg%3e") center/contain;
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
    
    // Variant Glass - Effet de verre
    &.bg-transparent.border.border-gray-300 {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-color: rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        
        &:hover {
            box-shadow: 
                0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                background: rgba(255, 255, 255, 0.3);
                
                &:checked {
                    background: var(--color-primary, #3b82f6);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-primary, #3b82f6);
                }
            }
        }
    }
    
    // Variant Dash - Style pointillé
    &.border-dashed.border-2 {
        background: rgba(255, 255, 255, 0.05);
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-secondary, #8b5cf6);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-secondary, #8b5cf6);
                }
            }
        }
    }
    
    // Variant Outline - Bordure avec effet
    &.border-2.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-success, #10b981);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-success, #10b981);
                }
            }
        }
    }
    
    // Variant Ghost - Fond invisible
    &.border.border-transparent.bg-transparent {
        &:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-neutral, #6b7280);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-neutral, #6b7280);
                }
            }
        }
    }
    
    // Variant Soft - Style doux
    &.border-b-2.border-gray-300.bg-transparent.rounded-none {
        background: rgba(255, 255, 255, 0.05);
        border-bottom-width: 2px;
        
        &:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-accent, #f59e0b);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-accent, #f59e0b);
                }
            }
        }
    }
    
    // Styles pour les couleurs DaisyUI
    &.rating-primary {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-primary, #3b82f6);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-primary, #3b82f6);
                }
            }
        }
    }
    
    &.rating-secondary {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-secondary, #8b5cf6);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-secondary, #8b5cf6);
                }
            }
        }
    }
    
    &.rating-accent {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-accent, #f59e0b);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-accent, #f59e0b);
                }
            }
        }
    }
    
    &.rating-info {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-info, #06b6d4);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-info, #06b6d4);
                }
            }
        }
    }
    
    &.rating-success {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-success, #10b981);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-success, #10b981);
                }
            }
        }
    }
    
    &.rating-warning {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-warning, #f59e0b);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-warning, #f59e0b);
                }
            }
        }
    }
    
    &.rating-error {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-error, #ef4444);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-error, #ef4444);
                }
            }
        }
    }
    
    &.rating-neutral {
        input[type="radio"] {
            &.mask-star, &.mask-star-2 {
                &:checked {
                    background: var(--color-neutral, #6b7280);
                }
                
                &:hover:not(:disabled) {
                    background: var(--color-neutral, #6b7280);
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

// Styles pour les tailles DaisyUI
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

// Styles pour les labels inline
.label-text {
    // Labels inline pour les ratings
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les labels flottants
.floating-label {
    // Label flottant pour les ratings
    position: relative;
    
    .label-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        transition: all 0.2s ease-in-out;
    }
}

// Container pour les ratings
.rating-container {
    display: inline-flex;
    align-items: center;
}
</style>
