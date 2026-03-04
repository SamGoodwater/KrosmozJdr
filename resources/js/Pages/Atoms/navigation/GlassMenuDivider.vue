<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuDivider Atom (Navigation Glass)
 *
 * @description
 * Séparateur horizontal atomique pour menus glassmorphism.
 * - Contraste discret, cohérent avec les surfaces glass du design system
 * - Évite la duplication des séparateurs entre groupes d'items
 *
 * @example
 * <GlassMenuDivider />
 *
 * @props {Boolean} compact - Réduit les marges verticales
 */
import { computed } from "vue";
import { getCommonProps, getCommonAttrs, mergeClasses } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    compact: { type: Boolean, default: false },
});

const dividerClasses = computed(() =>
    mergeClasses(
        [
            "glass-menu-divider",
            props.compact && "glass-menu-divider-compact",
        ],
        props.class,
    ),
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <span :class="dividerClasses" v-bind="attrs" v-on="$attrs"></span>
</template>

<style scoped lang="scss">
.glass-menu-divider {
    display: block;
    width: 100%;
    height: 1px;
    margin: 0.15rem 0;
    border-bottom: 1px solid color-mix(in srgb, var(--color-base-content) 14%, transparent);
}

.glass-menu-divider-compact {
    margin: 0.05rem 0;
}
</style>
