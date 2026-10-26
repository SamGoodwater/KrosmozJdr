<script setup>
/**
 * Une ligne d’effet « minimal » : élément, caractéristique, texte ou vue monstre invoqué (SpellSummonMonsterInline), zone.
 *
 * @props {object} item - Chip (characteristic, element, value, summon_monster, area, tooltip…)
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import SpellSummonMonsterInline from '@/Pages/Molecules/entity/spell/SpellSummonMonsterInline.vue';
import SpellCharacteristicHoverLink from '@/Pages/Molecules/entity/spell/SpellCharacteristicHoverLink.vue';
import SpellEffectChipTooltipContent from '@/Pages/Molecules/entity/spell/SpellEffectChipTooltipContent.vue';
import { buildEffectUsageMinimalParts } from '@/Composables/entity/buildEffectUsageMinimalParts';
import { buildUnifiedSubEffectModel } from '@/Composables/entity/useSpellSubEffectPresentation';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});

const parts = computed(() => buildEffectUsageMinimalParts(props.item));

const tooltipModel = computed(() =>
    buildUnifiedSubEffectModel({
        source: 'chip',
        chip: props.item,
        layout: 'minimal',
    }),
);

const tooltipDetail = computed(() => {
    const t = parts.value?.tooltip;
    return t != null && String(t).trim() !== '' ? String(t).trim() : '';
});

const characteristicKey = computed(() => {
    const raw = props.item?.characteristic;
    return raw != null ? String(raw).trim() : '';
});

const lineInnerClass =
    'inline-flex min-w-0 max-w-full flex-wrap items-center gap-x-1 gap-y-0.5 text-left text-xs leading-snug text-base-content';
</script>

<template>
    <span :class="lineInnerClass" data-cy="spell-effect-usage-minimal-line">
        <SpellCharacteristicHoverLink
            v-if="parts.elementBlock && characteristicKey"
            :characteristic-key="characteristicKey"
            :label="parts.elementBlock.label"
            :icon="parts.elementBlock.icon"
            :color-style="parts.elementBlock.style || null"
        />
        <template v-else-if="parts.elementBlock">
            <Icon
                :source="parts.elementBlock.icon"
                :alt="parts.elementBlock.label"
                size="xs"
                class="shrink-0 opacity-90"
                :style="parts.elementBlock.style"
            />
            <span class="shrink-0 tabular-nums" :style="parts.elementBlock.style">
                ({{ parts.elementBlock.label }})
            </span>
        </template>
        <SpellCharacteristicHoverLink
            v-if="parts.characteristicBlock && characteristicKey"
            :characteristic-key="characteristicKey"
            :label="parts.characteristicBlock.name"
            :icon="parts.characteristicBlock.icon"
            :color-style="parts.characteristicBlock.style || null"
        />
        <Tooltip class="inline-flex max-w-full min-w-0" placement="top" :glass="false">
            <span class="inline-flex min-w-0 max-w-full flex-wrap items-center gap-x-1 gap-y-0.5">
                <template v-if="parts.summonMonster">
                    <span
                        v-if="parts.summonPrefix"
                        class="shrink-0 font-medium text-base-content"
                        >{{ parts.summonPrefix }}</span>
                    <SpellSummonMonsterInline class="min-w-0 shrink" :monster-brief="parts.summonMonster" />
                </template>
                <span v-else-if="parts.text" class="min-w-0 break-words">{{ parts.text }}</span>
                <template v-if="parts.area">
                    <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                    <AreaDisplay :area="parts.area" icon-only icon-size="sm" class="shrink-0" />
                </template>
            </span>
            <template #content>
                <SpellEffectChipTooltipContent :model="tooltipModel" :detail-text="tooltipDetail" />
            </template>
        </Tooltip>
    </span>
</template>
