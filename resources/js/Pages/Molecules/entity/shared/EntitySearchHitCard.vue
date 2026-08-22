<script setup>
/**
 * EntitySearchHitCard — Ligne résultat recherche (thumb + titre + sous-titre).
 *
 * @description
 * Visuel partagé entre recherche globale et panneau favoris. Densité `comfortable`
 * pour un format un peu plus grand. Au survol, si une fiche est hydratée, affiche
 * la vue Minimal en overlay.
 */
import { computed, markRaw, onMounted, ref, shallowRef, watch } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import { normalizeEntityThumbLabel } from "@/Utils/entity/entityThumb";
import { resolveEntityViewComponent } from "@/Utils/entity/resolveEntityViewComponent";
import {
    entityViewPropName,
    fetchEntityModelById,
    supportsEntityCatalogViews,
} from "@/Composables/entity/useEntityTableFetch";
import { OVERLAY_TRIGGER } from "@/Composables/overlay/overlayConstants";

const props = defineProps({
    result: { type: Object, required: true },
    density: {
        type: String,
        default: "default",
        validator: (v) => ["default", "comfortable"].includes(v),
    },
    entity: { type: Object, default: null },
    showFavoriteToggle: { type: Boolean, default: false },
    isFavorite: { type: Boolean, default: false },
    previewOnHover: { type: Boolean, default: true },
});

const emit = defineEmits(["open", "toggle-favorite"]);

const hydratedEntity = shallowRef(props.entity);
const minimalComponent = shallowRef(null);
const hydrateError = ref(false);
const hydrating = ref(false);

watch(
    () => props.entity,
    (value) => {
        if (value) hydratedEntity.value = value;
    },
);

const canPreview = computed(
    () => props.previewOnHover && supportsEntityCatalogViews(props.result?.entityType),
);

const thumbSize = computed(() => (props.density === "comfortable" ? "compact" : "search"));

const rowClass = computed(() =>
    props.density === "comfortable"
        ? "entity-search-hit entity-search-hit--comfortable flex w-full min-w-0 flex-1 items-start gap-3 px-3 py-3 text-left transition-colors hover:bg-base-200/70 rounded-box"
        : "entity-search-hit flex w-full min-w-0 flex-1 items-start gap-2.5 px-3 py-2.5 text-left transition-colors hover:bg-base-200/70",
);

const titleClass = computed(() =>
    props.density === "comfortable"
        ? "block text-base font-semibold leading-snug text-base-content"
        : "block text-sm font-semibold leading-snug text-base-content",
);

const subtitleClass = computed(() =>
    props.density === "comfortable"
        ? "mt-1 block text-sm leading-relaxed text-base-content/75 line-clamp-3"
        : "global-search-excerpt mt-1 block text-xs leading-relaxed text-base-content/75",
);

const overlayContent = computed(() => {
    if (!canPreview.value || !minimalComponent.value || !hydratedEntity.value) {
        return "";
    }
    const prop = entityViewPropName(props.result.entityType);
    return {
        component: markRaw(minimalComponent.value),
        props: {
            [prop]: hydratedEntity.value,
            showActions: false,
            displayMode: "extended",
        },
    };
});

async function ensureHydrated() {
    if (!canPreview.value || hydratedEntity.value || hydrating.value || hydrateError.value) {
        return;
    }
    hydrating.value = true;
    try {
        if (!minimalComponent.value) {
            minimalComponent.value = await resolveEntityViewComponent(
                props.result.entityType,
                "minimal",
            );
        }
        const model = await fetchEntityModelById(props.result.entityType, props.result.id);
        if (model) {
            hydratedEntity.value = model;
        } else {
            hydrateError.value = true;
        }
    } catch {
        hydrateError.value = true;
    } finally {
        hydrating.value = false;
    }
}

function onOpen() {
    emit("open", {
        result: props.result,
        entity: hydratedEntity.value,
    });
}

function onToggleFavorite(event) {
    event.stopPropagation();
    emit("toggle-favorite", props.result);
}

onMounted(() => {
    if (!canPreview.value) return;
    resolveEntityViewComponent(props.result.entityType, "minimal")
        .then((comp) => {
            minimalComponent.value = comp;
        })
        .catch(() => {});
});
</script>

<template>
    <div class="relative flex items-stretch gap-1">
        <OverlayTrigger
            v-if="canPreview"
            :content="overlayContent"
            :trigger="OVERLAY_TRIGGER.HOVER"
            placement="right-start"
            max-width="auto"
            :interactive="true"
            :close-on-outside="true"
            :close-on-escape="true"
            panel-class="max-w-[min(92vw,24rem)]"
            :chromeless="true"
            :focus-trap="false"
            class="min-w-0 flex-1"
        >
            <button
                type="button"
                :class="rowClass"
                @mouseenter="ensureHydrated"
                @focus="ensureHydrated"
                @click="onOpen"
            >
                <EntityThumb
                    :size="thumbSize"
                    :src="result.iconUrl || ''"
                    :label="normalizeEntityThumbLabel(result.title)"
                    :alt="result.title"
                    aria-hidden="true"
                />
                <span class="min-w-0 flex-1">
                    <span :class="titleClass">{{ result.title }}</span>
                    <span v-if="result.subtitle" :class="subtitleClass">{{ result.subtitle }}</span>
                </span>
            </button>
        </OverlayTrigger>

        <button
            v-else
            type="button"
            :class="rowClass"
            @click="onOpen"
        >
            <EntityThumb
                :size="thumbSize"
                :src="result.iconUrl || ''"
                :label="normalizeEntityThumbLabel(result.title)"
                :alt="result.title"
                aria-hidden="true"
            />
            <span class="min-w-0 flex-1">
                <span :class="titleClass">{{ result.title }}</span>
                <span v-if="result.subtitle" :class="subtitleClass">{{ result.subtitle }}</span>
            </span>
        </button>

        <button
            v-if="showFavoriteToggle"
            type="button"
            class="btn btn-ghost btn-square btn-sm shrink-0 self-center"
            :aria-label="isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'"
            @click="onToggleFavorite"
        >
            <Icon
                source="fa-heart"
                :pack="isFavorite ? 'solid' : 'regular'"
                size="sm"
                :class="isFavorite ? 'text-primary' : 'opacity-60'"
                alt=""
            />
        </button>
    </div>
</template>
