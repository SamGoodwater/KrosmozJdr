<script setup>
/**
 * GlobalSearchHitRow — Ligne de résultat recherche (style vue texte agrandie).
 *
 * @description
 * Vignette + titre + sous-titre. Au survol : aperçu Minimal (hydraté à la demande).
 * Au clic : émet `select` (le parent ouvre la modal full).
 */
import { computed, markRaw, ref } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { normalizeEntityThumbLabel } from "@/Utils/entity/entityThumb";
import {
    canOpenEntityModal,
    entityPropNameForType,
    fetchEntityModel,
} from "@/Utils/entity/fetchEntityModel";
import { resolveEntityViewComponentSync } from "@/Utils/entity/resolveEntityViewComponent";
import { normalizeEntityType } from "@/Entities/entity-registry";

const props = defineProps({
    hit: { type: Object, required: true },
    /** `default` = recherche header ; `lg` = panneau favoris. */
    density: {
        type: String,
        default: "lg",
        validator: (v) => ["default", "lg"].includes(v),
    },
    showFavoriteToggle: { type: Boolean, default: false },
    isFavorite: { type: Boolean, default: false },
});

const emit = defineEmits(["select", "toggle-favorite"]);

const thumbSize = computed(() => (props.density === "lg" ? "compact" : "search"));
const titleClass = computed(() =>
    props.density === "lg"
        ? "block text-base font-semibold leading-snug text-base-content"
        : "block text-sm font-semibold leading-snug text-base-content",
);

const canPreview = computed(() => canOpenEntityModal(props.hit?.entityType));
const previewLoading = ref(false);

const overlayContent = computed(() => {
    if (!canPreview.value) return null;
    const hit = props.hit;
    const type = normalizeEntityType(hit.entityType);
    return {
        key: `search-preview:${type}:${hit.id}`,
        loader: async () => {
            previewLoading.value = true;
            try {
                const model = await fetchEntityModel(type, hit.id);
                const Minimal = resolveEntityViewComponentSync(type, "minimal");
                if (!model || !Minimal) {
                    return "Aperçu indisponible";
                }
                const prop = entityPropNameForType(type);
                return {
                    component: markRaw(Minimal),
                    props: {
                        [prop]: model,
                        showActions: false,
                        displayMode: "extended",
                    },
                };
            } finally {
                previewLoading.value = false;
            }
        },
    };
});

function onSelect(event) {
    event?.preventDefault?.();
    emit("select", props.hit);
}

function onToggleFavorite(event) {
    event?.preventDefault?.();
    event?.stopPropagation?.();
    emit("toggle-favorite", props.hit);
}
</script>

<template>
    <div
        class="global-search-hit-row group relative flex w-full items-stretch gap-1 rounded-box transition-colors hover:bg-base-200/70"
        :class="density === 'lg' ? 'px-2 py-2' : 'px-1 py-0.5'"
    >
        <OverlayTrigger
            v-if="canPreview && overlayContent"
            class="min-w-0 flex-1"
            :content="overlayContent"
            trigger="hover"
            placement="right-start"
            max-width="auto"
            :interactive="true"
            :close-on-outside="true"
            :close-on-escape="true"
            panel-class="max-w-[min(92vw,24rem)]"
            :focus-trap="false"
        >
            <button
                type="button"
                class="global-search-hit flex w-full items-start gap-2.5 px-2 py-1.5 text-left"
                @click="onSelect"
            >
                <EntityThumb
                    :size="thumbSize"
                    :src="hit.iconUrl || ''"
                    :label="normalizeEntityThumbLabel(hit.title)"
                    :alt="hit.title"
                    aria-hidden="true"
                />
                <span class="min-w-0 flex-1">
                    <span :class="titleClass">{{ hit.title }}</span>
                    <span
                        v-if="hit.subtitle"
                        class="global-search-excerpt mt-0.5 block text-xs leading-relaxed text-base-content/70"
                    >
                        {{ hit.subtitle }}
                    </span>
                </span>
            </button>
        </OverlayTrigger>

        <button
            v-else
            type="button"
            class="global-search-hit flex min-w-0 flex-1 items-start gap-2.5 px-2 py-1.5 text-left"
            @click="onSelect"
        >
            <EntityThumb
                :size="thumbSize"
                :src="hit.iconUrl || ''"
                :label="normalizeEntityThumbLabel(hit.title)"
                :alt="hit.title"
                aria-hidden="true"
            />
            <span class="min-w-0 flex-1">
                <span :class="titleClass">{{ hit.title }}</span>
                <span
                    v-if="hit.subtitle"
                    class="global-search-excerpt mt-0.5 block text-xs leading-relaxed text-base-content/70"
                >
                    {{ hit.subtitle }}
                </span>
            </span>
        </button>

        <button
            v-if="showFavoriteToggle"
            type="button"
            class="btn btn-ghost btn-xs btn-square shrink-0 self-center"
            :aria-label="isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'"
            @click="onToggleFavorite"
        >
            <Icon
                source="fa-heart"
                :pack="isFavorite ? 'solid' : 'regular'"
                size="sm"
                :class="isFavorite ? 'text-primary' : 'opacity-50 group-hover:opacity-80'"
                alt=""
            />
        </button>
    </div>
</template>
