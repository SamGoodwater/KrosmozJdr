<script setup>
/**
 * ElementDisplay Atom
 *
 * @description
 * Affiche un élément (Spell, Capability) avec badge dégradé et icône.
 * Utilise les icônes de storage/app/public/images/icons/caracteristics/.
 *
 * @props {Number} element - Masque 7 bits (1–127) ou ancien code 0–29 (normalisé côté util).
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
  getElementIconForValue,
  normalizeElementStorageValue,
  getElementBadgeStyle,
} from '@/Utils/Entity/Elements.js';

const props = defineProps({
  element: {
    type: [Number, String],
    default: undefined,
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

const isUnset = computed(() => {
  const v = props.element;
  return v === null || v === undefined || v === '';
});

const maskValue = computed(() => {
  if (isUnset.value) return null;
  const m = normalizeElementStorageValue(props.element);
  return m === 0 ? null : m;
});

const label = computed(() => (maskValue.value === null ? '' : getElementLabel(maskValue.value) ?? ''));

const iconSource = computed(() => {
  if (maskValue.value === null) return getElementIconForValue(1);
  return getElementIconForValue(maskValue.value);
});

const badgeStyle = computed(() => (maskValue.value === null ? {} : getElementBadgeStyle(maskValue.value)));

const badgeClass = computed(() =>
  maskValue.value === null ? 'element-badge element-badge--empty' : 'element-badge element-badge--dynamic',
);

const sizeClass = computed(() => {
  const map = { xs: 'badge-xs', sm: 'badge-sm', md: 'badge-md', lg: 'badge-lg', xl: 'badge-xl' };
  return map[props.size] ?? 'badge-sm';
});
</script>

<template>
  <span v-if="isUnset" class="text-sm text-base-content/55">—</span>
  <span
    v-else
    :class="[
      badgeClass,
      sizeClass,
      'badge inline-flex items-center gap-1',
      variant === 'inline' && 'badge-ghost px-0',
    ]"
    :style="badgeStyle"
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
