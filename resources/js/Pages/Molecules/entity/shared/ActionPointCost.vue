<script setup>
/**
 * Coût en PA : « [icône PA] N PA » (+ icône réserve de Wakfu si demandé).
 *
 * @description
 * Quand `fromWakfuReserve` est vrai et le coût n’est pas 0, l’icône de la réserve
 * apparaît à côté de « PA » (alignée verticalement). Sinon affichage PA classique
 * (PA du tour / régénérés en combat).
 *
 * @example
 * <ActionPointCost :value="entity.pa" :from-wakfu-reserve="entity.paFromWakfuReserve" />
 */
import { computed } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import SpellUsageCharacteristicTooltipPanel from "@/Pages/Molecules/entity/spell/SpellUsageCharacteristicTooltipPanel.vue";
import { getByCharacteristicKey } from "@/Composables/store/useCharacteristicsStore";
import {
    resolveSpellUsageCharacteristicVisual,
    spellUsageIconBackdropStyle,
    spellUsageTextColorStyle,
} from "@/Utils/Entity/spellUsageCharacteristicVisual";

const props = defineProps({
    /** Valeur brute du champ `pa` (ex. "2"). */
    value: { type: [String, Number], default: null },
    /** True = PA pris dans la réserve de Wakfu. */
    fromWakfuReserve: { type: Boolean, default: false },
    /** Taille des icônes (px). */
    iconPx: { type: String, default: "14" },
});

const WAKFU_ICON_FALLBACK = "icons/caracteristics/wakfu.webp";

const amount = computed(() => {
    const raw = props.value == null ? "" : String(props.value).trim();
    return raw === "" ? "0" : raw;
});

const isFree = computed(() => amount.value === "0");

const showWakfuIcon = computed(() => props.fromWakfuReserve && !isFree.value);

const paVisual = computed(() => resolveSpellUsageCharacteristicVisual("pa"));

const wakfuIcon = computed(() => {
    const icon = String(getByCharacteristicKey("creature", "wakfu_reserve_creature")?.icon ?? "").trim();
    if (icon === "") return WAKFU_ICON_FALLBACK;
    return icon.includes("/") ? icon : `icons/caracteristics/${icon}`;
});

const ariaLabel = computed(() => {
    if (isFree.value) return "0 PA : sans coût";
    if (showWakfuIcon.value) return `${amount.value} PA depuis la réserve de Wakfu`;
    return `${amount.value} PA`;
});

const tooltipText = computed(() => {
    if (isFree.value) {
        return props.fromWakfuReserve
            ? "Sans coût : ne puise pas dans la réserve de Wakfu."
            : "Sans coût en PA.";
    }
    if (showWakfuIcon.value) {
        return (
            `${amount.value} PA depuis la réserve de Wakfu. Les dieux ne rendent chaque tour que les PA des sorts de classe : `
            + "ce coût puise dans ta réserve, en combat comme hors combat."
        );
    }
    return `${amount.value} PA du tour (régénérés en combat pour les sorts de classe).`;
});

const tooltipAccent = computed(() => {
    const c = paVisual.value?.color;
    return typeof c === "string" && c.startsWith("#") ? { "--color": c } : {};
});
</script>

<template>
    <Tooltip placement="top" color="neutral" :accent-style="tooltipAccent">
        <template #content>
            <SpellUsageCharacteristicTooltipPanel
                :visual="paVisual"
                :status-text="tooltipText"
                :show-characteristic-name="false"
            />
        </template>
        <span
            class="inline-flex cursor-help items-center gap-0.5 border-b border-dotted border-base-content/30"
            tabindex="0"
            :aria-label="ariaLabel"
            data-cy="action-point-cost"
        >
            <span
                v-if="paVisual.hasIcon"
                class="inline-flex shrink-0 items-center justify-center p-px"
                :style="spellUsageIconBackdropStyle(paVisual.color)"
            >
                <Image :source="paVisual.source" alt="" :width="iconPx" :height="iconPx" fit="contain" class="block shrink-0" />
            </span>
            <span class="text-base-content/90" :style="spellUsageTextColorStyle(paVisual.color)">{{ amount }}</span>
            <span class="inline-flex items-center gap-0.5">
                <span class="text-base-content/90" :style="spellUsageTextColorStyle(paVisual.color)">PA</span>
                <Image
                    v-if="showWakfuIcon"
                    data-testid="wakfu-reserve-icon"
                    data-cy="wakfu-reserve-icon"
                    :source="wakfuIcon"
                    alt=""
                    :width="iconPx"
                    :height="iconPx"
                    fit="contain"
                    class="block shrink-0 self-center drop-shadow"
                />
            </span>
        </span>
    </Tooltip>
</template>
