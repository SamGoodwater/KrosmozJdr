<script setup>
/**
 * Récapitulatif administration — utilisateurs et rôles.
 */
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Legend, Tooltip } from "chart.js";
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import AdminDoughnutChart from "@/Pages/Molecules/data-display/AdminDoughnutChart.vue";

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Legend, Tooltip);

defineOptions({ layout: AdminArea });

const props = defineProps({
    recap: { type: Object, required: true },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Récapitulatif administration");

const usersByRole = computed(() => props.recap?.usersByRole ?? []);
const userGrowth = computed(() => props.recap?.userGrowth ?? []);
const totalUsers = computed(() => props.recap?.totals?.users ?? 0);

const roleChart = computed(() => ({
    labels: usersByRole.value.map((r) => r.label),
    values: usersByRole.value.map((r) => r.count),
    colors: ["#94a3b8", "#60a5fa", "#a78bfa", "#34d399", "#fbbf24", "#f472b6"],
}));

const growthCanvas = ref(null);
/** @type {import('chart.js').Chart | null} */
let growthChart = null;

const buildGrowthChart = () => {
    if (!growthCanvas.value) return;
    growthChart?.destroy();
    growthChart = new Chart(growthCanvas.value, {
        type: "bar",
        data: {
            labels: userGrowth.value.map((r) => r.month),
            datasets: [
                {
                    label: "Inscriptions",
                    data: userGrowth.value.map((r) => r.count),
                    backgroundColor: "color-mix(in srgb, hsl(var(--p)) 55%, transparent)",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
            },
        },
    });
};

onMounted(buildGrowthChart);
watch(userGrowth, buildGrowthChart, { deep: true });
onBeforeUnmount(() => growthChart?.destroy());
</script>

<template>
    <Head title="Récapitulatif administration" />

    <div class="space-y-6 pb-8">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Récapitulatif</h1>
            <p class="mt-2 text-sm text-base-content/70 max-w-3xl">
                Évolution des inscriptions et répartition des comptes par rôle. Zone protégée par confirmation du mot de passe.
            </p>
        </div>

        <div class="rounded-box border border-base-300 bg-base-100/50 p-4">
            <p class="text-xs uppercase tracking-wide text-base-content/60">Utilisateurs enregistrés</p>
            <p class="text-3xl font-bold text-primary">{{ totalUsers }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-box border border-base-300 bg-base-100/40 p-4">
                <AdminDoughnutChart
                    v-bind="roleChart"
                    title="Répartition par rôle"
                    :height="260"
                />
            </div>
            <div class="rounded-box border border-base-300 bg-base-100/40 p-4">
                <p class="text-sm font-semibold text-base-content mb-2">Inscriptions (12 derniers mois)</p>
                <div class="h-[260px]">
                    <canvas ref="growthCanvas" />
                </div>
            </div>
        </div>
    </div>
</template>
