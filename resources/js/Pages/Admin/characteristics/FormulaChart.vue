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
    /** Bornes des axes { xMin, xMax, yMin, yMax }. Si fourni, fixe l'échelle (marge ~5 % au min, ~15 % au max). Surchargé par axisBoundsOverride si fourni. */
    axisBounds: { type: Object, default: null },
    /** Surcharge manuelle des bornes (affichage uniquement, ex. options du graphique). Prioritaire sur axisBounds. */
    axisBoundsOverride: { type: Object, default: null },
    /** Tension de la courbe (0 = segments droits, 0.4 = très lisse). */
    tension: { type: Number, default: 0.2 },
    /** Rayon des points (0 = masqués). */
    pointRadius: { type: Number, default: 2 },
    /** Rayon des points au survol. */
    pointHoverRadius: { type: Number, default: 4 },
    /** Épaisseur de la ligne en px. */
    borderWidth: { type: Number, default: 2 },
    /** Afficher la grille des axes. */
    showGrid: { type: Boolean, default: true },
});

const canvasRef = ref(null);
/** @type {import('chart.js').Chart | null} */
let chartInstance = null;

const hasPoints = () => Array.isArray(props.points) && props.points.length > 1;

/** Étend les bornes : ~5 % en min, ~15 % en max (5 % + 10 % en plus au max) pour que les extrêmes restent visibles. */
function withPadding(bounds) {
    if (!bounds) return null;
    const padMin = 0.05;
    const padMax = 0.15; // 5 % + 10 % en plus au max en x et y
    let xMin = bounds.xMin;
    let xMax = bounds.xMax;
    let yMin = bounds.yMin;
    let yMax = bounds.yMax;
    if (Number.isFinite(xMin) && Number.isFinite(xMax)) {
        const range = Math.max(xMax - xMin, 1);
        xMin = xMin - range * padMin;
        xMax = xMax + range * padMax;
    }
    if (Number.isFinite(yMin) && Number.isFinite(yMax)) {
        const range = Math.max(yMax - yMin, 1);
        yMin = yMin - range * padMin;
        yMax = yMax + range * padMax;
    }
    return { xMin, xMax, yMin, yMax };
}

function buildChart() {
    if (!canvasRef.value || !hasPoints()) return;
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
    const labels = props.points.map((p) => String(p.x));
    const data = props.points.map((p) => p.y);
    const effectiveBounds = props.axisBoundsOverride && (Number.isFinite(props.axisBoundsOverride.xMin) || Number.isFinite(props.axisBoundsOverride.yMin))
        ? props.axisBoundsOverride
        : withPadding(props.axisBounds);
    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Résultat',
                    data,
                    borderColor: 'var(--color, oklch(var(--p)))',
                    backgroundColor: 'var(--chart-fill, oklch(var(--p) / 0.1))',
                    fill: true,
                    tension: Math.max(0, Math.min(1, props.tension)),
                    pointRadius: Math.max(0, props.pointRadius),
                    pointHoverRadius: Math.max(0, props.pointHoverRadius),
                    borderWidth: Math.max(0, props.borderWidth),
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
                    grid: { display: props.showGrid },
                    ...(effectiveBounds && Number.isFinite(effectiveBounds.xMin) && Number.isFinite(effectiveBounds.xMax)
                        ? { min: effectiveBounds.xMin, max: effectiveBounds.xMax }
                        : {}),
                },
                y: {
                    title: { display: !!props.yLabel, text: props.yLabel },
                    beginAtZero: false,
                    grid: { display: props.showGrid },
                    ...(effectiveBounds && Number.isFinite(effectiveBounds.yMin) && Number.isFinite(effectiveBounds.yMax)
                        ? { min: effectiveBounds.yMin, max: effectiveBounds.yMax }
                        : {}),
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
    () => [
        props.points,
        props.axisBounds,
        props.axisBoundsOverride,
        props.tension,
        props.pointRadius,
        props.pointHoverRadius,
        props.borderWidth,
        props.showGrid,
    ],
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
