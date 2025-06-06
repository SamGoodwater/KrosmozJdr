<script setup>
/**
 * Container Atom (Tailwind/DaisyUI)
 *
 * @description
 * Composant atomique Container conforme Tailwind, DaisyUI et Atomic Design.
 * - Utilise la classe Tailwind 'container' (et 'mx-auto' par défaut)
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Prop 'fluid' (bool) : désactive 'container' pour un conteneur full width
 * - Prop 'responsive' (string) : applique 'md:container', 'lg:container', etc.
 * - Prop 'class' : classes custom supplémentaires
 * - Accessibilité : role, ariaLabel, id, tabindex
 * - Slot par défaut : contenu du container
 *
 * @example
 * <Container p="4" mx="auto" responsive="lg" shadow="md">Contenu</Container>
 * <Container fluid px="2" py="8">Contenu full width</Container>
 *
 * @props {Boolean} fluid - Désactive la classe 'container' (full width)
 * @props {String} responsive - Breakpoint responsive (ex: 'md', 'lg', ...), applique 'md:container', etc.
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu du container
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    fluid: { type: Boolean, default: false },
    responsive: { type: String, default: '' }, // ex: 'md', 'lg', etc.
    class: { type: String, default: '' },
    color: { type: String, default: 'bg-base-100' },
    border: { type: String, default: 'border-gray-200' },
});

const attrs = computed(() => getCommonAttrs(props));

function getContainerClasses(props) {
    const classes = ['container'];
    // Container
    if (!props.fluid) {
        if (props.responsive) {
            classes.push(`${props.responsive}:container`);
        } else {
            classes.push('container');
        }
        classes.push('mx-auto');
    }
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    // Classes custom
    if (props.class) classes.push(props.class);
    if (props.color) classes.push(props.color);
    if (props.border) classes.push(props.border, 'border-1 border-solid');
    return classes.join(' ');
}

const containerClasses = computed(() => getContainerClasses(props));
</script>

<template>
    <div :class="containerClasses" v-bind="attrs">
        <slot />
    </div>
</template>

<style scoped></style>
