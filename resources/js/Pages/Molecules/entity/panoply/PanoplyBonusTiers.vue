<script setup>
/**
 * Bonus de panoplie, un palier de pièces par ligne (ou par colonne en Full).
 *
 * @description
 * Plus de mini-menu : chaque palier affiche le badge de pièces puis ses bonus.
 * `stack` : une ligne par palier (minimal, line, tooltips).
 * `columns` : une colonne par palier, badge en tête, caractéristiques icon + nom.
 *
 * @example
 * <PanoplyBonusTiers :bonus='{"2":{"strength":1},"8":{"intelligence":1}}' />
 */
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import CharacteristicChip from "@/Pages/Atoms/data-display/CharacteristicChip.vue";
import CharacteristicInlineGroup from "@/Pages/Molecules/data-display/CharacteristicInlineGroup.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { panoplyTierStatMap, visiblePanoplyBonusTiers } from "@/Utils/entity/panoplyBonus";

const props = defineProps({
    bonus: {
        type: [Object, String, Array],
        default: null,
    },
    labelMode: {
        type: String,
        default: "icon-only",
        validator: (v) => ["full", "short", "icon-only"].includes(v),
    },
    layout: {
        type: String,
        default: "stack",
        validator: (v) => ["stack", "columns", "grid", "inline"].includes(v),
    },
});

const tiers = computed(() => visiblePanoplyBonusTiers(props.bonus));

const isColumns = computed(() => props.layout === "columns");

/**
 * @param {{ pieceCount?: number, rows?: Array }} tier
 * @returns {Array}
 */
function effectItemsForTier(tier) {
    const map = panoplyTierStatMap(tier);
    if (Object.keys(map).length === 0) {
        return [];
    }
    const cell = buildCharacteristicEffectCell({
        rawValues: [map],
        options: {},
        sourceGroups: ["panoply", "item"],
        size: "sm",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
}

/**
 * @param {number} pieceCount
 * @returns {string}
 */
function pieceLabel(pieceCount) {
    const n = Number(pieceCount);
    if (!Number.isFinite(n) || n < 1) {
        return "";
    }
    return n > 1 ? `${n} pièces` : "1 pièce";
}
</script>

<template>
    <div
        v-if="tiers.length"
        data-cy="panoply-bonus-tiers"
        class="min-w-0 max-w-full"
        :class="
            isColumns
                ? 'flex flex-wrap items-start gap-3'
                : 'flex flex-col gap-1'
        "
    >
        <div
            v-for="tier in tiers"
            :key="tier.pieceCount"
            data-cy="panoply-bonus-tier"
            :data-piece-count="tier.pieceCount"
            class="min-w-0"
            :class="
                isColumns
                    ? 'flex min-w-[7rem] flex-1 flex-col items-center gap-1.5'
                    : 'flex min-w-0 items-center gap-1.5'
            "
        >
            <Badge
                color="neutral"
                variant="outline"
                :size="isColumns ? 'sm' : 'xs'"
                :strong="true"
                :content="String(tier.pieceCount)"
                :title="pieceLabel(tier.pieceCount)"
                class="shrink-0"
            />
            <div
                v-if="isColumns"
                class="flex w-full flex-col items-stretch gap-1"
            >
                <CharacteristicChip
                    v-for="(item, idx) in effectItemsForTier(tier)"
                    :key="`${tier.pieceCount}-${idx}`"
                    :item="item"
                    :label-mode="labelMode"
                    class="w-full justify-center"
                />
            </div>
            <CharacteristicInlineGroup
                v-else
                :items="effectItemsForTier(tier)"
                :label-mode="labelMode"
                class="min-w-0"
            />
        </div>
    </div>
</template>
