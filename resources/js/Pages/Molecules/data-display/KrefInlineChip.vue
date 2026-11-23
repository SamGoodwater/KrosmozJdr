<script setup>
/**
 * Puce kref hors TipTap et dans l’éditeur : résumé de caractéristique, vue minimale d’entité,
 * ou libellé cliquable (page / section — le popover est géré par RichTextKrefInteractions).
 *
 * @example
 * <KrefInlineChip kref-type="characteristic" kref-payload='{"key":"action_points_creature"}' label="PA" />
 */
import { computed } from "vue";
import { parseKrefPayload, normalizeKrefType } from "@/Composables/richText/krefCodec";
import { getReferencePresentation } from "@/Composables/richText/referenceRenderService";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import KrefEntityEmbed from "@/Pages/Molecules/data-display/KrefEntityEmbed.vue";
import {
    resolveDef,
    getCharacteristicColorStyle,
} from "@/Composables/entity/useCharacteristicDisplay";

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

/** @type {import('vue').ComputedRef<'text'|'icon'|'minimal'>} */
const entityView = computed(() => {
    const v = String(payload.value?.view || "text").trim();
    return ["text", "icon", "minimal"].includes(v) ? v : "text";
});

const wrapCharacteristicTooltip = computed(
    () => krefType.value === "characteristic" && characteristicKey.value !== "",
);

const wrapEntityEmbed = computed(
    () => krefType.value === "entity" && entityTypeStr.value !== "" && entityId.value != null,
);

const rootClass = computed(() => {
    const base = props.asRoot ? presentation.value.wrapperClasses.join(" ") : "";
    const viewClass =
        wrapEntityEmbed.value && entityView.value !== "text" ? `kref--view-${entityView.value}` : "";
    return [base, viewClass].filter(Boolean).join(" ") || undefined;
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

        <KrefEntityEmbed
            v-else-if="wrapEntityEmbed"
            :entity-type="entityTypeStr"
            :entity-id="entityId"
            :label="presentation.displayLabel"
            :view="entityView"
            :fallback-icon-class="presentation.iconClass"
        />

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
</style>
