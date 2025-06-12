<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

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
 * @note Cet atom n'utilise PAS DaisyUI (aucune classe DaisyUI), il est purement utilitaire et basé sur Image.
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
import { getCommonProps, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { size6XlList } from '@/Pages/Atoms/atomMap';
import { sizeHeightMap } from './data-displayMap';

const props = defineProps({
    ...getCommonProps(),
    source: { type: String, required: true },
    alt: { type: String, required: true },
    size: {
        type: String,
        default: 'md',
        validator: v => size6XlList.includes(v),
    },
    class: { type: String, default: '' },
});

const height = computed(() => sizeHeightMap[props.size] || sizeHeightMap.md);
const grayscale = computed(() => props.disabled ? { filter: 'grayscale(100%)' } : {});

const atomClasses = computed(() => {
    return mergeClasses(
        ['icon'],
        props.class
    );
});
</script>

<template>
    <Image :source="props.source" :alt="props.alt" :height="height" :size="''" v-on="$attrs" :style="grayscale"
        :class="atomClasses" :tooltip="props.tooltip" :tooltip_placement="props.tooltip_placement" :id="props.id"
        :ariaLabel="props.ariaLabel" :role="props.role" :tabindex="props.tabindex" :disabled="props.disabled" />
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
