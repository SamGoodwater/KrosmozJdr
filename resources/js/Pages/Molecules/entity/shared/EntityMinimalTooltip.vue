<script setup>
/**
 * EntityMinimalTooltip — Hover card affichant la vue Minimal (style Wikipédia).
 *
 * @description
 * Au survol d'une référence d'entité (lien, nom), charge à la demande et affiche la carte Minimal.
 * - Lazy loading : fetch uniquement au survol (pas de requêtes au chargement).
 * - Cache en mémoire : évite les re-fetch pour la même entité.
 * - Interactif : le survol de la carte la garde ouverte (clic possible sur les liens).
 * - Positionnement robuste via Floating UI (flip, shift).
 * - Icône de chargement visible pendant le fetch.
 *
 * @props {string} entityType - Type d'entité (resources, items, consumables)
 * @props {number|string} entityId - ID de l'entité à afficher
 * @props {Object} [entity] - Entité complète (évite le fetch si fournie)
 * @props {Object} [tableMeta] - Meta du tableau (characteristics) pour les effets
 * @props {string} [placement] - Placement du popover (top, bottom, left, right)
 * @slot default - Élément déclencheur (lien, span, etc.)
 *
 * @example
 * <EntityMinimalTooltip entity-type="resources" :entity-id="42">
 *   <a :href="...">Bois</a>
 * </EntityMinimalTooltip>
 */
import { ref, computed, onUnmounted } from "vue";
import { useFloating, offset, flip, shift, autoUpdate } from "@floating-ui/vue";
import { useEntityHoverCard } from "@/Composables/entity/useEntityHoverCard";
import ResourceViewMinimal from "@/Pages/Molecules/entity/resource/ResourceViewMinimal.vue";
import ItemViewMinimal from "@/Pages/Molecules/entity/item/ItemViewMinimal.vue";
import ConsumableViewMinimal from "@/Pages/Molecules/entity/consumable/ConsumableViewMinimal.vue";

const props = defineProps({
    entityType: {
        type: String,
        required: true,
        validator: (v) => ["resources", "items", "consumables"].includes(v),
    },
    entityId: {
        type: [Number, String],
        default: null,
    },
    entity: {
        type: Object,
        default: null,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    placement: {
        type: String,
        default: "top",
        validator: (v) => ["top", "bottom", "left", "right"].includes(v),
    },
});

const triggerRef = ref(null);
const floatingRef = ref(null);
const open = ref(false);

const { entityData, fetchedMeta, loading, fetchEntity } = useEntityHoverCard({
    entityType: props.entityType,
    entityId: props.entityId,
    entity: props.entity,
});

const propNameByType = {
    resources: "resource",
    items: "item",
    consumables: "consumable",
};
const minimalComponent = computed(() => {
    switch (props.entityType) {
        case "resources":
            return ResourceViewMinimal;
        case "items":
            return ItemViewMinimal;
        case "consumables":
            return ConsumableViewMinimal;
        default:
            return null;
    }
});
const propName = computed(() => propNameByType[props.entityType] ?? "entity");

const { floatingStyles } = useFloating(triggerRef, floatingRef, {
    open,
    placement: props.placement,
    middleware: [offset(8), flip(), shift({ padding: 8 })],
    whileElementsMounted: autoUpdate,
});

// Délais : ouverture après 250 ms, fermeture après 120 ms (permet de bouger vers la carte)
const OPEN_DELAY_MS = 250;
const CLOSE_DELAY_MS = 120;

let openTimer = null;
let closeTimer = null;

function clearTimers() {
    if (openTimer) {
        clearTimeout(openTimer);
        openTimer = null;
    }
    if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
}

function scheduleOpen() {
    clearTimers();
    openTimer = setTimeout(() => {
        openTimer = null;
        open.value = true;
        fetchEntity();
    }, OPEN_DELAY_MS);
}

function scheduleClose() {
    clearTimers();
    closeTimer = setTimeout(() => {
        closeTimer = null;
        open.value = false;
    }, CLOSE_DELAY_MS);
}

function onTriggerEnter() {
    clearTimers();
    scheduleOpen();
}

function onTriggerLeave() {
    scheduleClose();
}

function onFloatingEnter() {
    clearTimers();
    open.value = true;
}

function onFloatingLeave() {
    scheduleClose();
}

onUnmounted(clearTimers);
</script>

<template>
    <div
        ref="triggerRef"
        class="inline"
        @mouseenter="onTriggerEnter"
        @mouseleave="onTriggerLeave"
    >
        <slot />

        <Teleport to="body">
            <div
                v-if="open"
                ref="floatingRef"
                role="tooltip"
                class="entity-minimal-tooltip z-[1100] overflow-hidden shadow-xl border border-base-300 bg-base-100 min-w-[260px] max-w-[320px] backdrop-blur-xl"
                :style="floatingStyles"
                @mouseenter="onFloatingEnter"
                @mouseleave="onFloatingLeave"
            >
                <div class="p-0">
                    <div
                        v-if="loading"
                        class="flex items-center justify-center gap-2 p-6 text-base-content/70 text-sm"
                    >
                        <span class="loading loading-spinner loading-sm" aria-hidden="true" />
                        <span>Chargement…</span>
                    </div>
                    <component
                        v-else-if="minimalComponent && entityData"
                        :is="minimalComponent"
                        :[propName]="entityData"
                        :table-meta="fetchedMeta || tableMeta"
                        display-mode="extended"
                        :show-actions="false"
                    />
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.entity-minimal-tooltip {
    border-radius: var(--rounded-box, 0.1rem);
}
</style>