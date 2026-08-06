<script setup>
/**
 * AlmanaxMiniCalendar
 *
 * @description
 * Mini-calendrier Almanax (mois Dofus, jours = calendrier réel) pour le popover du header.
 *
 * @example
 * <AlmanaxMiniCalendar />
 */
import { computed, ref } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    ALMANAX_MONTHS,
    ALMANAX_WEEKDAY_HEADERS,
    buildAlmanaxMonthGrid,
    getAlmanaxMonth,
    getAlmanaxMonthIconUrl,
    shiftAlmanaxMonth,
} from "@/Utils/layout/almanaxCalendar";

const today = new Date();
const viewYear = ref(today.getFullYear());
const viewMonthIndex = ref(today.getMonth());
const imageFailed = ref(false);

const month = computed(() => ALMANAX_MONTHS[viewMonthIndex.value] ?? getAlmanaxMonth(today));
const imageUrl = computed(() => getAlmanaxMonthIconUrl(month.value));
const weeks = computed(() => buildAlmanaxMonthGrid(viewYear.value, viewMonthIndex.value));
const isCurrentMonth = computed(
    () => viewYear.value === today.getFullYear() && viewMonthIndex.value === today.getMonth(),
);
const subtitle = computed(
    () => `${month.value.gregorianMonth} ${viewYear.value} · jours = calendrier réel`,
);

function go(delta) {
    imageFailed.value = false;
    const next = shiftAlmanaxMonth(viewYear.value, viewMonthIndex.value, delta);
    viewYear.value = next.year;
    viewMonthIndex.value = next.monthIndex;
}

function goToday() {
    imageFailed.value = false;
    viewYear.value = today.getFullYear();
    viewMonthIndex.value = today.getMonth();
}
</script>

<template>
    <div
        class="w-[min(100vw-2rem,18rem)] space-y-3 rounded-box border border-base-300/40 bg-base-100/90 p-3 text-base-content shadow-lg backdrop-blur-md"
        role="dialog"
        aria-label="Calendrier Almanax"
    >
        <header class="flex items-center gap-2">
            <img
                v-if="!imageFailed"
                :src="imageUrl"
                :alt="`Mois de ${month.name}`"
                class="size-9 shrink-0 rounded-full object-cover ring-1 ring-base-300"
                loading="lazy"
                @error="imageFailed = true"
            >
            <span
                v-else
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"
                aria-hidden="true"
            >
                <i class="fa-solid fa-calendar-day" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold leading-tight">
                    {{ month.name }}
                </p>
                <p class="truncate text-[11px] text-base-content/60">
                    {{ subtitle }}
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-0.5">
                <Tooltip content="Mois précédent" placement="top">
                    <button
                        type="button"
                        class="btn btn-ghost btn-xs btn-square"
                        aria-label="Mois précédent"
                        @click="go(-1)"
                    >
                        <i class="fa-solid fa-chevron-left" aria-hidden="true" />
                    </button>
                </Tooltip>
                <Tooltip content="Revenir à aujourd’hui" placement="top">
                    <button
                        type="button"
                        class="btn btn-ghost btn-xs"
                        :class="{ 'btn-active': isCurrentMonth }"
                        aria-label="Aujourd’hui"
                        @click="goToday"
                    >
                        <i class="fa-solid fa-circle-dot text-[10px]" aria-hidden="true" />
                    </button>
                </Tooltip>
                <Tooltip content="Mois suivant" placement="top">
                    <button
                        type="button"
                        class="btn btn-ghost btn-xs btn-square"
                        aria-label="Mois suivant"
                        @click="go(1)"
                    >
                        <i class="fa-solid fa-chevron-right" aria-hidden="true" />
                    </button>
                </Tooltip>
            </div>
        </header>

        <div class="grid grid-cols-7 gap-0.5 text-center text-[10px] font-medium uppercase tracking-wide text-base-content/50">
            <span v-for="label in ALMANAX_WEEKDAY_HEADERS" :key="label">{{ label }}</span>
        </div>

        <div class="grid grid-cols-7 gap-0.5" role="grid" :aria-label="`Mois de ${month.name}`">
            <template v-for="(week, wi) in weeks" :key="`w-${wi}`">
                <Tooltip
                    v-for="(cell, di) in week"
                    :key="`d-${wi}-${di}`"
                    :content="cell ? cell.almanaxLabel : ''"
                    placement="top"
                >
                    <span
                        role="gridcell"
                        class="inline-flex aspect-square items-center justify-center rounded-md text-xs"
                        :class="{
                            'text-base-content/30': cell && !cell.inMonth,
                            'text-base-content/80': cell && cell.inMonth && !cell.isToday,
                            'bg-primary text-primary-content font-semibold shadow-sm': cell?.isToday,
                            'hover:bg-base-200': cell && !cell.isToday,
                        }"
                        :aria-current="cell?.isToday ? 'date' : undefined"
                        :aria-label="cell?.almanaxLabel"
                    >
                        {{ cell?.day ?? "" }}
                    </span>
                </Tooltip>
            </template>
        </div>

        <p class="text-[11px] leading-snug text-base-content/55">
            Les jours correspondent au calendrier réel. Seuls les mois portent les noms du Monde des Douze.
        </p>
    </div>
</template>
