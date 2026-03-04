<script setup>
defineOptions({ inheritAttrs: false });

/**
 * GlassMenuItem Atom (Navigation Glass)
 *
 * @description
 * Item atomique pour menus glassmorphism, compatible lien Inertia ou bouton d'action.
 * - Affichage homogène icône + label
 * - États hover/focus/disabled harmonisés avec le design system
 * - Variante `danger` dédiée aux actions destructives/déconnexion
 *
 * @example
 * <GlassMenuItem icon="fa-user" route="user.show">Mon compte</GlassMenuItem>
 * <GlassMenuItem icon="fa-right-from-bracket" danger @click="logout">Se déconnecter</GlassMenuItem>
 *
 * @props {String} route - Nom de route Laravel/Inertia (optionnel)
 * @props {String} href - URL directe (optionnel)
 * @props {String} icon - Nom de l'icône FontAwesome (optionnel)
 * @props {String} iconAlt - Texte alternatif de l'icône
 * @props {String} iconPack - Pack FontAwesome (solid, regular, brands, duotone)
 * @props {Boolean} active - Met en avant l'item actif
 * @props {Boolean} compact - Réduit la hauteur et les paddings
 * @props {Boolean} danger - Variante visuelle danger
 * @props {Boolean} disabled - Désactive l'item
 * @props {String} type - Type HTML pour le mode bouton
 * @slot default - Label/contenu principal
 */
import { computed, onMounted, ref } from "vue";
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
    hover3d: { type: Boolean, default: false },
    danger: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    type: {
        type: String,
        default: "button",
        validator: (v) => ["button", "submit", "reset"].includes(v),
    },
});

const emit = defineEmits(["click"]);
const reduceMotion = ref(false);
const finePointer = ref(true);

const isLink = computed(() => Boolean(props.route || props.href));
const canAnimate3d = computed(() =>
    props.hover3d && !props.disabled && finePointer.value && !reduceMotion.value,
);

const itemClasses = computed(() =>
    mergeClasses(
        [
            "glass-menu-item",
            props.active && "glass-menu-item-active",
            props.compact && "glass-menu-item-compact",
            canAnimate3d.value && "glass-menu-item-3d",
            props.danger && "glass-menu-item-danger",
            props.disabled && "glass-menu-item-disabled",
        ],
        props.class,
    ),
);

const iconClasses = computed(() =>
    mergeClasses([
        "glass-menu-item-icon",
    ]),
);

const attrs = computed(() => getCommonAttrs(props));

function handleClick(event) {
    if (props.disabled) {
        event.preventDefault();
        return;
    }
    emit("click", event);
}

function handleMouseMove(event) {
    if (!canAnimate3d.value) return;
    const el = event.currentTarget;
    if (!(el instanceof HTMLElement)) return;

    const rect = el.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return;

    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    const px = (x / rect.width - 0.5) * 2;
    const py = (y / rect.height - 0.5) * 2;
    const rotateX = -py * 5;
    const rotateY = px * 6;

    el.style.setProperty("--menu-mx", `${x}px`);
    el.style.setProperty("--menu-my", `${y}px`);
    el.style.setProperty("--menu-rx", `${rotateX.toFixed(2)}deg`);
    el.style.setProperty("--menu-ry", `${rotateY.toFixed(2)}deg`);
    el.style.setProperty("--menu-light-opacity", "0.65");
}

function handleMouseLeave(event) {
    const el = event.currentTarget;
    if (!(el instanceof HTMLElement)) return;

    el.style.setProperty("--menu-rx", "0deg");
    el.style.setProperty("--menu-ry", "0deg");
    el.style.setProperty("--menu-light-opacity", "0");
}

onMounted(() => {
    if (typeof window === "undefined") return;
    reduceMotion.value = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    finePointer.value = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
});
</script>

<template>
    <Route
        v-if="isLink"
        :route="route"
        :href="href"
        :class="'glass-menu-link'"
        :aria-label="props.ariaLabel"
        :tabindex="props.tabindex"
        :role="props.role"
        :id="props.id"
        v-bind="attrs"
        @click="handleClick"
    >
        <span
            :class="itemClasses"
            @mousemove="handleMouseMove"
            @mouseleave="handleMouseLeave"
        >
            <Icon
                v-if="icon"
                :source="icon"
                :pack="iconPack"
                :alt="iconAlt || ''"
                size="sm"
                :class="iconClasses"
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
        @mousemove="handleMouseMove"
        @mouseleave="handleMouseLeave"
    >
        <Icon
            v-if="icon"
            :source="icon"
            :pack="iconPack"
            :alt="iconAlt || ''"
            size="sm"
            :class="iconClasses"
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
    gap: 0.55rem;
    width: 100%;
    min-height: 2.35rem;
    border-radius: var(--radius-field, 0.1rem);
    padding: 0.5rem 0.75rem;
    color: color-mix(in srgb, var(--color-base-content) 82%, transparent);
    background: color-mix(in srgb, var(--color-base-100) 22%, transparent);
    border: 1px solid transparent;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
    transition:
        transform 0.2s ease,
        background-color 0.2s ease,
        color 0.2s ease,
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.glass-menu-item-3d {
    transform-style: preserve-3d;
    transform:
        perspective(760px)
        rotateX(var(--menu-rx, 0deg))
        rotateY(var(--menu-ry, 0deg))
        translateX(var(--menu-shift-x, 0px));
    will-change: transform;
    transition:
        transform 0.22s ease,
        background-color 0.2s ease,
        color 0.2s ease,
        border-color 0.2s ease,
        box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

.glass-menu-item-3d::after {
    content: "";
    position: absolute;
    inset: -45%;
    pointer-events: none;
    background: radial-gradient(
        circle 100px at var(--menu-mx, 50%) var(--menu-my, 50%),
        color-mix(in srgb, var(--color-base-content) 16%, transparent) 0%,
        color-mix(in srgb, var(--color-primary-400) 12%, transparent) 40%,
        transparent 75%
    );
    opacity: var(--menu-light-opacity, 0);
    transition: opacity 0.22s ease;
}

.glass-menu-item-compact {
    min-height: 2rem;
    gap: 0.45rem;
    padding: 0.35rem 0.6rem;
}

.glass-menu-item:hover {
    --menu-shift-x: 3px;
    color: var(--color-base-content);
    background: color-mix(in srgb, var(--color-base-100) 34%, transparent);
    border-color: color-mix(in srgb, var(--color-base-content) 22%, transparent);
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.1),
        0 6px 16px rgba(0, 0, 0, 0.12);
}

.glass-menu-item-active {
    color: var(--color-base-content);
    background: color-mix(in srgb, var(--color-primary-500) 13%, var(--color-base-100));
    border-color: color-mix(in srgb, var(--color-primary-400) 32%, transparent);
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.12),
        0 6px 14px rgba(0, 0, 0, 0.1);
}

.glass-menu-item:focus-visible {
    outline: none;
    color: var(--color-base-content);
    border-color: color-mix(in srgb, var(--color-primary-400) 50%, transparent);
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.12),
        0 0 0 2px color-mix(in srgb, var(--color-primary-400) 25%, transparent);
}

.glass-menu-item-icon {
    flex-shrink: 0;
    opacity: 0.85;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.glass-menu-item:hover .glass-menu-item-icon,
.glass-menu-item:focus-visible .glass-menu-item-icon {
    opacity: 1;
    transform: translateX(1px);
}

.glass-menu-item-danger:hover,
.glass-menu-item-danger:focus-visible {
    color: color-mix(in srgb, var(--color-error-300) 88%, var(--color-base-content));
    border-color: color-mix(in srgb, var(--color-error-400) 36%, transparent);
    background: color-mix(in srgb, var(--color-error-500) 10%, transparent);
}

.glass-menu-item-disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

@media (prefers-reduced-motion: reduce) {
    .glass-menu-item-3d {
        transform: translateX(var(--menu-shift-x, 0px));
    }

    .glass-menu-item-3d::after {
        display: none;
    }
}
</style>
