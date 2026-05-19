<script setup>
/**
 * EntityViewFullWrapper — Wrapper pour les vues Full affichées en page
 */
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

defineProps({
    showBackButton: { type: Boolean, default: false },
    backRoute: { type: String, default: "" },
    backLabel: { type: String, default: "Retour à la liste" },
});
</script>

<template>
    <div class="space-y-4" v-bind="$attrs">
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
