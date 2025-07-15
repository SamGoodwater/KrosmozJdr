<script setup>
/**
 * Route Atom (Lien Inertia + DaisyUI Link)
 *
 * @description
 * Composant atomique pour créer des liens (Link) Vue/Inertia stylés DaisyUI Link, avec accessibilité et support complet des variantes DaisyUI.
 * - Slot par défaut : contenu du lien (texte, icône, etc.)
 * - Props : href, route, target, method, replace, color, hover, + commonProps (id, ariaLabel, role, tabindex, etc.)
 * - Si la prop route est fournie, href est calculé via la fonction route()
 * - Utilise le composant Link d'Inertia pour les routes internes
 * - Utilise des balises <a> natives pour les URLs externes
 * - Applique les classes DaisyUI Link explicitement (link, link-primary, etc.)
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/link/
 * @version DaisyUI v5.x
 *
 * @example
 * <!-- Route interne avec Inertia -->
 * <Route route="user.show" color="primary">Mon compte</Route>
 * <Route route="logout" method="post" color="error">Déconnexion</Route>
 * 
 * <!-- URL externe avec balise <a> native -->
 * <Route href="https://google.com" target="_blank" color="info" hover>Google</Route>
 * <Route href="mailto:contact@example.com" color="success">Nous contacter</Route>
 * 
 * <!-- URL interne avec href -->
 * <Route href="/dashboard" color="primary">Dashboard</Route>
 *
 * @props {String} href - URL du lien (défaut : #)
 * @props {String} route - Nom de la route Laravel (optionnel, prioritaire sur href)
 * @props {String} target - Cible du lien (_blank, _self, _parent, _top)
 * @props {String} method - Méthode HTTP pour le lien Inertia (get, post, put, delete, etc.), défaut 'get'
 * @props {Boolean} replace - Remplace l'historique (Inertia), défaut false
 * @props {String} color - Couleur DaisyUI Link ('', 'neutral', 'primary', 'secondary', 'accent', 'success', 'info', 'warning', 'error')
 * @props {Boolean} hover - Affiche le soulignement uniquement au survol (link-hover)
 * @props {String} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - Contenu du lien (texte, icône, etc.)
 *
 * @note Toutes les classes DaisyUI Link sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Le composant gère automatiquement le choix entre Link d'Inertia et balise <a> native selon le type d'URL.
 * @note Ce composant fusionne l'API DaisyUI Link et Inertia Link.
 */

import { computed, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
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
        validator: (v) => !v || colorList.includes(v),
    },
    hover: {
        type: Boolean,
        default: false,
    },
});

// Fonction utilitaire pour savoir si une URL est interne (même domaine)
function isInternalUrl(url) {
    try {
        const parsed = new URL(url, window.location.origin);
        return parsed.host === window.location.host;
    } catch {
        // Si ce n'est pas une URL absolue, on considère interne si ça commence par /
        return url.startsWith('/');
    }
}

// Utilitaire pour détecter mailto, tel, ancre
function isSpecialUrl(url) {
    return (
        url.startsWith('mailto:') ||
        url.startsWith('tel:') ||
        url.startsWith('#')
    );
}

// Détermine si on doit utiliser une balise <a> native ou le composant Link d'Inertia
const shouldUseNativeLink = computed(() => {
    const finalHref = hrefRef.value;
    return (
        !isInternalUrl(finalHref) ||
        finalHref.startsWith('mailto:') ||
        finalHref.startsWith('tel:') ||
        props.target === '_blank' ||
        finalHref.startsWith('#')
    );
});

// Calcule le href final
const hrefRef = computed(() => {
    if (props.route) {
        // Si c'est un mailto, tel ou ancre, on la retourne telle quelle
        if (isSpecialUrl(props.route)) {
            return props.route;
        }
        // Sinon, on essaie de la traiter comme une route nommée
        try {
            return route(props.route);
        } catch (e) {
            console.warn(
                `Route "${props.route}" not found, falling back to direct URL`,
            );
            // Fallback intelligent : si ça ressemble à une URL, on l'utilise
            if (props.route.startsWith('/')) {
                return props.route;
            }
            // Sinon, on utilise href comme fallback
            return props.href;
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

const linkComponent = computed(() => {
    return shouldUseNativeLink.value ? 'a' : Link;
});

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <component
        :is="linkComponent"
        :href="hrefRef"
        :target="target || undefined"
        v-bind="attrs"
        v-on="$attrs"
        :class="atomClasses"
    >
        <slot />
    </component>
</template>

<style scoped>
/* Ajoute ici des styles spécifiques si besoin */
</style>
