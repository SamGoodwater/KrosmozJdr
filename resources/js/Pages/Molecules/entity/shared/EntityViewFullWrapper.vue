<script setup>
/**
 * EntityViewFullWrapper — Wrapper pour les vues Full affichées en page
 *
 * @description
 * Enveloppe une fiche Full avec un bouton retour liste optionnel.
 *
 * @example
 * <EntityViewFullWrapper :show-back-button="true" back-route="entities.spells.index">
 *   <SpellViewFull :spell="spell" />
 * </EntityViewFullWrapper>
 *
 * @props {Boolean} showBackButton - Affiche le bouton Liste
 * @props {String} backRoute - Nom de route Ziggy vers l’index
 * @props {Object|Array} backRouteParams - Params Ziggy (optionnel)
 * @props {String} backHref - URL directe (optionnel)
 * @props {String} backLabel - Libellé (défaut : « Liste »)
 */
import EntityListBackButton from "@/Pages/Atoms/action/EntityListBackButton.vue";

defineProps({
    showBackButton: { type: Boolean, default: false },
    backRoute: { type: String, default: "" },
    backRouteParams: { type: [Object, Array], default: () => ({}) },
    backHref: { type: String, default: "" },
    backLabel: { type: String, default: "Liste" },
});
</script>

<template>
    <div class="space-y-4" v-bind="$attrs">
        <EntityListBackButton
            v-if="showBackButton && (backRoute || backHref)"
            :route-name="backRoute"
            :route-params="backRouteParams"
            :href="backHref"
            :label="backLabel"
        />

        <slot />
    </div>
</template>
