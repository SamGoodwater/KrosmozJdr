<script setup>
/**
 * Puce kref hors TipTap et dans l’éditeur : résumé de caractéristique, vue minimale d’entité,
 * ou libellé cliquable (page / section — le popover est géré par RichTextKrefInteractions).
 *
 * @example
 * <KrefInlineChip kref-type="characteristic" kref-payload='{"key":"action_points_creature"}' label="PA" />
 */
import { computed, markRaw, onMounted, ref, watch } from "vue";
import { parseKrefPayload, normalizeKrefType } from "@/Composables/richText/krefCodec";
import { getReferencePresentation } from "@/Composables/richText/referenceRenderService";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import KrefEntityTooltipBody from "@/Pages/Molecules/data-display/KrefEntityTooltipBody.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import { loadKrefEntityMinimalOverlay } from "@/Composables/richText/krefEntityMinimalOverlay";
import {
    getCachedKrefEntityPreview,
    loadKrefEntityPreview,
} from "@/Composables/richText/krefEntityPreviewCache";
import { OVERLAY_TRIGGER } from "@/Composables/overlay/overlayConstants";
import {
    resolveDef,
    getCharacteristicColorStyle,
} from "@/Composables/entity/useCharacteristicDisplay";
import { getSpellInlineChipSurfaceStyle } from "@/Utils/Entity/Elements";
import { spellImageFilterStyle } from "@/Utils/Entity/spellImageFilter";

const props = defineProps({
    krefType: { type: String, default: "" },
    /** Payload JSON (chaîne), comme l’attribut du nœud TipTap. */
    krefPayload: { type: String, default: "{}" },
    label: { type: String, default: "" },
    /**
     * true : la puce est le span.kref (lecture).
     * false : le parent (NodeViewWrapper) porte déjà la classe kref.
     */
    asRoot: { type: Boolean, default: true },
});

const krefType = computed(() => normalizeKrefType(props.krefType));
const payload = computed(() => parseKrefPayload(props.krefPayload));

const presentation = computed(() =>
    getReferencePresentation({
        krefType: props.krefType,
        krefPayload: props.krefPayload,
        label: props.label,
    }),
);

const characteristicKey = computed(() => {
    if (krefType.value !== "characteristic") return "";
    return typeof payload.value?.key === "string" ? payload.value.key.trim() : "";
});

const charDef = computed(() => {
    if (!characteristicKey.value) return null;
    return resolveDef(characteristicKey.value, undefined, {
        sourceGroups: ["creature", "item", "resource", "spell", "capability", "consumable", "panoply"],
    });
});

const charIcon = computed(() => charDef.value?._resolvedIcon ?? charDef.value?.icon ?? null);

/**
 * @param {unknown} raw
 * @returns {string}
 */
function visibleLabel(raw) {
    const t = String(raw ?? "")
        .replace(/[\u200B-\u200D\uFEFF\u00AD]/g, "")
        .trim();
    return t.length > 0 ? t : "";
}

const charLabel = computed(() => {
    const d = charDef.value;
    const explicit = visibleLabel(props.label);
    if (!d) {
        return explicit || "Caractéristique";
    }
    if (explicit) {
        return explicit;
    }
    return visibleLabel(d.name) || visibleLabel(d.short_name) || "Caractéristique";
});

const charInfoModel = computed(() => ({
    key: characteristicKey.value,
    name: charLabel.value,
    shortName: charDef.value?.short_name || charLabel.value,
    icon: charIcon.value || "",
    color: charDef.value?._resolvedColor ?? charDef.value?.color ?? "",
    helper: charDef.value?.helper || "",
    descriptions: charDef.value?.descriptions || "",
    hideDisplayValueInTooltip: true,
    displayValue: "",
}));

const showCharacteristicResolvedChip = computed(
    () => krefType.value === "characteristic" && characteristicKey.value !== "",
);
const charTextStyle = computed(() =>
    getCharacteristicColorStyle(charDef.value?._resolvedColor ?? charDef.value?.color) || {},
);

const entityId = computed(() => {
    if (krefType.value !== "entity") return null;
    const id = payload.value?.id;
    if (id == null || id === "") return null;
    return id;
});

const entityTypeStr = computed(() => {
    if (krefType.value !== "entity") return "";
    const t = payload.value?.entityType;
    return typeof t === "string" ? t.trim() : "";
});

const wrapCharacteristicTooltip = computed(
    () => krefType.value === "characteristic" && characteristicKey.value !== "",
);

const wrapEntityTooltip = computed(
    () => krefType.value === "entity" && entityTypeStr.value !== "" && entityId.value != null,
);

const isSpellEntity = computed(() => entityTypeStr.value === "spells");

/** Aperçu léger (image + élément) pour les krefs sorts. */
const spellPreview = ref(null);

async function hydrateSpellPreview() {
    if (!isSpellEntity.value || entityId.value == null) {
        spellPreview.value = null;
        return;
    }
    const cached = getCachedKrefEntityPreview("spells", entityId.value);
    if (cached) {
        spellPreview.value = cached;
        return;
    }
    try {
        spellPreview.value = await loadKrefEntityPreview("spells", entityId.value);
    } catch {
        spellPreview.value = null;
    }
}

onMounted(() => {
    void hydrateSpellPreview();
});

watch([isSpellEntity, entityId], () => {
    void hydrateSpellPreview();
});

const spellChipStyle = computed(() => {
    if (!isSpellEntity.value) return undefined;
    return getSpellInlineChipSurfaceStyle(spellPreview.value?.element);
});

const spellImageStyle = computed(() =>
    spellImageFilterStyle({ element: spellPreview.value?.element, image: spellPreview.value?.image }),
);

const entityOverlayContent = computed(() => {
    if (!wrapEntityTooltip.value) {
        return "";
    }
    const entityType = entityTypeStr.value;
    const id = entityId.value;
    return {
        key: `kref-entity-minimal:${entityType}:${id}`,
        loader: async () => {
            const overlay = await loadKrefEntityMinimalOverlay(entityType, id);
            if (overlay) {
                return overlay;
            }
            return {
                component: markRaw(KrefEntityTooltipBody),
                props: { entityType, id },
            };
        },
    };
});

const rootClass = computed(() => {
    const base = props.asRoot ? presentation.value.wrapperClasses.join(" ") : "";
    return [base, isSpellEntity.value ? "kref--spell-chip" : ""].filter(Boolean).join(" ") || undefined;
});
</script>

<template>
    <span
        :class="rootClass"
        :data-kref-type="asRoot ? krefType : undefined"
        :data-kref-payload="asRoot ? krefPayload : undefined"
    >
        <Tooltip
            v-if="wrapCharacteristicTooltip"
            placement="bottom"
            glass
            color="neutral"
            class="inline-flex max-w-full min-w-0 align-baseline"
        >
            <template #content>
                <CharacteristicInfoPanel :model="charInfoModel" tone="dark" />
            </template>
            <span class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
                <Icon v-if="charIcon" :source="charIcon" class="kref__iconimg" :alt="charLabel" size="xs" />
                <i v-else :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
                <span class="kref__label font-semibold" :style="charTextStyle">{{ charLabel }}</span>
            </span>
        </Tooltip>

        <OverlayTrigger
            v-else-if="wrapEntityTooltip"
            :content="entityOverlayContent"
            :trigger="OVERLAY_TRIGGER.HOVER"
            placement="bottom-start"
            max-width="auto"
            :interactive="true"
            :close-on-outside="true"
            :close-on-escape="true"
            :chromeless="true"
            panel-class="max-w-[min(92vw,22rem)]"
            :focus-trap="false"
            class="inline-flex max-w-full min-w-0 align-baseline"
        >
            <span
                class="kref__entity-chip inline-flex max-w-full min-w-0 items-center gap-1 align-baseline rounded-sm border border-transparent px-1 py-0.5 transition-colors"
                :class="{ 'kref__entity-chip--spell': isSpellEntity }"
                :style="spellChipStyle"
            >
                <span
                    v-if="isSpellEntity && spellPreview?.image"
                    class="pointer-events-none inline-flex h-4 w-4 shrink-0 overflow-hidden rounded-sm"
                    :style="spellImageStyle"
                >
                    <Image
                        :src="spellPreview.image"
                        :alt="presentation.displayLabel"
                        fit="cover"
                        rounded="sm"
                        class="h-4 w-4"
                    />
                </span>
                <i
                    v-else
                    :class="[presentation.iconClass, 'kref__icon']"
                    aria-hidden="true"
                />
                <span class="kref__label">{{ presentation.displayLabel }}</span>
            </span>
        </OverlayTrigger>

        <span
            v-else-if="showCharacteristicResolvedChip"
            class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline"
        >
            <Icon v-if="charIcon" :source="charIcon" class="kref__iconimg" :alt="charLabel" size="xs" />
            <i v-else :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
            <span class="kref__label font-semibold" :style="charTextStyle">{{ charLabel }}</span>
        </span>

        <span v-else class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
            <i :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
            <span class="kref__label">{{ presentation.displayLabel }}</span>
        </span>
    </span>
</template>

<style scoped lang="scss">
.kref__icon {
    margin-inline-end: 0.28em;
    font-size: 0.92em;
    opacity: 0.92;
}

.kref__iconimg {
    margin-inline-end: 0.25em;
    display: inline-flex;
    vertical-align: middle;
}

.kref__label {
    min-width: 0;
    color: inherit;
    text-decoration: none;
}

.kref__entity-chip--spell {
    cursor: pointer;
    text-decoration: none;
}

.kref__entity-chip--spell:hover {
    background-color: var(--spell-chip-hover-bg) !important;
    border-color: color-mix(in oklch, currentColor 35%, transparent);
}
</style>
