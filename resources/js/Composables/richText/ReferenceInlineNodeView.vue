<script setup>
/**
 * Rendu Vue d’une référence inline TipTap (kref) : icône + libellé, infobulle riche selon le type.
 *
 * @description Le racine doit rester {@link NodeViewWrapper} pour TipTap.
 */
import { computed } from "vue";
import { NodeViewWrapper, nodeViewProps } from "@tiptap/vue-3";
import { encodeKrefTitle, parseKrefPayload, normalizeKrefType } from "@/Composables/richText/krefCodec";
import { getReferencePresentation } from "@/Composables/richText/referenceRenderService";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import KrefEntityTooltipBody from "@/Pages/Molecules/data-display/KrefEntityTooltipBody.vue";
import {
    resolveDef,
    getCharacteristicColorStyle,
} from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps(nodeViewProps);

const krefType = computed(() => normalizeKrefType(props.node.attrs.krefType));
const payload = computed(() => parseKrefPayload(props.node.attrs.krefPayload));

const presentation = computed(() => getReferencePresentation(props.node.attrs));

const titleAttr = computed(() =>
    encodeKrefTitle({
        krefType: props.node.attrs.krefType,
        krefPayload: props.node.attrs.krefPayload,
        label: props.node.attrs.label,
    }),
);

const wrapperClass = computed(() => {
    const fromEditor = props.HTMLAttributes?.class;
    const base = presentation.value.wrapperClasses;
    if (!fromEditor) return base.join(" ");
    return [...base, fromEditor].filter(Boolean).join(" ");
});

const charDef = computed(() => {
    if (krefType.value !== "characteristic") return null;
    const key = typeof payload.value?.key === "string" ? payload.value.key.trim() : "";
    if (!key) return null;
    return resolveDef(key, undefined, {
        sourceGroups: ["creature", "item", "resource", "spell", "capability", "consumable", "panoply"],
    });
});

const charIcon = computed(() => charDef.value?._resolvedIcon ?? charDef.value?.icon ?? null);

/**
 * Texte affichable : trim + retrait ZWSP / tiret conditionnel, sinon chaîne vide.
 *
 * @param {unknown} raw
 * @returns {string}
 */
function visibleLabel(raw) {
    const t = String(raw ?? "")
        .replace(/[\u200B-\u200D\uFEFF\u00AD]/g, "")
        .trim();
    return t.length > 0 ? t : "";
}

/**
 * Libellé affiché : texte explicite du kref si réellement visible ; sinon nom BDD puis abrégé.
 * Jamais de chaîne vide (sinon puce kref de largeur nulle = « plus rien »).
 */
const charLabel = computed(() => {
    const d = charDef.value;
    const explicit = visibleLabel(props.node.attrs.label);
    if (!d) {
        return explicit || "Caractéristique";
    }
    if (explicit) {
        return explicit;
    }
    return visibleLabel(d.name) || visibleLabel(d.short_name) || "Caractéristique";
});

/** Branche « carac résolue » sans ambiguïté ref/template (évite les rendus vides). */
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
    () => krefType.value === "characteristic" && charTooltipText.value.trim() !== "",
);

const wrapEntityTooltip = computed(
    () => krefType.value === "entity" && entityTypeStr.value !== "" && entityId.value != null,
);
</script>

<template>
    <NodeViewWrapper as="span" :class="wrapperClass" :title="titleAttr">
        <Tooltip
            v-if="wrapCharacteristicTooltip"
            :content="charTooltipText"
            placement="bottom"
            glass
            color="neutral"
            class="inline-flex max-w-full min-w-0 align-baseline"
        >
            <template #content>
                <div class="max-w-sm whitespace-pre-wrap text-sm leading-snug text-base-content">
                    {{ charTooltipText }}
                </div>
            </template>
            <span class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
                <Icon
                    v-if="charIcon"
                    :source="charIcon"
                    class="kref__iconimg"
                    :alt="charLabel"
                    size="xs"
                />
                <i v-else :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
                <span class="kref__label font-semibold" :style="charTextStyle">{{ charLabel }}</span>
            </span>
        </Tooltip>

        <Tooltip
            v-else-if="wrapEntityTooltip"
            placement="bottom"
            glass
            color="secondary"
            class="inline-flex max-w-full min-w-0 align-baseline"
        >
            <template #content>
                <KrefEntityTooltipBody :entity-type="entityTypeStr" :id="entityId" />
            </template>
            <span class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
                <i :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
                <span class="kref__label">{{ presentation.displayLabel }}</span>
            </span>
        </Tooltip>

        <span v-else-if="showCharacteristicResolvedChip" class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
            <Icon
                v-if="charIcon"
                :source="charIcon"
                class="kref__iconimg"
                :alt="charLabel"
                size="xs"
            />
            <i v-else :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
            <span class="kref__label font-semibold" :style="charTextStyle">{{ charLabel }}</span>
        </span>

        <span v-else class="inline-flex max-w-full min-w-0 items-center gap-0.5 align-baseline">
            <i :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
            <span class="kref__label">{{ presentation.displayLabel }}</span>
        </span>
    </NodeViewWrapper>
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
