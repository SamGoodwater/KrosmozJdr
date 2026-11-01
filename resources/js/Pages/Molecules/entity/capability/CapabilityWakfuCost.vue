<script setup>
/**
 * Coût d'une capacité : « [icône PA] 2 PA [icône réserve de Wakfu] ».
 *
 * @description
 * Une capacité se paie toujours en PA pris dans la réserve de Wakfu (en combat comme hors combat) :
 * l'icône de la réserve chevauche légèrement les lettres « PA », le tooltip rappelle la règle.
 * Sans coût (vide ou 0) : « [icône PA] 0 PA », sans icône de réserve.
 *
 * @example
 * <CapabilityWakfuCost :value="capability.pa" />
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
    /** Taille des icônes (px). */
    iconPx: { type: String, default: "14" },
});

const WAKFU_ICON_FALLBACK = "icons/caracteristics/wakfu.webp";

const amount = computed(() => {
    const raw = props.value == null ? "" : String(props.value).trim();
    return raw === "" ? "0" : raw;
});

const isFree = computed(() => amount.value === "0");

const paVisual = computed(() => resolveSpellUsageCharacteristicVisual("pa"));

const wakfuIcon = computed(() => {
    const icon = String(getByCharacteristicKey("creature", "wakfu_reserve_creature")?.icon ?? "").trim();
    if (icon === "") return WAKFU_ICON_FALLBACK;
    return icon.includes("/") ? icon : `icons/caracteristics/${icon}`;
});

const ariaLabel = computed(() => (isFree.value ? "0 PA : sans coût" : `${amount.value} PA depuis la réserve de Wakfu`));

const tooltipText = computed(() =>
    isFree.value
        ? "Sans coût : cette capacité ne puise pas dans la réserve de Wakfu."
        : `${amount.value} PA depuis la réserve de Wakfu. Les dieux ne rendent chaque tour que les PA des sorts de classe : `
          + "une capacité puise toujours dans ta réserve, en combat comme hors combat.",
);

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
        >
            <span
                v-if="paVisual.hasIcon"
                class="inline-flex shrink-0 items-center justify-center p-px"
                :style="spellUsageIconBackdropStyle(paVisual.color)"
            >
                <Image :source="paVisual.source" alt="" :width="iconPx" :height="iconPx" fit="contain" class="block shrink-0" />
            </span>
            <span class="text-base-content/90" :style="spellUsageTextColorStyle(paVisual.color)">{{ amount }}</span>
            <span class="relative inline-flex items-center" :class="{ 'pr-1.5': !isFree }">
                <span class="text-base-content/90" :style="spellUsageTextColorStyle(paVisual.color)">PA</span>
                <Image
                    v-if="!isFree"
                    data-testid="wakfu-reserve-icon"
                    :source="wakfuIcon"
                    alt=""
                    :width="iconPx"
                    :height="iconPx"
                    fit="contain"
                    class="pointer-events-none absolute -right-1 -top-1.5 block shrink-0 drop-shadow"
                />
            </span>
        </span>
    </Tooltip>
</template>
