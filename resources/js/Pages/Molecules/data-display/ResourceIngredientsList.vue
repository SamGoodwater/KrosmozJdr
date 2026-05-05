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
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import ResourceViewMinimal from "@/Pages/Molecules/entity/resource/ResourceViewMinimal.vue";

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

const buildOverlayContent = (ing) => ({
    key: `resource-ingredient:${ing.id}`,
    loader: async ({ signal }) => {
        const url = `${route("api.tables.resources")}?format=entities&filters[id]=${ing.id}&limit=1`;
        const response = await fetch(url, {
            credentials: "include",
            headers: { Accept: "application/json" },
            signal,
        });
        const json = await response.json();
        const entity = Array.isArray(json?.entities) ? json.entities[0] || null : null;
        if (!entity) {
            return `Ressource #${ing.id}`;
        }
        return {
            component: ResourceViewMinimal,
            props: {
                resource: entity,
                displayMode: "extended",
                showActions: false,
            },
        };
    },
});
</script>

<template>
    <div v-if="normalizedIngredients.length > 0" class="flex flex-wrap items-center gap-2">
        <OverlayTrigger
            v-for="ing in normalizedIngredients"
            :key="ing.id"
            :content="buildOverlayContent(ing)"
            trigger="hover"
            placement="top"
            max-width="md"
            :interactive="true"
            :lazy="true"
            :cache="{ key: `resource-ingredient:${ing.id}`, ttlMs: 600000, maxEntries: 300 }"
            panel-class="p-1"
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
                    <Image
                        v-if="ing.image"
                        :source="ing.image"
                        :alt="ing.name"
                        fit="contain"
                        class="h-full w-full"
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
                    <Image
                        v-if="ing.image"
                        :source="ing.image"
                        :alt="ing.name"
                        fit="contain"
                        class="h-full w-full"
                    />
                    <Icon v-else source="fa-solid fa-box" alt="" size="xs" class="text-base-content/50" />
                </div>
                <span class="truncate max-w-[8rem]">
                    {{ ing.name }}<template v-if="ing.quantity > 1">×{{ ing.quantity }}</template>
                </span>
            </span>
        </OverlayTrigger>
    </div>
</template>
