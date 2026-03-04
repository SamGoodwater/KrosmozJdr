<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuPanel Atom (Navigation Glass Container)
 *
 * @description
 * Conteneur atomique pour les menus glassmorphism de navigation.
 * - Gère l'espacement, les coins arrondis et la coupure des débordements
 * - Sert de base commune pour les menus compte et navigation principale
 * - Conçu pour accueillir des `GlassMenuItem`, `GlassMenuSectionTitle` et `GlassMenuDivider`
 *
 * @example
 * <GlassMenuPanel>
 *   <GlassMenuSectionTitle>Administration</GlassMenuSectionTitle>
 *   <GlassMenuItem icon="fa-users" route="user.index">Utilisateurs</GlassMenuItem>
 * </GlassMenuPanel>
 *
 * @props {Boolean} compact - Réduit les espacements internes
 * @slot default - Contenu du menu
 */
import { computed } from "vue";
import { getCommonProps, getCommonAttrs, mergeClasses } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    compact: { type: Boolean, default: false },
});

const panelClasses = computed(() =>
    mergeClasses(
        [
            "glass-menu-panel",
            props.compact && "glass-menu-panel-compact",
        ],
        props.class,
    ),
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="panelClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped lang="scss">
.glass-menu-panel {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 0.25rem;
    border-radius: var(--radius-box, 1rem);
    overflow: hidden;
}

.glass-menu-panel-compact {
    gap: 0.2rem;
    padding: 0.15rem;
}
</style>
