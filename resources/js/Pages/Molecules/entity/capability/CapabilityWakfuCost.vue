<script setup>
/**
 * Alias capacité de {@link ActionPointCost} (PA depuis la réserve par défaut).
 *
 * @deprecated Préférer `ActionPointCost` avec `fromWakfuReserve` explicite.
 */
import ActionPointCost from "@/Pages/Molecules/entity/shared/ActionPointCost.vue";

defineProps({
    value: { type: [String, Number], default: null },
    iconPx: { type: String, default: "14" },
    /** Surcharge du mode réserve (défaut true pour les capacités). */
    fromWakfuReserve: { type: Boolean, default: true },
});
</script>

<template>
    <ActionPointCost :value="value" :from-wakfu-reserve="fromWakfuReserve" :icon-px="iconPx" />
</template>
