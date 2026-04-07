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

const lineInnerClass =
    'inline-flex min-w-0 max-w-full flex-wrap items-center gap-x-1 gap-y-0.5 text-left text-xs leading-snug text-base-content';
</script>

<template>
    <Tooltip class="inline-flex max-w-full min-w-0" placement="top" :glass="false">
        <span :class="lineInnerClass">
            <template v-if="parts.elementBlock">
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
            <template v-if="parts.characteristicBlock">
                <Icon
                    v-if="parts.characteristicBlock.icon"
                    :source="parts.characteristicBlock.icon"
                    :alt="parts.characteristicBlock.name"
                    size="xs"
                    class="shrink-0 opacity-90"
                    :style="parts.characteristicBlock.style"
                />
                <span class="shrink-0 font-medium" :style="parts.characteristicBlock.style">
                    {{ parts.characteristicBlock.name }}
                </span>
            </template>
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
</template>
