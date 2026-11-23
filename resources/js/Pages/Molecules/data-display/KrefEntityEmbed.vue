<script setup>
/**
 * Embed kref entité selon `view` : text (puce) | icon (vignette) | minimal (carte).
 */
import { computed, markRaw, onMounted, ref, shallowRef, watch } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import KrefEntityTooltipBody from "@/Pages/Molecules/data-display/KrefEntityTooltipBody.vue";
import { loadKrefEntityMinimalOverlay } from "@/Composables/richText/krefEntityMinimalOverlay";
import {
    getCachedKrefEntityPreview,
    loadKrefEntityPreview,
} from "@/Composables/richText/krefEntityPreviewCache";
import { OVERLAY_TRIGGER } from "@/Composables/overlay/overlayConstants";
import { getSpellInlineChipSurfaceStyle } from "@/Utils/Entity/Elements";
import { spellImageFilterStyle } from "@/Utils/Entity/spellImageFilter";

const props = defineProps({
    entityType: { type: String, required: true },
    entityId: { type: [String, Number], required: true },
    label: { type: String, default: "" },
    /** @type {'text'|'icon'|'minimal'} */
    view: {
        type: String,
        default: "text",
        validator: (v) => ["text", "icon", "minimal"].includes(v),
    },
    fallbackIconClass: { type: String, default: "fa-solid fa-cube" },
});

const normalizedView = computed(() => {
    const v = String(props.view || "text").trim();
    return ["text", "icon", "minimal"].includes(v) ? v : "text";
});

const isSpell = computed(() => props.entityType === "spells");
const preview = ref(null);
const minimalHost = shallowRef(null);
const minimalError = ref("");

async function hydratePreview() {
    const et = String(props.entityType || "").trim();
    const id = props.entityId;
    if (!et || id == null || id === "") {
        preview.value = null;
        return;
    }
    const cached = getCachedKrefEntityPreview(et, id);
    if (cached) {
        preview.value = cached;
        return;
    }
    try {
        preview.value = await loadKrefEntityPreview(et, id);
    } catch {
        preview.value = null;
    }
}

async function hydrateMinimal() {
    if (normalizedView.value !== "minimal") {
        minimalHost.value = null;
        return;
    }
    minimalError.value = "";
    try {
        const overlay = await loadKrefEntityMinimalOverlay(props.entityType, props.entityId);
        if (overlay) {
            minimalHost.value = {
                component: markRaw(overlay.component),
                props: overlay.props,
            };
            return;
        }
        minimalError.value = "Aperçu indisponible.";
    } catch {
        minimalError.value = "Aperçu indisponible.";
        minimalHost.value = null;
    }
}

onMounted(() => {
    void hydratePreview();
    void hydrateMinimal();
});

watch(
    () => [props.entityType, props.entityId, normalizedView.value],
    () => {
        void hydratePreview();
        void hydrateMinimal();
    },
);

const displayLabel = computed(
    () => String(props.label || preview.value?.name || "Entité").trim() || "Entité",
);

const chipStyle = computed(() => {
    if (!isSpell.value) return undefined;
    return getSpellInlineChipSurfaceStyle(preview.value?.element);
});

const imageStyle = computed(() =>
    spellImageFilterStyle({
        element: preview.value?.element,
        image: preview.value?.image,
    }),
);

const overlayContent = computed(() => {
    const entityType = props.entityType;
    const id = props.entityId;
    return {
        key: `kref-entity-embed:${entityType}:${id}`,
        loader: async () => {
            const overlay = await loadKrefEntityMinimalOverlay(entityType, id);
            if (overlay) return overlay;
            return {
                component: markRaw(KrefEntityTooltipBody),
                props: { entityType, id },
            };
        },
    };
});
</script>

<template>
    <span
        v-if="normalizedView === 'minimal'"
        class="kref-entity-embed kref-entity-embed--minimal block max-w-[min(100%,22rem)] my-2"
    >
        <component
            :is="minimalHost.component"
            v-if="minimalHost"
            v-bind="minimalHost.props"
        />
        <span v-else-if="minimalError" class="text-sm text-base-content/60">{{ minimalError }}</span>
        <span v-else class="loading loading-spinner loading-sm text-base-content/40" aria-label="Chargement" />
    </span>

    <OverlayTrigger
        v-else-if="normalizedView === 'icon'"
        :content="overlayContent"
        :trigger="OVERLAY_TRIGGER.HOVER"
        placement="bottom-start"
        max-width="auto"
        :interactive="true"
        :close-on-outside="true"
        :close-on-escape="true"
        :chromeless="true"
        panel-class="max-w-[min(92vw,22rem)]"
        :focus-trap="false"
        class="kref-entity-embed kref-entity-embed--icon inline-flex align-baseline"
    >
        <button
            type="button"
            class="btn btn-ghost btn-xs btn-square border border-base-300/40"
            :aria-label="displayLabel"
            :title="displayLabel"
            :style="chipStyle"
        >
            <span
                v-if="preview?.image"
                class="inline-flex h-5 w-5 overflow-hidden rounded-sm"
                :style="imageStyle"
            >
                <Image :src="preview.image" :alt="displayLabel" fit="cover" rounded="sm" class="h-5 w-5" />
            </span>
            <i v-else :class="fallbackIconClass" aria-hidden="true" />
        </button>
    </OverlayTrigger>

    <OverlayTrigger
        v-else
        :content="overlayContent"
        :trigger="OVERLAY_TRIGGER.HOVER"
        placement="bottom-start"
        max-width="auto"
        :interactive="true"
        :close-on-outside="true"
        :close-on-escape="true"
        :chromeless="true"
        panel-class="max-w-[min(92vw,22rem)]"
        :focus-trap="false"
        class="kref-entity-embed kref-entity-embed--text inline-flex max-w-full min-w-0 align-baseline"
    >
        <span
            class="kref__entity-chip inline-flex max-w-full min-w-0 items-center gap-1 rounded-sm border border-transparent px-1 py-0.5 transition-colors"
            :class="{ 'kref__entity-chip--spell': isSpell }"
            :style="chipStyle"
        >
            <span
                v-if="preview?.image"
                class="pointer-events-none inline-flex h-4 w-4 shrink-0 overflow-hidden rounded-sm"
                :style="imageStyle"
            >
                <Image :src="preview.image" :alt="displayLabel" fit="cover" rounded="sm" class="h-4 w-4" />
            </span>
            <i v-else :class="[fallbackIconClass, 'kref__icon']" aria-hidden="true" />
            <span class="kref__label">{{ displayLabel }}</span>
        </span>
    </OverlayTrigger>
</template>

<style scoped>
.kref__entity-chip--spell {
    cursor: pointer;
    text-decoration: none;
}
.kref__entity-chip--spell:hover {
    background-color: var(--spell-chip-hover-bg) !important;
}
.kref__icon {
    margin-inline-end: 0.28em;
    font-size: 0.92em;
    opacity: 0.92;
}
.kref__label {
    min-width: 0;
    text-decoration: none;
    color: inherit;
}
</style>
