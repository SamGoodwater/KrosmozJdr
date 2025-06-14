<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Route Atom (Lien Inertia + DaisyUI Link)
 *
 * @description
 * Composant atomique pour créer des liens (Link) Vue/Inertia stylés DaisyUI Link, avec accessibilité, gestion du tooltip et support complet des variantes DaisyUI.
 * - Slot par défaut : contenu du lien (texte, icône, etc.)
 * - Props : href, route, target, method, replace, color, hover, + commonProps (id, ariaLabel, role, tabindex, tooltip, etc.)
 * - Si la prop route est fournie, href est calculé via la fonction route()
 * - Utilise le composant Link d'Inertia
 * - Applique les classes DaisyUI Link explicitement (link, link-primary, etc.)
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @see https://daisyui.com/components/link/
 * @version DaisyUI v5.x
 *
 * @example
 * <Route href="/home">Accueil</Route>
 * <Route route="user.dashboard" color="primary">Mon compte</Route>
 * <Route href="https://google.com" target="_blank" color="info" hover>Google</Route>
 * <Route route="logout" method="post" color="error">Déconnexion</Route>
 *
 * @props {String} href - URL du lien (défaut : #)
 * @props {String} route - Nom de la route Laravel (optionnel, prioritaire sur href)
 * @props {String} target - Cible du lien (_blank, _self, _parent, _top)
 * @props {String} method - Méthode HTTP pour le lien Inertia (get, post, put, delete, etc.), défaut 'get'
 * @props {Boolean} replace - Remplace l'historique (Inertia), défaut false
 * @props {String} color - Couleur DaisyUI Link ('', 'neutral', 'primary', 'secondary', 'accent', 'success', 'info', 'warning', 'error')
 * @props {Boolean} hover - Affiche le soulignement uniquement au survol (link-hover)
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - hérités de commonProps
 * @slot default - Contenu du lien (texte, icône, etc.)
 *
 * @note Toutes les classes DaisyUI Link sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Le composant gère l'accessibilité, l'API Inertia et le tooltip intégré.
 * @note Ce composant fusionne l'API DaisyUI Link et Inertia Link.
 */

import { computed, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    getCommonProps,
    getCommonAttrs,
    mergeClasses,
    getCustomUtilityClasses,
} from "@/Utils/atomic-design/uiHelper";
import { colorList } from "@/Pages/Atoms/atomMap";
import { targetList, methodList } from "./actionMap";

const props = defineProps({
    ...getCommonProps(),
    href: {
        type: String,
        default: "#",
    },
    route: {
        type: String,
        default: "",
    },
    target: {
        type: String,
        default: "",
        validator: (v) => !v || targetList.includes(v),
    },
    method: {
        type: String,
        default: "get",
        validator: (v) => methodList.includes(v),
    },
    replace: {
        type: Boolean,
        default: false,
    },
    // DaisyUI Link props
    color: {
        type: String,
        default: "",
        validator: (v) => colorList.includes(v),
    },
    hover: {
        type: Boolean,
        default: false,
    },
});

// Vérifie si l'URL est une URL directe (mailto:, http://, etc.)
const isDirectUrl = (url) => {
    return (
        url.startsWith("mailto:") ||
        url.startsWith("http://") ||
        url.startsWith("https://") ||
        url.startsWith("tel:") ||
        url.startsWith("#") ||
        url.startsWith("/")
    );
};

// Calcule le href final
const hrefRef = computed(() => {
    if (props.route) {
        // Si c'est une URL directe, on la retourne telle quelle
        if (isDirectUrl(props.route)) {
            return props.route;
        }
        // Sinon, on essaie de la traiter comme une route nommée
        try {
            return route(props.route);
        } catch (e) {
            console.warn(
                `Route "${props.route}" not found, falling back to direct URL`,
            );
            return props.route;
        }
    }
    return props.href;
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            "link",
            props.color === "neutral" && "link-neutral",
            props.color === "primary" && "link-primary",
            props.color === "secondary" && "link-secondary",
            props.color === "accent" && "link-accent",
            props.color === "success" && "link-success",
            props.color === "info" && "link-info",
            props.color === "warning" && "link-warning",
            props.color === "error" && "link-error",
            props.hover && "link-hover",
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class,
    ),
);

const attrs = computed(() => getCommonAttrs(props));

// Gestion des méthodes HTTP
const handleClick = (e) => {
    // Si c'est une URL directe ou une méthode GET, on laisse le lien normal fonctionner
    if (isDirectUrl(hrefRef.value) || props.method === "get") {
        return;
    }

    e.preventDefault();
    router.visit(hrefRef.value, {
        method: props.method,
        replace: props.replace,
    });
};
</script>

<template>
    <Tooltip
        v-if="props.tooltip"
        :content="props.tooltip"
        :placement="props.tooltip_placement"
    >
        <Link
            :href="hrefRef"
            :target="target || undefined"
            :method="method"
            :replace="replace"
            :class="atomClasses"
            v-bind="attrs"
            v-on="$attrs"
            @click="handleClick"
        >
            <slot />
        </Link>
        <template v-if="typeof props.tooltip === 'object'" #content>
            <slot name="tooltip" />
        </template>
    </Tooltip>
    <Link
        v-else
        :href="hrefRef"
        :target="target || undefined"
        :method="method"
        :replace="replace"
        :class="atomClasses"
        v-bind="attrs"
        v-on="$attrs"
        @click="handleClick"
    >
        <slot />
    </Link>
</template>

<style scoped>
/* Ajoute ici des styles spécifiques si besoin */
</style>
