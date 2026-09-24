<script setup>
/**
 * EntityListBackButton — Bouton ghost xs « Liste » vers l’index d’une entité.
 *
 * @description
 * Lien retour compact (Inertia) vers une liste d’entités. Accepte un nom de
 * route Ziggy (+ params) ou un `href` direct.
 *
 * @example
 * <EntityListBackButton route-name="entities.spells.index" />
 * <EntityListBackButton
 *   route-name="admin.content.types.show"
 *   :route-params="{ kind: 'resource' }"
 * />
 * <EntityListBackButton href="/entities/items" />
 *
 * @props {String} routeName - Nom de route Laravel / Ziggy
 * @props {Object|Array|Number|String} routeParams - Paramètres Ziggy (optionnel)
 * @props {String} href - URL directe (utilisée si pas de routeName, ou fallback)
 * @props {String} label - Libellé du bouton (défaut : « Liste »)
 */
import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

const props = defineProps({
    routeName: { type: String, default: "" },
    routeParams: {
        type: [Object, Array, Number, String],
        default: () => ({}),
    },
    href: { type: String, default: "" },
    label: { type: String, default: "Liste" },
});

const resolvedHref = computed(() => {
    if (props.routeName) {
        try {
            return route(props.routeName, props.routeParams);
        } catch (e) {
            console.warn(
                `EntityListBackButton: route "${props.routeName}" introuvable`,
                e,
            );
            return props.href || "#";
        }
    }
    return props.href || "#";
});
</script>

<template>
    <Route v-if="resolvedHref && resolvedHref !== '#'" :href="resolvedHref">
        <Btn color="neutral" variant="ghost" size="xs" type="button" class="gap-1.5">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            {{ label }}
        </Btn>
    </Route>
</template>
