<script setup>
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
 */

import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps(),
    href: {
        type: String,
        default: '#',
    },
    route: {
        type: String,
        default: '',
    },
    target: {
        type: String,
        default: '',
        validator: v => ['_blank', '_self', '_parent', '_top'].includes(v),
    },
    method: {
        type: String,
        default: 'get',
        validator: v => ['get', 'post', 'put', 'delete', 'patch', 'options', 'head', 'trace', 'connect', 'link', 'unlink'].includes(v),
    },
    replace: {
        type: Boolean,
        default: false,
    },
    // DaisyUI Link props
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'success', 'info', 'warning', 'error'].includes(v),
    },
    hover: {
        type: Boolean,
        default: false,
    },
});

// Calcule le href final
const hrefRef = computed(() => {
    if (props.route && typeof route === 'function') {
        return route(props.route);
    }
    return props.href;
});

function getAtomClasses(props) {
    // Classes DaisyUI Link explicites
    const classes = ['link'];
    if (props.color === 'neutral') classes.push('link-neutral');
    if (props.color === 'primary') classes.push('link-primary');
    if (props.color === 'secondary') classes.push('link-secondary');
    if (props.color === 'accent') classes.push('link-accent');
    if (props.color === 'success') classes.push('link-success');
    if (props.color === 'info') classes.push('link-info');
    if (props.color === 'warning') classes.push('link-warning');
    if (props.color === 'error') classes.push('link-error');
    if (props.hover) classes.push('link-hover');
    return classes.join(' ');
}
const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <Link :href="hrefRef" :target="target || undefined" :method="method" :replace="replace" :class="atomClasses"
            v-bind="attrs">
        <slot />
        </Link>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped>
/* Ajoute ici des styles spécifiques si besoin */
</style>
