<script setup>
/**
 * AlmanaxHeaderBadge
 *
 * @description
 * Badge discret du header affichant le mois Almanax/Dofus correspondant au mois réel.
 *
 * @example
 * <AlmanaxHeaderBadge />
 */
import { computed, ref } from "vue";
import { getAlmanaxMonth, getAlmanaxMonthIconUrl } from "@/Utils/layout/almanaxCalendar";

const imageFailed = ref(false);
const today = new Date();
const month = computed(() => getAlmanaxMonth(today));
const dayLabel = computed(() => today.toLocaleDateString("fr-FR", { day: "2-digit", month: "long" }));
const imageUrl = computed(() => getAlmanaxMonthIconUrl(month.value));
const title = computed(() => `Almanax : ${dayLabel.value}, mois de ${month.value.name}`);
</script>

<template>
    <div
        class="hidden md:flex items-center gap-2 rounded-full border border-base-300 bg-base-100/60 px-2 py-1 text-xs text-base-content/80 shadow-sm backdrop-blur"
        :title="title"
        aria-label="Mois Almanax du jour"
    >
        <img
            v-if="!imageFailed"
            :src="imageUrl"
            :alt="`Mois de ${month.name}`"
            class="size-6 rounded-full object-cover"
            loading="lazy"
            @error="imageFailed = true"
        >
        <span v-else class="inline-flex size-6 items-center justify-center rounded-full bg-primary/10 text-primary">
            <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
        </span>
        <span class="hidden lg:inline whitespace-nowrap">{{ month.name }}</span>
    </div>
</template>
