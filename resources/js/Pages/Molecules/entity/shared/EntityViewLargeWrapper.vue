<script setup>
/**
 * EntityViewLargeWrapper — Wrapper pour les vues Large affichées en page
 *
 * @description
 * Ajoute des options communes (bouton retour, etc.) quand une vue Large
 * est affichée sur une page entière plutôt que dans une modale.
 *
 * @props {boolean} showBackButton - Afficher le bouton « Retour à la liste »
 * @props {string} [backRoute] - Nom de la route Laravel pour le retour (ex: entities.resources.index)
 * @props {string} [backLabel] - Libellé du bouton (défaut: « Retour à la liste »)
 *
 * @slot default - Contenu (ex: ResourceViewLarge)
 *
 * @example
 * <EntityViewLargeWrapper
 *   :show-back-button="true"
 *   back-route="entities.resources.index"
 * >
 *   <ResourceViewLarge :resource="resource" :show-actions="true" />
 * </EntityViewLargeWrapper>
 */
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

const props = defineProps({
    showBackButton: { type: Boolean, default: false },
    backRoute: { type: String, default: "" },
    backLabel: { type: String, default: "Retour à la liste" },
});
</script>

<template>
    <div class="space-y-4">
        <Route
            v-if="showBackButton && backRoute"
            :route="backRoute"
        >
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                {{ backLabel }}
            </Btn>
        </Route>

        <slot />
    </div>
</template>
