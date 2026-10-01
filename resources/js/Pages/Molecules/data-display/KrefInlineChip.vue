<script setup>
/**
 * Puce kref hors TipTap et dans l’éditeur : résumé de caractéristique, vue minimale d’entité,
 * ou libellé cliquable (page / section — le popover est géré par RichTextKrefInteractions).
 *
 * @example
 * <KrefInlineChip kref-type="characteristic" kref-payload='{"key":"action_points_creature"}' label="PA" />
 */
import { computed, markRaw, ref, watch } from "vue";
import { parseKrefPayload, normalizeKrefType } from "@/Composables/richText/krefCodec";
import { getReferencePresentation } from "@/Composables/richText/referenceRenderService";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import KrefEntityTooltipBody from "@/Pages/Molecules/data-display/KrefEntityTooltipBody.vue";
import { loadKrefEntityMinimalOverlay } from "@/Composables/richText/krefEntityMinimalOverlay";
import { OVERLAY_TRIGGER } from "@/Composables/overlay/overlayConstants";
import { loadKrefCharacteristicReferenceMeta } from "@/Composables/richText/krefCharacteristicReferenceCache";
import { parseCharacteristicFormulaRichText } from "@/Composables/characteristic/useCharacteristicFormulaRichText";
import CharacteristicFormulaRichText from "@/Pages/Molecules/data-display/CharacteristicFormulaRichText.vue";
import { buildFormulaTableView } from "@/Utils/characteristic/formulaConfig";
import { buildCharacteristicKrefScopes } from "@/Utils/characteristic/characteristicScopeSummary";
import {
    resolveDef,
    getCharacteristicColorStyle,
} from "@/Composables/entity/useCharacteristicDisplay";

const FORMULA_SOURCE_GROUPS = [
    "creature",
    "item",
    "resource",
    "spell",
    "capability",
    "consumable",
    "panoply",
];

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

const charDef = computed(() => {
    if (krefType.value !== "characteristic") return null;
    const key = typeof payload.value?.key === "string" ? payload.value.key.trim() : "";
    if (!key) return null;
    return resolveDef(key, undefined, {
        sourceGroups: ["creature", "item", "resource", "spell", "capability", "consumable", "panoply"],
    });
});

const charIcon = computed(() => charDef.value?._resolvedIcon ?? charDef.value?.icon ?? null);
const charReferenceMeta = ref(null);
let charReferenceLoadSeq = 0;

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

const showCharacteristicResolvedChip = computed(
    () => krefType.value === "characteristic" && charDef.value != null,
);
const charTextStyle = computed(() =>
    getCharacteristicColorStyle(charDef.value?._resolvedColor ?? charDef.value?.color) || {},
);

function stripToPlainText(s) {
    if (s == null || s === "") return "";
    const d = document.createElement("div");
    d.innerHTML = String(s);
    return (d.textContent || "").replace(/\s+/g, " ").trim();
}

function formatDescriptions(raw) {
    if (raw == null) return "";
    if (typeof raw === "string") return stripToPlainText(raw);
    if (Array.isArray(raw)) {
        return raw
            .map((entry) => {
                if (typeof entry === "string") return stripToPlainText(entry);
                if (entry && typeof entry === "object" && entry.text) return stripToPlainText(entry.text);
                return "";
            })
            .filter(Boolean)
            .join("\n\n");
    }
    return "";
}

const charTooltipText = computed(() => {
    const d = charDef.value;
    if (!d) return "";
    const desc = formatDescriptions(d.descriptions);
    const helper = stripToPlainText(d.helper || "");
    const parts = [desc, helper].filter(Boolean);
    return parts.join("\n\n");
});

function segmentStyle(segment) {
    return getCharacteristicColorStyle(segment?.color) || {};
}

const charTooltipScopes = computed(() =>
    buildCharacteristicKrefScopes(charReferenceMeta.value?.rows).map((scope) => {
        const isTable = scope.formula !== "" && buildFormulaTableView(scope.formula) != null;
        return {
            ...scope,
            isTable,
            segments: scope.formula !== "" && !isTable
                ? parseCharacteristicFormulaRichText(scope.formula, {
                    sourceGroups: FORMULA_SOURCE_GROUPS,
                    tooltipOrder: "descriptions_first",
                })
                : [],
        };
    }),
);

async function ensureCharReferenceMeta() {
    if (krefType.value !== "characteristic") {
        return;
    }
    const key = typeof payload.value?.key === "string" ? payload.value.key.trim() : "";
    if (!key) {
        charReferenceMeta.value = null;
        return;
    }
    if (charReferenceMeta.value !== null) {
        return;
    }

    const seq = ++charReferenceLoadSeq;
    const meta = await loadKrefCharacteristicReferenceMeta(key);
    if (seq !== charReferenceLoadSeq) {
        return;
    }
    charReferenceMeta.value = meta;
}

watch(
    () => [krefType.value, payload.value?.key],
    () => {
        charReferenceMeta.value = null;
        charReferenceLoadSeq += 1;
    },
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
    () =>
        krefType.value === "characteristic" &&
        (charTooltipText.value.trim() !== "" || charTooltipScopes.value.length > 0),
);

const wrapEntityTooltip = computed(
    () => krefType.value === "entity" && entityTypeStr.value !== "" && entityId.value != null,
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

const rootClass = computed(() => (props.asRoot ? presentation.value.wrapperClasses.join(" ") : undefined));
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
            @open="ensureCharReferenceMeta"
        >
            <template #content>
                <div class="kref-rich-preview-panel max-h-[min(70vh,28rem)] max-w-sm overflow-y-auto text-sm leading-snug text-base-content">
                    <p v-if="charTooltipText" class="whitespace-pre-wrap">{{ charTooltipText }}</p>
                    <div v-if="charTooltipScopes.length" class="mt-2 space-y-2.5 border-t border-base-content/10 pt-2">
                        <section v-for="scope in charTooltipScopes" :key="scope.id" class="text-xs">
                            <p class="font-semibold text-base-content">{{ scope.label }}</p>
                            <p v-if="scope.detail" class="text-base-content/80">{{ scope.detail }}</p>
                            <CharacteristicFormulaRichText
                                v-if="scope.isTable"
                                class="mt-1"
                                :formula="scope.formula"
                                :source-groups="FORMULA_SOURCE_GROUPS"
                                tooltip-order="descriptions_first"
                            />
                            <p
                                v-else-if="scope.segments.length"
                                class="mt-0.5 inline-flex flex-wrap items-center gap-1 text-base-content/80"
                            >
                                <template v-for="(segment, idx) in scope.segments" :key="`${scope.id}-seg-${idx}`">
                                    <span v-if="segment.type === 'text'" class="whitespace-pre-wrap">
                                        {{ segment.text }}
                                    </span>
                                    <Tooltip v-else :content="segment.tooltip" placement="top">
                                        <span class="inline-flex items-center gap-1 rounded px-1 py-0.5 bg-base-200/40">
                                            <Icon
                                                v-if="segment.icon"
                                                :source="segment.icon"
                                                :alt="segment.label"
                                                size="xs"
                                                :style="segmentStyle(segment)"
                                            />
                                            <span class="font-semibold" :style="segmentStyle(segment)">
                                                {{ segment.label }}
                                            </span>
                                        </span>
                                    </Tooltip>
                                </template>
                            </p>
                        </section>
                    </div>
                </div>
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
            <span class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
                <i :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
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
}
</style>
