<script setup>
/**
 * Aperçu graphique d'une formule (points x/y) avec Chart.js.
 * Courbe type line, axes X (variable) et Y (résultat).
 */
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    points: {
        type: Array,
        default: () => [],
    },
    /** Hauteur minimale en px (le conteneur peut être plus grand via CSS). */
    height: { type: Number, default: 128 },
    /** Libellé axe X (ex. niveau, variable). */
    xLabel: { type: String, default: '' },
    /** Libellé axe Y (ex. résultat, valeur). */
    yLabel: { type: String, default: '' },
});

const canvasRef = ref(null);
/** @type {import('chart.js').Chart | null} */
let chartInstance = null;

const hasPoints = () => Array.isArray(props.points) && props.points.length > 1;

function buildChart() {
    if (!canvasRef.value || !hasPoints()) return;
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
    const labels = props.points.map((p) => String(p.x));
    const data = props.points.map((p) => p.y);
    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Résultat',
                    data,
                    borderColor: 'oklch(var(--p))',
                    backgroundColor: 'oklch(var(--p) / 0.1)',
                    fill: true,
                    tension: 0.2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.parsed.y}`,
                    },
                },
            },
            scales: {
                x: {
                    title: { display: !!props.xLabel, text: props.xLabel },
                    ticks: { maxTicksLimit: 8 },
                },
                y: {
                    title: { display: !!props.yLabel, text: props.yLabel },
                    beginAtZero: false,
                },
            },
        },
    });
}

function destroyChart() {
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
}

onMounted(() => {
    buildChart();
});

onBeforeUnmount(() => {
    destroyChart();
});

watch(
    () => props.points,
    () => {
        destroyChart();
        buildChart();
    },
    { deep: true }
);
</script>

<template>
    <div v-if="!hasPoints()" class="flex h-full items-center justify-center text-xs text-base-content/50">
        Aucun point (vérifiez la formule et les limites).
    </div>
    <div v-else class="relative h-full w-full" :style="{ minHeight: `${height}px` }">
        <canvas ref="canvasRef" />
    </div>
</template>
