<script setup>
/**
 * Affichage du mode de résolution d’un sort.
 *
 * - `variant="full"` : libellé coloré + formule (vs séparé) + valeurs si runtime.
 * - `variant="compact"` : libellé cliquable → popover avec la formule (et valeurs).
 *
 * @example
 * <SpellResolutionFormula :entity="spell" variant="full" />
 * <SpellResolutionFormula :entity="spell" variant="compact" />
 */
import { computed, inject, unref } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Popover from "@/Pages/Atoms/feedback/Popover.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import {
    getCharacteristicColorStyle,
    resolveDef,
} from "@/Composables/entity/useCharacteristicDisplay";
import { CHARACTERISTIC_RUNTIME_INJECT_KEY } from "@/Composables/entity/characteristicRuntimeContext";
import { getElementTextStyle } from "@/Utils/Entity/Elements";
import { buildSpellResolutionFormula } from "@/Utils/Entity/spellResolutionFormula";

const props = defineProps({
    entity: { type: Object, required: true },
    /** full = page/modal ; compact = carte minimale (popover au clic). */
    variant: {
        type: String,
        default: "full",
        validator: (v) => ["full", "compact"].includes(v),
    },
});

const injectedRuntime = inject(CHARACTERISTIC_RUNTIME_INJECT_KEY, null);

const formula = computed(() => buildSpellResolutionFormula(props.entity));

/** Couleur d’élément sans fond pour Jet d’attaque / sauvegarde / DD. */
const elementTextStyle = computed(() => getElementTextStyle(formula.value.elementPrimary));

const elementTextClass = "inline font-semibold leading-none";

const chips = computed(() => {
    const map = {};
    const allParts = [...formula.value.leftParts, ...formula.value.rightParts];
    for (const part of allParts) {
        if (part.type !== "chip" || map[part.key]) continue;
        const def = resolveDef(part.key, undefined, { sourceGroups: ["creature"] });
        map[part.key] = {
            key: part.key,
            name: def?.name || part.key,
            shortName: def?.short_name || def?.shortName || def?.name || part.key,
            icon: def?._resolvedIcon || def?.icon || "",
            colorStyle: getCharacteristicColorStyle(def?._resolvedColor || def?.color) || undefined,
            cardGroup: "creature",
            hideDisplayValueInTooltip: true,
        };
    }
    return map;
});

/**
 * Valeurs numériques si un runtime créature est fourni.
 * @returns {Array<{ key: string, label: string, value: string, colorStyle?: object }>}
 */
const runtimeValues = computed(() => {
    const runtime = unref(injectedRuntime);
    const computedMap = runtime?.computed;
    if (!computedMap || typeof computedMap !== "object") return [];
    const out = [];
    for (const key of formula.value.valueKeys) {
        const entry = computedMap[key];
        if (entry == null) continue;
        const raw =
            entry.total ??
            entry.value ??
            entry.display_value ??
            entry.displayValue ??
            null;
        if (raw == null || String(raw).trim() === "" || String(raw) === "—") continue;
        const chip = chips.value[key];
        out.push({
            key,
            label: chip?.shortName || key,
            value: String(raw),
            colorStyle: chip?.colorStyle,
        });
    }
    return out;
});

const hasFormulaBody = computed(
    () => formula.value.leftParts.length > 0 || formula.value.rightParts.length > 0,
);

/**
 * Regroupe les segments colorés en pastilles lisibles.
 * @param {Array<{ text: string, colored?: boolean }>} parts
 * @returns {Array<{ text: string, colored: boolean }>}
 */
function groupHeadlineParts(parts) {
    const list = Array.isArray(parts) ? parts : [];
    const out = [];
    for (const part of list) {
        const colored = Boolean(part?.colored);
        const text = part?.text != null ? String(part.text) : "";
        if (!text) continue;
        const last = out[out.length - 1];
        if (last && last.colored === colored) {
            last.text += text;
        } else {
            out.push({ text, colored });
        }
    }
    return out;
}

const leftHeadlineGroups = computed(() => groupHeadlineParts(formula.value.leftHeadline));
const rightHeadlineGroups = computed(() => groupHeadlineParts(formula.value.rightHeadline));
</script>

<template>
    <div v-if="formula.show" class="spell-resolution-formula min-w-0">
        <!-- Carte minimale : libellé → popover -->
        <Popover
            v-if="variant === 'compact'"
            placement="top"
            max-width="md"
            panel-class="rounded-box border border-base-300 bg-base-100 p-3 text-base-content shadow-lg space-y-2 max-w-sm"
        >
            <button
                type="button"
                class="spell-resolution-trigger inline-flex max-w-full flex-wrap items-center gap-x-1 text-left text-xs font-medium leading-snug hover:underline"
                data-cy="spell-resolution-trigger"
            >
                <template v-for="(part, i) in leftHeadlineGroups" :key="`hl-${i}`">
                    <span
                        v-if="part.colored"
                        :class="elementTextClass"
                        :style="elementTextStyle"
                    >{{ part.text }}</span>
                    <span v-else>{{ part.text }}</span>
                </template>
                <template v-if="rightHeadlineGroups.length">
                    <span class="px-1.5 opacity-60">vs</span>
                    <template v-for="(part, i) in rightHeadlineGroups" :key="`hr-${i}`">
                        <span
                            v-if="part.colored"
                            :class="elementTextClass"
                            :style="elementTextStyle"
                        >{{ part.text }}</span>
                        <span v-else>{{ part.text }}</span>
                    </template>
                </template>
            </button>
            <template #content>
                <div class="space-y-2 text-sm">
                    <p class="inline-flex max-w-full flex-wrap items-center gap-x-1 font-medium leading-snug">
                        <template v-for="(part, i) in leftHeadlineGroups" :key="`phl-${i}`">
                            <span
                                v-if="part.colored"
                                :class="elementTextClass"
                                :style="elementTextStyle"
                            >{{ part.text }}</span>
                            <span v-else>{{ part.text }}</span>
                        </template>
                        <template v-if="rightHeadlineGroups.length">
                            <span class="px-2 opacity-60">vs</span>
                            <template v-for="(part, i) in rightHeadlineGroups" :key="`phr-${i}`">
                                <span
                                    v-if="part.colored"
                                    :class="elementTextClass"
                                    :style="elementTextStyle"
                                >{{ part.text }}</span>
                                <span v-else>{{ part.text }}</span>
                            </template>
                        </template>
                    </p>
                    <div
                        v-if="hasFormulaBody"
                        class="inline-flex max-w-full flex-wrap items-center gap-x-1 gap-y-1 leading-snug"
                    >
                        <template v-for="(part, index) in formula.leftParts" :key="`pl-${index}`">
                            <span v-if="part.type === 'text'">{{ part.text }}</span>
                            <Tooltip v-else placement="top">
                                <template #content>
                                    <CharacteristicInfoPanel :model="chips[part.key]" tone="dark" />
                                </template>
                                <span
                                    class="inline-flex cursor-default items-center gap-1 rounded bg-base-200/50 px-1 py-0.5"
                                    :style="chips[part.key]?.colorStyle"
                                >
                                    <Icon
                                        v-if="chips[part.key]?.icon"
                                        :source="chips[part.key].icon"
                                        :alt="chips[part.key].shortName"
                                        size="xs"
                                    />
                                    <span class="font-semibold">{{ chips[part.key]?.shortName }}</span>
                                </span>
                            </Tooltip>
                        </template>
                        <span v-if="formula.rightParts.length" class="px-2 opacity-60">vs</span>
                        <template v-for="(part, index) in formula.rightParts" :key="`pr-${index}`">
                            <span v-if="part.type === 'text'">{{ part.text }}</span>
                            <Tooltip v-else placement="top">
                                <template #content>
                                    <CharacteristicInfoPanel :model="chips[part.key]" tone="dark" />
                                </template>
                                <span
                                    class="inline-flex cursor-default items-center gap-1 rounded bg-base-200/50 px-1 py-0.5"
                                    :style="chips[part.key]?.colorStyle"
                                >
                                    <Icon
                                        v-if="chips[part.key]?.icon"
                                        :source="chips[part.key].icon"
                                        :alt="chips[part.key].shortName"
                                        size="xs"
                                    />
                                    <span class="font-semibold">{{ chips[part.key]?.shortName }}</span>
                                </span>
                            </Tooltip>
                        </template>
                    </div>
                    <ul
                        v-if="runtimeValues.length"
                        class="flex flex-wrap gap-x-3 gap-y-1 text-xs opacity-90"
                    >
                        <li
                            v-for="row in runtimeValues"
                            :key="row.key"
                            class="inline-flex items-center gap-1"
                            :style="row.colorStyle"
                        >
                            <span class="font-medium">{{ row.label }}</span>
                            <span class="tabular-nums">{{ row.value }}</span>
                        </li>
                    </ul>
                    <p v-if="formula.note" class="text-xs leading-snug opacity-80">{{ formula.note }}</p>
                </div>
            </template>
        </Popover>

        <!-- Page / modal : libellé puis formule (deux lignes, sans fond) -->
        <div v-else class="flex flex-col gap-1.5">
            <p class="flex max-w-full flex-wrap items-center gap-x-1 font-medium leading-snug">
                <template v-for="(part, i) in leftHeadlineGroups" :key="`fhl-${i}`">
                    <span
                        v-if="part.colored"
                        :class="elementTextClass"
                        :style="elementTextStyle"
                    >{{ part.text }}</span>
                    <span v-else>{{ part.text }}</span>
                </template>
                <template v-if="rightHeadlineGroups.length">
                    <span class="px-2 opacity-60">vs</span>
                    <template v-for="(part, i) in rightHeadlineGroups" :key="`fhr-${i}`">
                        <span
                            v-if="part.colored"
                            :class="elementTextClass"
                            :style="elementTextStyle"
                        >{{ part.text }}</span>
                        <span v-else>{{ part.text }}</span>
                    </template>
                </template>
            </p>
            <div
                v-if="hasFormulaBody"
                class="flex max-w-full flex-wrap items-center gap-x-1 gap-y-1 leading-snug text-primary-200/90"
                data-cy="spell-resolution-formula-line"
            >
                <template v-for="(part, index) in formula.leftParts" :key="`fl-${index}`">
                    <span v-if="part.type === 'text'">{{ part.text }}</span>
                    <Tooltip v-else placement="top">
                        <template #content>
                            <CharacteristicInfoPanel :model="chips[part.key]" tone="dark" />
                        </template>
                        <span
                            class="inline-flex cursor-default items-center gap-1 rounded bg-base-200/50 px-1 py-0.5"
                            :style="chips[part.key]?.colorStyle"
                        >
                            <Icon
                                v-if="chips[part.key]?.icon"
                                :source="chips[part.key].icon"
                                :alt="chips[part.key].shortName"
                                size="xs"
                            />
                            <span class="font-semibold">{{ chips[part.key]?.shortName }}</span>
                        </span>
                    </Tooltip>
                </template>
                <span v-if="formula.rightParts.length" class="px-2 opacity-60">vs</span>
                <template v-for="(part, index) in formula.rightParts" :key="`fr-${index}`">
                    <span v-if="part.type === 'text'">{{ part.text }}</span>
                    <Tooltip v-else placement="top">
                        <template #content>
                            <CharacteristicInfoPanel :model="chips[part.key]" tone="dark" />
                        </template>
                        <span
                            class="inline-flex cursor-default items-center gap-1 rounded bg-base-200/50 px-1 py-0.5"
                            :style="chips[part.key]?.colorStyle"
                        >
                            <Icon
                                v-if="chips[part.key]?.icon"
                                :source="chips[part.key].icon"
                                :alt="chips[part.key].shortName"
                                size="xs"
                            />
                            <span class="font-semibold">{{ chips[part.key]?.shortName }}</span>
                        </span>
                    </Tooltip>
                </template>
            </div>
            <ul
                v-if="runtimeValues.length"
                class="flex flex-wrap gap-x-3 gap-y-1 text-xs opacity-90"
            >
                <li
                    v-for="row in runtimeValues"
                    :key="row.key"
                    class="inline-flex items-center gap-1"
                    :style="row.colorStyle"
                >
                    <span class="font-medium">{{ row.label }}</span>
                    <span class="tabular-nums">{{ row.value }}</span>
                </li>
            </ul>
            <p v-if="formula.note" class="text-xs leading-snug opacity-80">{{ formula.note }}</p>
        </div>
    </div>
</template>
