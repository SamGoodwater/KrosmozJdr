<script setup>
/**
 * EntityLabel — Indicateur de type d'entité (icône sur fond polygon + label optionnel)
 *
 * @description
 * Fond en forme de polygon (clip-path) uniquement derrière l'icône, couleur de l'entité.
 * Label : couleur entité, font-semibold, uppercase.
 * - icon-only : icône seule avec fond polygon.
 * - icon-stack : label sous l'icône (icône plus grande, police plus petite).
 * - icon-inline : label à droite de l'icône (icône plus petite, police plus grande).
 *
 * @props {string} entity - Clé d'entité (obligatoire)
 * @props {string} variant - 'icon-only' | 'icon-stack' | 'icon-inline', défaut 'icon-only'
 * @props {string} size - 'xs' | 'sm' | 'md' | 'lg' | 'xl', défaut 'md'
 * @props {string} label - Override du label affiché (optionnel)
 * @slot default - Remplace le label en variantes stack/inline
 * Tooltip au survol : affiche le nom de l'entité.
 *
 * @example
 * <EntityLabel entity="npc" />
 * <EntityLabel entity="spell" variant="icon-stack" size="md" />
 * <EntityLabel entity="item" variant="icon-inline" size="sm" />
 */
import { computed, ref } from 'vue';
import { getCommonProps, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getEntityConfig, getEntityIconUrl } from '@/config/entities';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

/** Polygon personnalisé pour le fond de l'icône. */
const ICON_BG_POLYGON = 'polygon(91% 72%, 96% 61%, 100% 40%, 95% 25%, 100% 9%, 80% 3%, 54% 0, 1% 6%, 6% 16%, 0 30%, 13% 43%, 8% 59%, 1% 75%, 13% 85%, 5% 100%, 54% 95%, 75% 93%, 91% 100%, 100% 85%);';

/**
 * Mapping explicite entity → variable CSS pour le fond (teinte 200).
 */
const ENTITY_BG_VAR = {
  section: 'var(--color-section-800)',
  page: 'var(--color-page-800)',
  npc: 'var(--color-npc-800)',
  item: 'var(--color-item-800)',
  creature: 'var(--color-creature-800)',
  shop: 'var(--color-shop-800)',
  campaign: 'var(--color-campaign-800)',
  resource: 'var(--color-resource-800)',
  monster: 'var(--color-monster-800)',
  panoply: 'var(--color-panoply-800)',
  specialization: 'var(--color-specialization-800)',
  spell: 'var(--color-spell-800)',
  user: 'var(--color-user-800)',
  attribute: 'var(--color-attribute-800)',
  capitalize: 'var(--color-capitalize-800)',
  breed: 'var(--color-breed-800)',
  consumable: 'var(--color-consumable-800)',
  scenario: 'var(--color-scenario-800)',
  condition: 'var(--color-condition-800)',
};

/**
 * Mapping explicite entity → variable CSS pour la couleur du label (teinte 700 pour lisibilité).
 */
const ENTITY_TEXT_VAR = {
  section: 'var(--color-section-400)',
  page: 'var(--color-page-400)',
  npc: 'var(--color-npc-400)',
  item: 'var(--color-item-400)',
  creature: 'var(--color-creature-400)',
  shop: 'var(--color-shop-400)',
  campaign: 'var(--color-campaign-400)',
  resource: 'var(--color-resource-400)',
  monster: 'var(--color-monster-400)',
  panoply: 'var(--color-panoply-400)',
  specialization: 'var(--color-specialization-400)',
  spell: 'var(--color-spell-400)',
  user: 'var(--color-user-400)',
  attribute: 'var(--color-attribute-400)',
  capitalize: 'var(--color-capitalize-400)',
  breed: 'var(--color-breed-400)',
  consumable: 'var(--color-consumable-400)',
  scenario: 'var(--color-scenario-400)',
  condition: 'var(--color-condition-400)',
};

const props = defineProps({
  ...getCommonProps(),
  entity: { type: String, required: true },
  variant: {
    type: String,
    default: 'icon-only',
    validator: (v) => ['icon-only', 'icon-stack', 'icon-inline'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v),
  },
  label: { type: String, default: '' },
});

const iconError = ref(false);

const entityKey = computed(() => (props.entity || '').trim().toLowerCase());

const config = computed(() => getEntityConfig(props.entity));

const iconUrl = computed(() => {
  if (iconError.value) return '';
  return getEntityIconUrl(props.entity);
});

const displayLabel = computed(() => {
  if (props.label) return props.label;
  return config.value.label;
});

const bgVar = computed(() => ENTITY_BG_VAR[entityKey.value] ?? 'var(--color-neutral-400)');
const textVar = computed(() => ENTITY_TEXT_VAR[entityKey.value] ?? 'var(--color-neutral-700)');

/** Style inline pour le bloc icône (fond + clip-path) — pas de classe dynamique. */
const iconWrapStyle = computed(() => ({
  backgroundColor: bgVar.value,
  clipPath: ICON_BG_POLYGON,
}));

/**
 * Tailles selon variant :
 * - icon-only : une seule échelle (icône).
 * - icon-stack : icône plus grande, texte plus petit.
 * - icon-inline : icône plus petite, texte plus gros.
 */
const sizeClasses = computed(() => {
  const s = props.size;
  const v = props.variant;

  if (v === 'icon-stack') {
    const icon = {
      xs: 'entity-label-icon-stack-xs',
      sm: 'entity-label-icon-stack-sm',
      md: 'entity-label-icon-stack-md',
      lg: 'entity-label-icon-stack-lg',
      xl: 'entity-label-icon-stack-xl',
    }[s] ?? 'entity-label-icon-stack-md';
    const text = {
      xs: 'text-[10px]',
      sm: 'text-xs',
      md: 'text-xs',
      lg: 'text-sm',
      xl: 'text-sm',
    }[s] ?? 'text-xs';
    return { icon, text };
  }

  if (v === 'icon-inline') {
    const icon = {
      xs: 'entity-label-icon-inline-xs',
      sm: 'entity-label-icon-inline-sm',
      md: 'entity-label-icon-inline-md',
      lg: 'entity-label-icon-inline-lg',
      xl: 'entity-label-icon-inline-xl',
    }[s] ?? 'entity-label-icon-inline-md';
    const text = {
      xs: 'text-xs',
      sm: 'text-sm',
      md: 'text-base',
      lg: 'text-lg',
      xl: 'text-xl',
    }[s] ?? 'text-base';
    return { icon, text };
  }

  // icon-only
  const icon = {
    xs: 'entity-label-icon-only-xs',
    sm: 'entity-label-icon-only-sm',
    md: 'entity-label-icon-only-md',
    lg: 'entity-label-icon-only-lg',
    xl: 'entity-label-icon-only-xl',
  }[s] ?? 'entity-label-icon-only-md';
  const text = 'text-sm';
  return { icon, text };
});

const rootClasses = computed(() =>
  mergeClasses(
    'entity-label',
    'inline-flex',
    'items-center',
    props.variant === 'icon-stack' ? 'flex-col gap-0.5' : 'gap-2',
    'shrink-0',
    props.class
  )
);

const iconWrapClasses = computed(() =>
  mergeClasses(
    'entity-label-icon-wrap',
    'inline-flex',
    'items-center',
    'justify-center',
    'shrink-0',
    sizeClasses.value.icon
  )
);

</script>

<template>
  <Tooltip :content="displayLabel">
    <span :class="rootClasses" class="entity-label-root">
      <span
        :class="iconWrapClasses"
        :style="iconWrapStyle"
      >
      <img
        v-if="iconUrl && !iconError"
        :src="iconUrl"
        :alt="displayLabel"
        class="entity-label-img"
        @error="iconError = true"
      />
      <span
        v-else
        class="entity-label-fallback"
        aria-hidden="true"
      />
    </span>
    <template v-if="variant === 'icon-stack'">
      <span
        :class="[sizeClasses.text, 'entity-label-text']"
        class="font-semibold uppercase truncate max-w-[4.5rem] text-center"
        :style="{ color: textVar }"
      >
        <slot v-if="$slots.default" />
        <template v-else>{{ displayLabel }}</template>
      </span>
    </template>
    <template v-else-if="variant === 'icon-inline'">
      <span
        :class="[sizeClasses.text, 'entity-label-text']"
        class="font-semibold uppercase truncate max-w-[8rem]"
        :style="{ color: textVar }"
      >
        <slot v-if="$slots.default" />
        <template v-else>{{ displayLabel }}</template>
      </span>
    </template>
    </span>
  </Tooltip>
</template>

<style scoped>
/* Bloc icône : le polygon est appliqué ici */
.entity-label-icon-wrap {
  -webkit-clip-path: polygon(91% 72%, 96% 61%, 100% 40%, 95% 25%, 100% 9%, 80% 3%, 54% 0, 1% 6%, 6% 16%, 0 30%, 13% 43%, 8% 59%, 1% 75%, 13% 85%, 5% 100%, 54% 95%, 75% 93%, 91% 100%, 100% 85%);;
  clip-path: polygon(91% 72%, 96% 61%, 100% 40%, 95% 25%, 100% 9%, 80% 3%, 54% 0, 1% 6%, 6% 16%, 0 30%, 13% 43%, 8% 59%, 1% 75%, 13% 85%, 5% 100%, 54% 95%, 75% 93%, 91% 100%, 100% 85%);;
}

.entity-label-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  padding: 0.15em;
  box-sizing: border-box;
}

.entity-label-fallback {
  width: 60%;
  height: 60%;
  border-radius: 2px;
  background: rgba(255, 255, 255, 0.4);
}

/* icon-only */
.entity-label-icon-only-xs {
  width: 1rem;
  height: 1rem;
}
.entity-label-icon-only-sm {
  width: 1.25rem;
  height: 1.25rem;
}
.entity-label-icon-only-md {
  width: 1.5rem;
  height: 1.5rem;
}
.entity-label-icon-only-lg {
  width: 1.75rem;
  height: 1.75rem;
}
.entity-label-icon-only-xl {
  width: 2.25rem;
  height: 2.25rem;
}

/* icon-stack : icône plus grande */
.entity-label-icon-stack-xs {
  width: 1.25rem;
  height: 1.25rem;
}
.entity-label-icon-stack-sm {
  width: 1.5rem;
  height: 1.5rem;
}
.entity-label-icon-stack-md {
  width: 1.75rem;
  height: 1.75rem;
}
.entity-label-icon-stack-lg {
  width: 2rem;
  height: 2rem;
}
.entity-label-icon-stack-xl {
  width: 2.5rem;
  height: 2.5rem;
}

/* icon-inline : icône plus petite */
.entity-label-icon-inline-xs {
  width: 0.875rem;
  height: 0.875rem;
}
.entity-label-icon-inline-sm {
  width: 1rem;
  height: 1rem;
}
.entity-label-icon-inline-md {
  width: 1.125rem;
  height: 1.125rem;
}
.entity-label-icon-inline-lg {
  width: 1.25rem;
  height: 1.25rem;
}
.entity-label-icon-inline-xl {
  width: 1.5rem;
  height: 1.5rem;
  }
</style>
