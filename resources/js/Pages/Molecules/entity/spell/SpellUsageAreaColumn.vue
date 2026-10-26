<script setup>
/**
 * Colonne zone d’un degré : icône + libellé, schéma visible ou en infobulle.
 *
 * @example
 * <SpellUsageAreaColumn area="line-1x3" :show-schema="true" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import AreaDisplay from "@/Pages/Molecules/entity/spell/AreaDisplay.vue";
import SpellZonePreview from "@/Pages/Molecules/entity/spell/SpellZonePreview.vue";
import {
    getAreaHumanReadable,
    getAreaIcon,
    getAreaShape,
    getAreaShapeLabel,
} from "@/Utils/Entity/Areas";

const props = defineProps({
    area: {
        type: String,
        default: "",
    },
    /** true = page / modal : schéma inline ; false = minimal : schéma dans AreaDisplay tooltip. */
    showSchema: {
        type: Boolean,
        default: false,
    },
});

const normalized = computed(() => (props.area != null ? String(props.area).trim() : ""));
const isPoint = computed(() => normalized.value === "point" || getAreaShape(normalized.value) === "point");
const humanLabel = computed(() => getAreaHumanReadable(normalized.value));
</script>

<template>
    <div
        v-if="normalized"
        class="spell-usage-area-column flex shrink-0 flex-col items-end gap-1 self-stretch"
        data-cy="spell-usage-area"
    >
        <template v-if="isPoint">
            <span
                class="inline-flex items-center gap-1 text-xs font-medium text-primary-200"
                data-cy="spell-usage-area-unique"
            >
                <Icon
                    :source="getAreaIcon(normalized)"
                    :alt="getAreaShapeLabel('point')"
                    size="sm"
                    class="shrink-0 opacity-90"
                />
                <span>Cible unique</span>
            </span>
        </template>
        <template v-else-if="showSchema">
            <div class="flex items-start gap-2">
                <div class="flex min-w-0 flex-col items-end gap-0.5">
                    <AreaDisplay :area="normalized" icon-size="sm" />
                    <span class="max-w-[10rem] text-right text-[0.7rem] leading-snug text-primary-300">
                        {{ humanLabel }}
                    </span>
                </div>
                <SpellZonePreview
                    :area="normalized"
                    :max-viewport-px="120"
                    :cell-size-min-px="10"
                    class="shrink-0"
                />
            </div>
        </template>
        <AreaDisplay v-else :area="normalized" icon-size="sm" />
    </div>
</template>
