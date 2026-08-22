<script setup>
/**
 * Bonus de panoplie par palier de pièces (chiffre seul, comme les sorts).
 *
 * @description
 * Un seul palier (ou aucun) : pas de mini-menu. Les paliers sans bonus sont omis.
 *
 * @example
 * <PanoplyBonusTiers :bonus='{"2":{"strength":1}}' />
 */
import { ref, computed, watch } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
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
        default: "full",
        validator: (v) => ["full", "short", "icon-only"].includes(v),
    },
    layout: {
        type: String,
        default: "grid",
        validator: (v) => ["grid", "inline"].includes(v),
    },
});

const activeTierIndex = ref(0);

const tiers = computed(() => visiblePanoplyBonusTiers(props.bonus));

watch(
    tiers,
    (list) => {
        if (activeTierIndex.value >= list.length) {
            activeTierIndex.value = 0;
        }
    },
    { deep: true },
);

const activeTier = computed(() => tiers.value[activeTierIndex.value] ?? null);

const effectItems = computed(() => {
    const map = panoplyTierStatMap(activeTier.value);
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
});

const showTierTabs = computed(() => tiers.value.length > 1);
</script>

<template>
    <span
        v-if="tiers.length"
        class="inline-flex min-w-0 max-w-full flex-col gap-1 align-middle"
    >
        <div
            v-if="showTierTabs"
            role="tablist"
            class="inline-flex max-w-full flex-wrap items-center gap-0.5"
            aria-label="Paliers de panoplie"
        >
            <button
                v-for="(tier, i) in tiers"
                :key="tier.pieceCount"
                type="button"
                role="tab"
                class="inline-flex min-h-0 min-w-0 shrink-0 cursor-pointer items-center justify-center rounded border border-transparent bg-transparent p-0 leading-none transition-[opacity,box-shadow] hover:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-1 focus-visible:ring-offset-base-100"
                :class="
                    activeTierIndex === i
                        ? 'opacity-100 shadow-sm ring-1 ring-primary/50 ring-offset-1 ring-offset-base-100'
                        : 'opacity-55'
                "
                :aria-selected="activeTierIndex === i"
                :title="String(tier.pieceCount)"
                @click.stop="activeTierIndex = i"
            >
                <Badge
                    color="neutral"
                    variant="outline"
                    size="xs"
                    :strong="true"
                    :content="String(tier.pieceCount)"
                    :title="String(tier.pieceCount)"
                />
            </button>
        </div>
        <CharacteristicEffectsGrid
            v-if="layout === 'grid'"
            :items="effectItems"
            :label-mode="labelMode"
            class="min-w-0"
        />
        <CharacteristicInlineGroup
            v-else
            :items="effectItems"
            :label-mode="labelMode"
            class="min-w-0"
        />
    </span>
</template>
