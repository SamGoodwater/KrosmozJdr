<script setup>
/**
 * Chip « Boss + PA légendaires » pour la rangée de propriétés sous le nom.
 *
 * @description
 * Affiche l’icône Boss, le nombre de PA légendaires, puis l’icône PA.
 * Rendu vide si le pool est vide ou 0.
 *
 * @props {number|string|null} legendaryPa - Pool de PA légendaires
 * @props {string} [tooltip] - Texte du survol (règle des PA légendaires)
 * @props {string} [sizeClass] - Classes Tailwind pour l’icône Boss
 *
 * @example
 * <MonsterLegendaryPaMark :legendary-pa="3" size-class="h-4 w-4" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import MonsterBossMark from "@/Pages/Molecules/entity/monster/MonsterBossMark.vue";
import { resolveDef } from "@/Composables/entity/useCharacteristicDisplay";
import { normalizeCharacteristicIcon } from "@/Utils/Entity/characteristicTooltipLabels";

const MONSTER_BOSS_LEGENDARY_PA_TOOLTIP =
    "Boss : PA légendaires utilisables entre deux tours d'autres créatures, autant que le pool restant, en plus du tour. Le pool se recharge à la fin du tour du boss.";

const props = defineProps({
    legendaryPa: {
        type: [Number, String],
        default: null,
    },
    tooltip: {
        type: String,
        default: MONSTER_BOSS_LEGENDARY_PA_TOOLTIP,
    },
    sizeClass: {
        type: String,
        default: "h-4 w-4",
    },
});

const displayPa = computed(() => {
    const raw = props.legendaryPa;
    if (raw === null || raw === undefined || raw === "") {
        return null;
    }
    const n = Number(raw);
    if (Number.isNaN(n) || n <= 0) {
        return null;
    }
    return n;
});

const paDef = computed(() => resolveDef("pa", null, { sourceGroups: ["creature"] }) || null);

const paIcon = computed(() => {
    const raw = paDef.value?._resolvedIcon || paDef.value?.icon || "fa-solid fa-bolt";
    return normalizeCharacteristicIcon(raw) || "fa-solid fa-bolt";
});

const paLabel = computed(() => paDef.value?.short_name || paDef.value?.name || "PA");

const ariaLabel = computed(() =>
    displayPa.value != null ? `Boss, ${displayPa.value} ${paLabel.value} légendaires` : "Boss",
);
</script>

<template>
    <Tooltip v-if="displayPa != null" :content="tooltip" placement="top">
        <span
            class="monster-legendary-pa-mark inline-flex shrink-0 items-center gap-0.5 text-warning"
            role="img"
            :aria-label="ariaLabel"
        >
            <MonsterBossMark :show-tooltip="false" :size-class="sizeClass" />
            <span class="tabular-nums font-semibold leading-none">{{ displayPa }}</span>
            <Icon :source="paIcon" :alt="paLabel" size="xs" class="shrink-0" />
        </span>
    </Tooltip>
</template>
