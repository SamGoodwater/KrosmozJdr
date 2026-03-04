<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuGroup Atom (Navigation Glass Disclosure)
 *
 * @description
 * Groupe repliable atomique pour menus glassmorphism.
 * - Encapsule un bloc `<details>/<summary>` cohérent avec `GlassMenuItem`
 * - Gère les états fermé/ouvert, y compris une variante compacte
 * - Utilise les variables DaisyUI pour les rayons
 *
 * @example
 * <GlassMenuGroup :open="true" icon="fa-database" icon-alt="Entités">
 *   <template #title>Entités</template>
 *   <GlassMenuItem compact>Objets</GlassMenuItem>
 * </GlassMenuGroup>
 *
 * @props {Boolean} open - Ouvre le groupe par défaut
 * @props {Boolean} compact - Réduit les espacements
 * @props {String} icon - Icône FontAwesome (optionnel)
 * @props {String} iconAlt - Texte alternatif de l'icône
 * @props {String} iconPack - Pack FontAwesome (solid, regular, brands, duotone)
 * @slot title - Titre du summary
 * @slot default - Contenu repliable
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { getCommonProps, getCommonAttrs, mergeClasses } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    open: { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
    icon: { type: String, default: "" },
    iconAlt: { type: String, default: "" },
    iconPack: {
        type: String,
        default: "solid",
        validator: (v) => ["solid", "regular", "brands", "duotone"].includes(v),
    },
});

const groupClasses = computed(() =>
    mergeClasses(
        [
            "glass-menu-group",
            props.compact && "glass-menu-group-compact",
        ],
        props.class,
    ),
);

const summaryClasses = computed(() =>
    mergeClasses([
        "glass-menu-group-summary",
        props.compact && "glass-menu-group-summary-compact",
    ]),
);

const contentClasses = computed(() =>
    mergeClasses([
        "glass-menu-group-content",
        props.compact && "glass-menu-group-content-compact",
    ]),
);

const iconClasses = computed(() =>
    mergeClasses([
        "glass-menu-group-summary-icon",
    ]),
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <details :open="open" :class="groupClasses" v-bind="attrs" v-on="$attrs">
        <summary :class="summaryClasses">
            <Icon
                v-if="icon"
                :source="icon"
                :pack="iconPack"
                :alt="iconAlt || ''"
                size="sm"
                :class="iconClasses"
            />
            <slot name="title" />
        </summary>
        <div :class="contentClasses">
            <slot />
        </div>
    </details>
</template>

<style scoped lang="scss">
.glass-menu-group {
    border-radius: var(--radius-field, 0.75rem);
}

.glass-menu-group-summary {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 0.55rem;
    min-height: 2.35rem;
    border-radius: var(--radius-field, 0.1rem);
    padding: 0.5rem 0.75rem;
    color: color-mix(in srgb, var(--color-base-content) 82%, transparent);
    background: color-mix(in srgb, var(--color-base-100) 20%, transparent);
    border: 1px solid transparent;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.glass-menu-group-summary-compact {
    min-height: 2rem;
    gap: 0.45rem;
    padding: 0.35rem 0.6rem;
}

.glass-menu-group-summary::-webkit-details-marker {
    display: none;
}

.glass-menu-group-summary:hover {
    color: var(--color-base-content);
    background: color-mix(in srgb, var(--color-base-100) 30%, transparent);
    border-color: color-mix(in srgb, var(--color-base-content) 20%, transparent);
}

.glass-menu-group[open] > .glass-menu-group-summary {
    color: var(--color-base-content);
    background: color-mix(in srgb, var(--color-primary-500) 12%, var(--color-base-100));
    border-color: color-mix(in srgb, var(--color-primary-400) 28%, transparent);
}

.glass-menu-group-summary-icon {
    opacity: 0.9;
}

.glass-menu-group-content {
    padding-left: 1rem;
    padding-top: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.glass-menu-group-content-compact {
    padding-left: 0.85rem;
    padding-top: 0.35rem;
    gap: 0.35rem;
}
</style>
