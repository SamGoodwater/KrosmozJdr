<script setup>
/**
 * ResourceIngredientsList — Liste d'ingrédients (ressources) avec icône + nom.
 *
 * @description
 * Vue texte : icône + nom × quantité, tooltip minimal au survol.
 * Pour équipements (items), consommables et ressources (recette).
 *
 * @props {Array} ingredients - Liste {id, name, image?, pivot?: {quantity}}}
 * @example
 * <ResourceIngredientsList :ingredients="item.resources" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityMinimalTooltip from "@/Pages/Molecules/entity/shared/EntityMinimalTooltip.vue";

const props = defineProps({
    ingredients: {
        type: Array,
        default: () => [],
    },
});

const normalizedIngredients = computed(() => {
    const raw = props.ingredients || [];
    return raw.map((ing) => ({
        id: ing.id ?? ing.resource_id,
        name: ing.name ?? "—",
        image: ing.image ?? null,
        quantity: ing.pivot?.quantity ?? ing.quantity ?? 1,
    }));
});

const showHref = (id) => (id ? route("entities.resources.show", { resource: id }) : null);
</script>

<template>
    <div v-if="normalizedIngredients.length > 0" class="flex flex-wrap items-center gap-2">
        <EntityMinimalTooltip
            v-for="ing in normalizedIngredients"
            :key="ing.id"
            entity-type="resources"
            :entity-id="ing.id"
        >
            <Route
                v-if="showHref(ing.id)"
                :href="showHref(ing.id)"
                color="neutral"
                class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
            >
                <div
                    class="w-4 h-4 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                >
                    <img
                        v-if="ing.image"
                        :src="ing.image"
                        :alt="ing.name"
                        class="h-full w-full object-contain"
                        loading="lazy"
                    />
                    <Icon v-else source="fa-solid fa-box" alt="" size="xs" class="text-base-content/50" />
                </div>
                <span class="truncate max-w-[8rem]">
                    {{ ing.name }}<template v-if="ing.quantity > 1">×{{ ing.quantity }}</template>
                </span>
            </Route>
            <span
                v-else
                class="inline-flex items-center gap-1.5 text-xs text-base-content/90"
            >
                <div
                    class="w-4 h-4 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                >
                    <img
                        v-if="ing.image"
                        :src="ing.image"
                        :alt="ing.name"
                        class="h-full w-full object-contain"
                        loading="lazy"
                    />
                    <Icon v-else source="fa-solid fa-box" alt="" size="xs" class="text-base-content/50" />
                </div>
                <span class="truncate max-w-[8rem]">
                    {{ ing.name }}<template v-if="ing.quantity > 1">×{{ ing.quantity }}</template>
                </span>
            </span>
        </EntityMinimalTooltip>
    </div>
</template>
