<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuItem Atom (Navigation Glass)
 *
 * @description
 * Item atomique pour menus glassmorphism, compatible lien Inertia ou bouton.
 * Utilise les classes glass (border-glass, bd-glass, box-shadow-glass) et --color.
 * Style discret, dense, moderne. Animation légère au survol.
 *
 * @example
 * <GlassMenuItem icon="fa-user" href="/compte">Mon compte</GlassMenuItem>
 * <GlassMenuItem danger @click="logout">Se déconnecter</GlassMenuItem>
 *
 * @props {String} href - URL (optionnel)
 * @props {String} icon - Icône FontAwesome (optionnel)
 * @props {Boolean} active - Item actif
 * @props {Boolean} compact - Réduit hauteur et padding
 * @props {Boolean} danger - Variante danger
 * @props {Boolean} disabled - Désactivé
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import { getCommonProps, getCommonAttrs, mergeClasses } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    route: { type: String, default: "" },
    href: { type: String, default: "" },
    icon: { type: String, default: "" },
    iconAlt: { type: String, default: "" },
    iconPack: {
        type: String,
        default: "solid",
        validator: (v) => ["solid", "regular", "brands", "duotone"].includes(v),
    },
    active: { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
    danger: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    type: {
        type: String,
        default: "button",
        validator: (v) => ["button", "submit", "reset"].includes(v),
    },
});

const emit = defineEmits(["click"]);

const isLink = computed(() => Boolean(props.route || props.href));

const itemClasses = computed(() =>
    mergeClasses(
        [
            "glass-menu-item",
            "hover:box-glass-xs-b",
            props.active && "glass-menu-item-active",
            props.compact && "glass-menu-item-compact",
            props.danger && "glass-menu-item-danger",
            props.disabled && "glass-menu-item-disabled",
        ],
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));

function handleClick(event) {
    if (props.disabled) {
        event.preventDefault();
        return;
    }
    emit("click", event);
}
</script>

<template>
    <Route
        v-if="isLink"
        :route="route"
        :href="href"
        class="glass-menu-link"
        :aria-label="props.ariaLabel"
        :tabindex="props.tabindex"
        :role="props.role"
        :id="props.id"
        v-bind="attrs"
        @click="handleClick"
    >
        <span :class="itemClasses">
            <Icon
                v-if="icon"
                :source="icon"
                :pack="iconPack"
                :alt="iconAlt || ''"
                size="sm"
                class="glass-menu-item-icon"
            />
            <slot />
        </span>
    </Route>

    <button
        v-else
        :type="type"
        :class="itemClasses"
        :disabled="disabled"
        v-bind="attrs"
        v-on="$attrs"
        @click="handleClick"
    >
        <Icon
            v-if="icon"
            :source="icon"
            :pack="iconPack"
            :alt="iconAlt || ''"
            size="sm"
            class="glass-menu-item-icon"
        />
        <slot />
    </button>
</template>

<style scoped lang="scss">
.glass-menu-link {
    display: block;
    width: 100%;
    text-decoration: none;
}

.glass-menu-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    min-height: 2.25rem;
    padding: 0.4rem 0.65rem;
    border: none;
    overflow: hidden;
    transition:
        transform 0.18s ease,
        background 0.18s ease,
        color 0.18s ease,
        box-shadow 0.18s ease;
}

.glass-menu-item:hover:not(.glass-menu-item-disabled) {
    background: color-mix(in srgb, var(--color-base-100) 32%, transparent);
    color: var(--color-base-content);
    transform: translateX(2px);
}

.glass-menu-item-active {
    background: color-mix(in srgb, var(--color) 12%, color-mix(in srgb, var(--color-base-100) 28%, transparent));
    color: var(--color-base-content);
}

.glass-menu-item:focus-visible {
    outline: none;
}

.glass-menu-item-compact {
    min-height: 1.85rem;
    gap: 0.4rem;
    padding: 0.28rem 0.5rem;
}

.glass-menu-item-icon {
    flex-shrink: 0;
    opacity: 0.82;
    transition: opacity 0.18s ease;
}

.glass-menu-item:hover .glass-menu-item-icon,
.glass-menu-item:focus-visible .glass-menu-item-icon {
    opacity: 1;
}

.glass-menu-item-danger:hover:not(.glass-menu-item-disabled),
.glass-menu-item-danger:focus-visible {
    --color: var(--color-error-500);
}

.glass-menu-item-disabled {
    opacity: 0.55;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
