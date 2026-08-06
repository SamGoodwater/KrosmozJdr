<script setup>
/**
 * AlmanaxHeaderBadge
 *
 * @description
 * Badge header Almanax : jour + mois Dofus, tooltip au survol, calendrier au clic.
 *
 * @example
 * <AlmanaxHeaderBadge />
 */
import { computed, ref } from "vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import AlmanaxMiniCalendar from "@/Pages/Molecules/header/AlmanaxMiniCalendar.vue";
import {
    getAlmanaxDate,
    getAlmanaxMonthIconUrl,
} from "@/Utils/layout/almanaxCalendar";

const imageFailed = ref(false);
const todayInfo = computed(() => getAlmanaxDate(new Date()));
const imageUrl = computed(() => getAlmanaxMonthIconUrl(todayInfo.value.month));
const shortLabel = computed(() => todayInfo.value.almanaxLabel);
const tooltipContent = computed(
    () => `${todayInfo.value.tooltip} — clique pour ouvrir le calendrier`,
);
</script>

<template>
    <Dropdown
        placement="bottom-end"
        size="sm"
        variant="glass"
        color="neutral"
        :close-on-content-click="false"
        :offset="10"
        class="rounded-box"
        aria-label="Ouvrir le calendrier Almanax"
    >
        <template #trigger>
            <Tooltip :content="tooltipContent" placement="bottom" color="neutral">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full border border-base-300 bg-base-100/70 px-2 py-1 text-xs text-base-content/85 shadow-sm backdrop-blur transition hover:border-primary/40 hover:bg-base-100 hover:text-base-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    :aria-label="`Almanax : ${shortLabel}`"
                >
                    <img
                        v-if="!imageFailed"
                        :src="imageUrl"
                        :alt="`Mois de ${todayInfo.month.name}`"
                        class="size-6 rounded-full object-cover"
                        loading="lazy"
                        @error="imageFailed = true"
                    >
                    <span
                        v-else
                        class="inline-flex size-6 items-center justify-center rounded-full bg-primary/10 text-primary"
                        aria-hidden="true"
                    >
                        <i class="fa-solid fa-calendar-day" />
                    </span>
                    <span class="whitespace-nowrap font-medium tabular-nums">
                        <span class="sm:hidden">{{ todayInfo.day }}</span>
                        <span class="hidden sm:inline">{{ shortLabel }}</span>
                    </span>
                </span>
            </Tooltip>
        </template>

        <template #content>
            <AlmanaxMiniCalendar />
        </template>
    </Dropdown>
</template>
