<script setup>
/**
 * Libellé de caractéristique d’effet avec fiche au survol (vue « lien »).
 *
 * @example
 * <SpellCharacteristicHoverLink characteristic-key="air" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import {
    getCharacteristicColorStyle,
    resolveDef,
    SPELL_EFFECT_CHIP_SOURCE_GROUPS,
} from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps({
    /** Clé caractéristique (air, agi, force…). */
    characteristicKey: { type: String, default: "" },
    /** Libellé forcé (sinon short_name / name de la définition). */
    label: { type: String, default: "" },
    /** Icône forcée. */
    icon: { type: String, default: "" },
    /** Couleur forcée (hex / token). */
    color: { type: [String, Object], default: null },
    /** Style CSS déjà résolu (prioritaire sur `color`). */
    colorStyle: { type: Object, default: null },
    size: {
        type: String,
        default: "xs",
        validator: (v) => ["xs", "sm"].includes(v),
    },
});

const def = computed(() => {
    const key = props.characteristicKey != null ? String(props.characteristicKey).trim() : "";
    if (!key) return null;
    return resolveDef(key, undefined, {
        sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS],
    });
});

const panelModel = computed(() => {
    const d = def.value;
    const key = props.characteristicKey || "characteristic";
    const resolvedStyle =
        props.colorStyle ||
        getCharacteristicColorStyle(props.color || d?._resolvedColor || d?.color) ||
        undefined;
    return {
        key,
        name: d?.name || props.label || key,
        shortName: props.label || d?.short_name || d?.shortName || d?.name || key,
        icon: props.icon || d?._resolvedIcon || d?.icon || "",
        colorStyle: resolvedStyle,
        cardGroup: "creature",
        hideDisplayValueInTooltip: true,
    };
});

const style = computed(() => panelModel.value.colorStyle);
const displayLabel = computed(() => panelModel.value.shortName);
const displayIcon = computed(() => panelModel.value.icon);
const canHover = computed(() => Boolean(props.characteristicKey || props.label));
</script>

<template>
    <Tooltip v-if="canHover" class="inline-flex shrink-0" placement="top">
        <template #content>
            <CharacteristicInfoPanel :model="panelModel" tone="dark" />
        </template>
        <span
            class="inline-flex cursor-help items-center gap-1 font-medium"
            :style="style"
            data-cy="spell-characteristic-hover-link"
        >
            <Icon
                v-if="displayIcon"
                :source="displayIcon"
                :alt="displayLabel"
                :size="size"
                class="shrink-0"
            />
            <span>{{ displayLabel }}</span>
        </span>
    </Tooltip>
    <span v-else class="inline-flex items-center gap-1 font-medium" :style="style">
        <Icon
            v-if="displayIcon"
            :source="displayIcon"
            :alt="displayLabel"
            :size="size"
            class="shrink-0"
        />
        <span>{{ displayLabel }}</span>
    </span>
</template>
