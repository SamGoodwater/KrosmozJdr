<script setup>
/**
 * Dropdown Atom (Custom)
 *
 * @description
 * Composant atomique Dropdown custom avec flexibilité maximale.
 * - Trigger personnalisable (texte simple ou slot)
 * - Contenu totalement personnalisable
 * - Positionnement intelligent
 * - Navigation clavier complète
 * - Responsive et optimisé mobile
 * - Système de variants et colors
 *
 * @example
 * <!-- Trigger simple avec Btn automatique -->
 * <Dropdown 
 *   trigger="Menu" 
 *   variant="glass" 
 *   color="primary"
 *   placement="bottom-end"
 * >
 *   <template #content>
 *     <div>Contenu personnalisé</div>
 *   </template>
 * </Dropdown>
 *
 * <!-- Trigger custom avec Btn personnalisé -->
 * <Dropdown placement="bottom-end">
 *   <template #trigger>
 *     <Btn variant="ghost" circle>
 *       <Icon source="fa-bars" />
 *     </Btn>
 *   </template>
 *   <template #content>
 *     <div>Contenu personnalisé</div>
 *   </template>
 * </Dropdown>
 *
 * <!-- Dropdown avec formulaire (ne se ferme pas au clic) -->
 * <Dropdown :close-on-content-click="false">
 *   <template #trigger>
 *     <Btn variant="outline">Options</Btn>
 *   </template>
 *   <template #content>
 *     <form>
 *       <label><input type="checkbox" /> Option 1</label>
 *       <label><input type="checkbox" /> Option 2</label>
 *     </form>
 *   </template>
 * </Dropdown>
 *
 * @props {String} trigger - Texte du trigger (optionnel si slot #trigger)
 * @props {String} variant - Variant du trigger (glass, ghost, outline, etc.)
 * @props {String} color - Couleur du trigger (primary, secondary, neutral, etc.)
 * @props {String} size - Taille du trigger (xs, sm, md, lg, xl)
 * @props {String} placement - Position (bottom-end, top-start, etc.)
 * @props {Number} offset - Distance entre trigger et contenu
 * @props {Boolean} hover - Ouverture au survol
 * @props {Boolean} closeOnContentClick - Fermer le dropdown lors d'un clic sur le contenu (défaut: true)
 * @props {Boolean} disabled - Désactiver le dropdown
 * @props {String} ariaLabel - Label d'accessibilité
 * @slot trigger - Trigger personnalisé
 * @slot content - Contenu du dropdown
 */
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { usePositioning } from '@/Composables/layout/usePositioning';
import { useClickOutside } from '@/Composables/layout/useClickOutside';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { variantList, colorList, sizeList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
  ...getCommonProps(),
  ...getCustomUtilityProps(),
  // Trigger
  trigger: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'glass',
    validator: (value) => variantList.includes(value),
  },
  color: {
    type: String,
    default: 'neutral',
    validator: (value) => colorList.includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => sizeList.includes(value),
  },
  // Positionnement
  placement: {
    type: String,
    default: 'bottom-end',
  },
  offset: {
    type: Number,
    default: 8,
  },
  // Comportement
  hover: {
    type: Boolean,
    default: false,
  },
  closeOnContentClick: {
    type: Boolean,
    default: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  // Accessibilité
  ariaLabel: {
    type: String,
    default: '',
  },
});

// ID unique pour ce dropdown
const dropdownId = ref(`dropdown-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`);

// État du dropdown
const isOpen = ref(false);
const zIndex = ref(1000);

// Refs
const containerRef = ref(null);
const triggerRef = ref(null);
const contentRef = ref(null);

// Composable de positionnement
const positioning = usePositioning({
  placement: props.placement,
  offset: props.offset,
  autoPlacement: true,
  responsive: true,
});

// Connecter les refs au composable
watch(triggerRef, (newRef) => {
  positioning.triggerRef.value = newRef;
});

watch(contentRef, (newRef) => {
  positioning.contentRef.value = newRef;
});

// Composable de clic extérieur
const { enable: enableClickOutside, disable: disableClickOutside } = useClickOutside(
  () => close(),
  {
    enabled: true,
    escapeKey: true,
    excludeSelectors: [`[data-dropdown-id="${dropdownId.value}"]`],
    closeOnContentClick: props.closeOnContentClick,
  }
);

// Méthodes du dropdown
const open = () => {
  if (isOpen.value || props.disabled) return;
  isOpen.value = true;
  zIndex.value = 1000 + Date.now() % 1000; // Z-index unique
  enableClickOutside();
  updatePosition();
};

const close = () => {
  if (!isOpen.value) return;
  isOpen.value = false;
  disableClickOutside();
};

const toggle = () => {
  if (isOpen.value) {
    close();
  } else {
    open();
  }
};

// Computed
const contentClasses = computed(() =>
  mergeClasses(
    [
      'dropdown-content',
      'dropdown-content-' + props.variant,
      'dropdown-content-' + props.size,
      props.variant === 'glass' && 'border-glass-sm',
      // États
      isOpen.value && 'dropdown-content-open',
    ].filter(Boolean),
    getCustomUtilityClasses(props),
    props.class,
  )
);

// Styles du contenu avec positionnement
const contentStyle = computed(() => ({
  ...positioning.positionStyle.value,
  zIndex: zIndex.value,
}));

// Attributs du conteneur
const containerAttrs = computed(() => ({
  ...getCommonAttrs(props),
  'data-dropdown-id': dropdownId.value,
}));

// Gestion des événements du trigger
const handleTriggerClick = (event) => {
  if (props.disabled) return;
  
  if (props.hover) return; // Pas de clic si hover activé
  
  toggle();
};

const handleTriggerKeydown = (event) => {
  if (props.disabled) return;

  switch (event.key) {
    case 'Enter':
    case ' ':
      event.preventDefault();
      toggle();
      break;
    case 'Escape':
      close();
      break;
    case 'ArrowDown':
      event.preventDefault();
      open();
      focusContent();
      break;
  }
};

// Gestion des événements du contenu
const handleContentKeydown = (event) => {
  switch (event.key) {
    case 'Escape':
      close();
      focusTrigger();
      break;
  }
};

// Gestion des clics sur le contenu
const handleContentClick = (event) => {
  // Si closeOnContentClick est true, fermer le dropdown
  if (props.closeOnContentClick) {
    // Attendre un peu pour permettre aux événements de se propager
    setTimeout(() => {
      close();
    }, 100);
  }
  // Sinon, ne rien faire (le dropdown reste ouvert)
};

// Gestion du hover
const handleTriggerMouseEnter = () => {
  if (props.hover && !props.disabled) {
    open();
  }
};

const handleTriggerMouseLeave = () => {
  if (props.hover) {
    // Délai pour permettre la navigation vers le contenu
    setTimeout(() => {
      if (!contentRef.value?.matches(':hover')) {
        close();
      }
    }, 100);
  }
};

const handleContentMouseEnter = () => {
  if (props.hover) {
    // Garder ouvert si on survole le contenu
  }
};

const handleContentMouseLeave = () => {
  if (props.hover) {
    close();
  }
};

// Méthodes utilitaires
const updatePosition = async () => {
  if (isOpen.value) {
    await nextTick();
    positioning.calculatePosition();
  }
};

const focusTrigger = () => {
  triggerRef.value?.focus();
};

const focusContent = () => {
  contentRef.value?.focus();
};

// Lifecycle
onMounted(() => {
  // Écouter les changements de taille de fenêtre
  window.addEventListener('resize', updatePosition);
  window.addEventListener('scroll', updatePosition);
});

onUnmounted(() => {
  window.removeEventListener('resize', updatePosition);
  window.removeEventListener('scroll', updatePosition);
});
</script>

<template>
  <div 
    ref="containerRef" 
    :class="['dropdown-container']" 
    v-bind="containerAttrs"
  >
    <!-- Trigger -->
    <div
      ref="triggerRef"
      :class="['dropdown-trigger-container']"
      @mouseenter="handleTriggerMouseEnter"
      @mouseleave="handleTriggerMouseLeave"
    >
      <!-- Trigger simple avec Btn -->
      <Btn
        v-if="trigger"
        :variant="variant"
        :color="color"
        :size="size"
        :disabled="disabled"
        :aria-label="ariaLabel"
        :aria-expanded="isOpen"
        aria-haspopup="true"
        @click="handleTriggerClick"
        @keydown="handleTriggerKeydown"
      >
        {{ trigger }}
      </Btn>
      
      <!-- Trigger custom via slot -->
      <div
        v-else
        ref="triggerRef"
        :class="['dropdown-trigger-' + props.variant]"
        :aria-expanded="isOpen"
        aria-haspopup="true"
        tabindex="0"
        role="button"
        @click="handleTriggerClick"
        @keydown="handleTriggerKeydown"
      >
        <slot name="trigger" />
      </div>
    </div>

    <!-- Contenu avec Teleport -->
    <Teleport to="body">
      <div
        v-show="isOpen"
        ref="contentRef"
        :data-dropdown-id="dropdownId"
        :class="contentClasses"
        :style="contentStyle"
        tabindex="-1"
        @keydown="handleContentKeydown"
        @click="handleContentClick"
        @mouseenter="handleContentMouseEnter"
        @mouseleave="handleContentMouseLeave"
      >
        <slot name="content" />
      </div>
    </Teleport>
  </div>
</template>

<style scoped lang="scss">
.dropdown-container {
  position: relative;
  display: inline-block;
}

.dropdown-trigger-container {
  display: inline-block;
}

.dropdown-content {
    --color: var(--color-neutral-500);
    position: fixed;
    z-index: 1000;
    // Animation d'entrée
    transform: scale(0.95) translateY(-8px);
    opacity: 0;
    transition: 
        transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
        opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);

    @media (max-width: 768px) {
        left: 50% !important;
        transform: translateX(-50%) scale(0.95) translateY(-8px);
        max-width: calc(100vw - 32px);
        width: auto;
        
        &.dropdown-content-open {
        transform: translateX(-50%) scale(1) translateY(0);
        }
    }
// Taille
    &.dropdown-content-xs {
        padding: 0.25rem;
        font-size: 0.75rem;
    }

    &.dropdown-content-sm {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    
    &.dropdown-content-md {
        padding: 0.75rem;
        font-size: 1rem;
    }

    &.dropdown-content-lg {
        padding: 1rem;
        font-size: 1.125rem;
    }

    &.dropdown-content-xl {
        padding: 1.5rem;
        font-size: 1.25rem;
    }

    &.dropdown-content-2xl {
        padding: 2rem;
        font-size: 1.5rem;
    }

    &.dropdown-content-3xl {
        padding: 2.5rem;
        font-size: 1.75rem;
    }

    &.dropdown-content-4xl {
        padding: 3rem;
        font-size: 2rem;
    }
    
// Couleur
    &.dropdown-content-error{--color: var(--color-error-500);}
    &.dropdown-content-warning{--color: var(--color-warning-500);}
    &.dropdown-content-success{--color: var(--color-success-500);}
    &.dropdown-content-info{--color: var(--color-info-500);}
    &.dropdown-content-primary{--color: var(--color-primary-500);}
    &.dropdown-content-secondary{--color: var(--color-secondary-500);}
    &.dropdown-content-neutral{--color: var(--color-neutral-500);}
    &.dropdown-content-accent{--color: var(--color-accent-500);}
  
    &.dropdown-content-open {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
  
    &.dropdown-content-glass {
        backdrop-filter: blur(24px);
        background: linear-gradient(
                        45deg,
                        color-mix(in srgb, var(--color) 25%, transparent) 20%,
                        color-mix(in srgb, var(--color) 30%, transparent) 30%,
                        color-mix(in srgb, var(--color) 45%, transparent) 55%,
                        color-mix(in srgb, var(--color) 30%, transparent) 65%
                    );
        background-color: color-mix(in srgb, --color 30%, transparent);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    &.dropdown-content-ghost {
        background-color: transparent;
    }
    &.dropdown-content-outline {
        background-color: transparent;
        border: 1px solid color-mix(in srgb, var(--color) 20%, transparent);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    &.dropdown-content-dash {
        backdrop-filter: blur(4px);
        background-color: color-mix(in srgb, var(--color) 80%, transparent);
        border: 1px dashed color-mix(in srgb, var(--color) 20%, transparent);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    &.dropdown-content-soft {
        backdrop-filter: blur(4px);
        background-color: color-mix(in srgb, var(--color) 20%, transparent);
        border: 1px solid color-mix(in srgb, var(--color) 20%, transparent);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
}
</style>
