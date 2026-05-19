<script setup>
/**
 * Graphique en anneau (Chart.js) pour les tableaux de bord admin.
 */
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { Chart, ArcElement, DoughnutController, Legend, Tooltip } from "chart.js";

Chart.register(ArcElement, DoughnutController, Legend, Tooltip);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    values: { type: Array, default: () => [] },
    colors: { type: Array, default: () => [] },
    title: { type: String, default: "" },
    height: { type: Number, default: 200 },
});

const canvasRef = ref(null);
/** @type {import('chart.js').Chart | null} */
let chartInstance = null;

const hasData = computed(() =>
    props.values.some((v) => Number(v) > 0)
);

const buildChart = () => {
    if (!canvasRef.value) return;
    chartInstance?.destroy();
    chartInstance = new Chart(canvasRef.value, {
        type: "doughnut",
        data: {
            labels: props.labels,
            datasets: [
                {
                    data: props.values.map((v) => Number(v) || 0),
                    backgroundColor: props.colors,
                    borderWidth: 1,
                    borderColor: "rgba(0,0,0,0.08)",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: "bottom", labels: { boxWidth: 12, font: { size: 11 } } },
            },
        },
    });
};

onMounted(buildChart);
watch(() => [props.labels, props.values, props.colors], buildChart, { deep: true });
onBeforeUnmount(() => chartInstance?.destroy());
</script>

<template>
    <div class="space-y-2">
        <p v-if="title" class="text-sm font-semibold text-base-content">{{ title }}</p>
        <div v-if="hasData" :style="{ height: `${height}px` }">
            <canvas ref="canvasRef" />
        </div>
        <p v-else class="text-xs text-base-content/50 italic">Aucune donnée</p>
    </div>
</template>
