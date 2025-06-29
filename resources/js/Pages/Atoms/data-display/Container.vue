<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Container Atom (Tailwind)
 *
 * @description
 * Composant atomique Container conforme Atomic Design, basé sur Tailwind (pas DaisyUI).
 * - Utilise la classe Tailwind 'container' (et 'mx-auto' par défaut)
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Prop 'fluid' (bool) : désactive 'container' pour un conteneur full width
 * - Prop 'responsive' (string) : applique 'md:container', 'lg:container', etc.
 * - Prop 'color' : couleur de fond (classe Tailwind, ex: bg-base-100)
 * - Prop 'border' : bordure (classe Tailwind, ex: border-gray-200)
 * - Accessibilité : role, ariaLabel, id, tabindex
 * - Slot par défaut : contenu du container
 *
 * @note Ce composant n'utilise PAS DaisyUI (aucune classe DaisyUI), il est purement Tailwind/utilitaire.
 *
 * @example
 * <Container p="4" mx="auto" responsive="lg" shadow="md">Contenu</Container>
 * <Container fluid px="2" py="8">Contenu full width</Container>
 *
 * @props {Boolean} fluid - Désactive la classe 'container' (full width)
 * @props {String} responsive - Breakpoint responsive (ex: 'md', 'lg', ...), applique 'md:container', etc.
 * @props {String} color - Couleur de fond (classe Tailwind, ex: bg-base-100)
 * @props {String} border - Bordure (classe Tailwind, ex: border-gray-200)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu du container
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    fluid: { type: Boolean, default: false },
    responsive: { type: String, default: '' }, // ex: 'md', 'lg', etc.
    class: { type: String, default: '' },
    color: { type: String, default: '' },
    border: { type: String, default: '' },
});

const attrs = computed(() => getCommonAttrs(props));

const atomClasses = computed(() =>
    mergeClasses(
        [
            !props.fluid && (props.responsive ? `${props.responsive}:container` : 'container'),
            !props.fluid && 'mx-auto',
            props.color,
            props.border && `${props.border} border-1 border-solid`,
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped></style>
