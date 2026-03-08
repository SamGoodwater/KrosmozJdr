<script setup>
/**
 * PropertyDisplay — affichage standardisé d'une propriété/caractéristique.
 *
 * @description
 * Utilise les métadonnées (icône, couleur, description) des caractéristiques BDD
 * ou des descriptors. Supporte icônes personnalisées (icons/caracteristics/) et FontAwesome.
 *
 * @props {Object} property - { icon, label, tooltip, color, value }
 * @props {String} variant - 'badge' | 'icon' | 'inline'
 * @props {String} size - 'xs' | 'sm' | 'md'
 *
 * @example
 * <PropertyDisplay :property="fieldUi" :value="cell.value" variant="badge" />
 * <PropertyDisplay :property="fieldUi" :value="cell.value" variant="icon" />
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const props = defineProps({
  property: {
    type: Object,
    default: () => ({}),
  },
  value: {
    type: [String, Number, Boolean],
    default: null,
  },
  variant: {
    type: String,
    default: 'inline',
    validator: (v) => ['badge', 'icon', 'inline'].includes(v),
  },
  size: {
    type: String,
    default: 'sm',
    validator: (v) => ['xs', 'sm', 'md'].includes(v),
  },
});

const displayValue = computed(() => {
  const v = props.value;
  if (v === null || v === undefined || v === '') return '—';
  if (typeof v === 'boolean') return v ? 'Oui' : 'Non';
  return String(v);
});

const tooltipContent = computed(() => {
  const t = props.property?.tooltip || props.property?.label || '';
  if (!t) return displayValue.value;
  return `${t}\n${displayValue.value}`.trim();
});

const effectiveColor = computed(() => {
  const c = props.property?.color;
  if (!c || typeof c !== 'string') return 'neutral';
  return c.trim();
});

const iconColorStyle = computed(() => {
  const c = props.property?.color;
  if (!c || typeof c !== 'string') return undefined;
  const t = c.trim();
  if (t.startsWith('#')) return { color: t };
  if (t.includes('-')) return { color: `var(--color-${t})` };
  return undefined;
});

const badgeSize = computed(() => {
  const map = { xs: 'xs', sm: 'sm', md: 'md' };
  return map[props.size] ?? 'sm';
});

const iconSize = computed(() => {
  const map = { xs: 'xs', sm: 'xs', md: 'sm' };
  return map[props.size] ?? 'xs';
});
</script>

<template>
  <Tooltip :content="tooltipContent" placement="top" class="inline-flex">
    <!-- Badge : fond coloré avec icône + valeur -->
    <Badge
      v-if="variant === 'badge'"
      :color="effectiveColor"
      :size="badgeSize"
      variant="soft"
      class="inline-flex items-center gap-1"
    >
      <Icon
        v-if="property?.icon"
        :source="property.icon"
        :alt="property.label || ''"
        :size="iconSize"
        class="shrink-0 opacity-90"
      />
      <span>{{ displayValue }}</span>
    </Badge>

    <!-- Icon : uniquement l'icône -->
    <span v-else-if="variant === 'icon'" class="inline-flex items-center">
      <Icon
        v-if="property?.icon"
        :source="property.icon"
        :alt="property.label || tooltipContent"
        :size="iconSize"
        :style="iconColorStyle"
        class="shrink-0"
      />
      <span v-else class="text-base-content/40">—</span>
    </span>

    <!-- Inline : icône + texte --> 
    <span v-else class="inline-flex items-center gap-1 text-xs">
      <Icon
        v-if="property?.icon"
        :source="property.icon"
        :alt="property.label || ''"
        :size="iconSize"
        class="shrink-0 opacity-80"
        :style="iconColorStyle"
      />
      <span :style="iconColorStyle">
        {{ displayValue }}
      </span>
    </span>
  </Tooltip>
</template>
