<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuSectionTitle Atom (Navigation Glass)
 *
 * @description
 * Titre de section atomique pour les menus glassmorphism.
 * - Uniformise les entêtes de groupes (Administration, Gestion, etc.)
 * - Léger et lisible, avec tracking et casse uppercase
 *
 * @example
 * <GlassMenuSectionTitle>Administration</GlassMenuSectionTitle>
 *
 * @props {Boolean} compact - Réduit la hauteur du titre
 * @slot default - Texte du titre
 */
import { computed } from "vue";
import { getCommonProps, getCommonAttrs, mergeClasses } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    compact: { type: Boolean, default: false },
});

const titleClasses = computed(() =>
    mergeClasses(
        [
            "glass-menu-section-title",
            props.compact && "glass-menu-section-title-compact",
        ],
        props.class,
    ),
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <p :class="titleClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </p>
</template>

<style scoped lang="scss">
.glass-menu-section-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: color-mix(in srgb, var(--color-base-content) 55%, transparent);
    text-align: center;
    padding: 0.25rem 0.5rem;
}

.glass-menu-section-title-compact {
    font-size: 0.65rem;
    padding: 0.15rem 0.45rem;
}
</style>
