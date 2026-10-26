<script setup>
/**
 * Rend une formule découpée en texte, puces caractéristique et mot « Bonus ».
 *
 * @example
 * <SpellFormulaPartsInline :parts="abbreviateDamageFormula(f, 'fire').visible" :full-parts="full" />
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
    /** @type {Array<{ type: string, text?: string, key?: string }>} */
    parts: {
        type: Array,
        default: () => [],
    },
    /** Parties pour l’infobulle Bonus (formule entière). */
    fullParts: {
        type: Array,
        default: () => [],
    },
});

const chips = computed(() => {
    const map = {};
    const all = [...(props.parts || []), ...(props.fullParts || [])];
    for (const part of all) {
        if (part?.type !== "chip" || !part.key || map[part.key]) continue;
        const def = resolveDef(part.key, undefined, {
            sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS, "creature", "object", "consumable", "spell"],
        });
        map[part.key] = {
            key: part.key,
            name: def?.name || part.key,
            shortName: def?.short_name || def?.shortName || def?.name || part.key,
            icon: def?._resolvedIcon || def?.icon || "",
            colorStyle: getCharacteristicColorStyle(def?._resolvedColor || def?.color) || undefined,
            cardGroup: def?.group || "creature",
            hideDisplayValueInTooltip: true,
        };
    }
    return map;
});
</script>

<template>
    <span class="spell-formula-parts inline-flex max-w-full flex-wrap items-center gap-x-0.5 gap-y-0.5">
        <template v-for="(part, index) in parts" :key="`p-${index}`">
            <span v-if="part.type === 'text'" class="whitespace-pre">{{ part.text }}</span>
            <Tooltip v-else-if="part.type === 'chip'" placement="top">
                <template #content>
                    <CharacteristicInfoPanel :model="chips[part.key]" tone="dark" />
                </template>
                <span
                    class="inline-flex cursor-default items-center gap-0.5 rounded px-0.5 font-semibold"
                    :style="chips[part.key]?.colorStyle"
                    :data-cy="`spell-formula-chip-${part.key}`"
                >
                    <Icon
                        v-if="chips[part.key]?.icon"
                        :source="chips[part.key].icon"
                        :alt="chips[part.key].shortName"
                        size="xs"
                    />
                    <span>{{ chips[part.key]?.shortName }}</span>
                </span>
            </Tooltip>
            <Tooltip v-else-if="part.type === 'bonus'" placement="top">
                <template #content>
                    <div
                        class="inline-flex max-w-sm flex-wrap items-center gap-x-0.5 gap-y-0.5 text-sm"
                        data-cy="spell-formula-bonus-tooltip"
                    >
                        <template v-for="(fp, fi) in fullParts" :key="`fp-${fi}`">
                            <span v-if="fp.type === 'text'" class="whitespace-pre">{{ fp.text }}</span>
                            <span
                                v-else-if="fp.type === 'chip'"
                                class="inline-flex items-center gap-0.5 font-semibold"
                                :style="chips[fp.key]?.colorStyle"
                            >
                                <Icon
                                    v-if="chips[fp.key]?.icon"
                                    :source="chips[fp.key].icon"
                                    :alt="chips[fp.key].shortName"
                                    size="xs"
                                />
                                <span>{{ chips[fp.key]?.shortName || fp.key }}</span>
                            </span>
                        </template>
                    </div>
                </template>
                <span
                    class="cursor-help font-semibold text-primary-200 underline decoration-dotted underline-offset-2"
                    data-cy="spell-formula-bonus"
                >Bonus</span>
            </Tooltip>
        </template>
    </span>
</template>
