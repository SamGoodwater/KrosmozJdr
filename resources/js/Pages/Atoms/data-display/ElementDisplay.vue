<script setup>
/**
 * ElementDisplay Atom
 *
 * @description
 * Affiche un élément (Spell, Capability) avec badge dégradé et icône.
 * Utilise les icônes de storage/app/public/images/icons/caracteristics/.
 *
 * @props {Number} element - Valeur élément 0-29
 * @props {String} size - Taille du badge (xs, sm, md, lg, xl)
 * @props {Boolean} showIcon - Afficher l'icône (défaut: true)
 * @props {Boolean} showLabel - Afficher le libellé (défaut: true)
 * @props {String} variant - Style (badge, chip, inline)
 *
 * @example
 * <ElementDisplay :element="2" />
 * <ElementDisplay :element="9" size="sm" />
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import {
  getElementLabel,
  getElementPrimaries,
  ELEMENT_PRIMARY_ICONS,
} from '@/Utils/Entity/Elements.js';

const props = defineProps({
  element: {
    type: [Number, String],
    default: 0,
  },
  size: {
    type: String,
    default: 'sm',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v),
  },
  showIcon: {
    type: Boolean,
    default: true,
  },
  showLabel: {
    type: Boolean,
    default: true,
  },
  variant: {
    type: String,
    default: 'badge',
    validator: (v) => ['badge', 'chip', 'inline'].includes(v),
  },
});

const numValue = computed(() => {
  const v = props.element;
  if (v === null || v === undefined) return 0;
  const n = typeof v === 'string' ? parseInt(v, 10) : Number(v);
  return Number.isFinite(n) && n >= 0 && n <= 29 ? n : 0;
});

const label = computed(() => getElementLabel(numValue.value) ?? 'Neutre');

const iconSource = computed(() => {
  const primaries = getElementPrimaries(numValue.value);
  const first = primaries[0] ?? 0;
  return ELEMENT_PRIMARY_ICONS[first] ?? ELEMENT_PRIMARY_ICONS[0];
});

const badgeClass = computed(() => `element-badge element-badge--${numValue.value}`);

const sizeClass = computed(() => {
  const map = { xs: 'badge-xs', sm: 'badge-sm', md: 'badge-md', lg: 'badge-lg', xl: 'badge-xl' };
  return map[props.size] ?? 'badge-sm';
});
</script>

<template>
  <span
    :class="[
      badgeClass,
      sizeClass,
      'badge inline-flex items-center gap-1',
      variant === 'inline' && 'badge-ghost px-0',
    ]"
  >
    <Icon
      v-if="showIcon"
      :source="iconSource"
      :alt="label"
      :size="size === 'xs' ? 'xs' : size === 'sm' ? 'xs' : 'sm'"
      class="shrink-0"
    />
    <span v-if="showLabel" class="truncate">{{ label }}</span>
  </span>
</template>
