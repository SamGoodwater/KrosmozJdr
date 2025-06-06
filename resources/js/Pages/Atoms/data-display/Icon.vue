<script setup>
/**
 * Icon Atom (Atomic Design, DaisyUI)
 *
 * @description
 * Composant atomique pour afficher une icône/image simple, basé sur l'atom Image.
 * - Props : source (nom logique ou chemin), alt (texte alternatif), size (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl), disabled (hérité de commonProps)
 * - La taille contrôle la hauteur (height), la largeur est auto
 * - Si disabled=true, l'icône est affichée en noir et blanc (grayscale)
 * - Tooltip intégré via commonProps
 *
 * @example
 * <Icon source="icons/modules/pa" alt="PA" size="md" />
 * <Icon source="logos/logo" alt="Logo" size="lg" :disabled="true" />
 *
 * @props {String} source - Nom logique ou chemin de l'icône (obligatoire)
 * @props {String} alt - Texte alternatif (obligatoire)
 * @props {String} size - Taille prédéfinie (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl), défaut md
 * @props {Boolean} disabled - Affiche l'icône en noir et blanc si true
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 */
import { computed } from 'vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import { getCommonProps } from '@/Utils/atom/atomManager';

const sizeHeightMap = {
    xs: '0.75rem',
    sm: '1rem',
    md: '1.5rem',
    lg: '2rem',
    xl: '3rem',
    '2xl': '4rem',
    '3xl': '5rem',
    '4xl': '6rem',
    '5xl': '7rem',
    '6xl': '8rem',
};

const props = defineProps({
    ...getCommonProps(),
    source: { type: String, required: true },
    alt: { type: String, required: true },
    size: {
        type: String,
        default: 'md',
        validator: v => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].includes(v),
    },
});

const height = computed(() => sizeHeightMap[props.size] || sizeHeightMap.md);
const grayscale = computed(() => props.disabled ? { filter: 'grayscale(100%)' } : {});

</script>

<template>
    <Image :source="props.source" :alt="props.alt" :height="height" :size="''" :style="grayscale"
        :tooltip="props.tooltip" :tooltip_placement="props.tooltip_placement" :id="props.id"
        :ariaLabel="props.ariaLabel" :role="props.role" :tabindex="props.tabindex" :disabled="props.disabled"
        class="icon" />
</template>

<style scoped lang="scss">
.icon {
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: inline-block;
    width: auto;
}
</style>
