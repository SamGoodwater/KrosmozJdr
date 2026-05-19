<script setup>
/**
 * Vue d’ensemble — gestion du contenu (camemberts entités × statuts, CMS).
 */
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import AdminDoughnutChart from "@/Pages/Molecules/data-display/AdminDoughnutChart.vue";

defineOptions({ layout: AdminArea });

const props = defineProps({
    overview: { type: Object, required: true },
    stateLabels: { type: Object, required: true },
    stateColors: { type: Object, required: true },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Gestion du contenu");

const cms = computed(() => props.overview?.cms ?? { pages: 0, sections: 0 });
const entities = computed(() => props.overview?.entities ?? []);

const chartForEntity = (entity) => {
    const labels = Object.keys(props.stateLabels).map((k) => props.stateLabels[k]);
    const values = Object.keys(props.stateLabels).map((k) => entity.byState?.[k] ?? 0);
    const colors = Object.keys(props.stateLabels).map((k) => props.stateColors[k] ?? "#94a3b8");
    return { labels, values, colors };
};
</script>

<template>
    <Head title="Gestion du contenu" />

    <div class="space-y-6 pb-8">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Gestion du contenu</h1>
            <p class="mt-2 text-sm text-base-content/70 max-w-3xl">
                Vue d’ensemble des entités par statut et du contenu CMS (pages et sections).
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-box border border-base-300 bg-base-100/50 p-4">
                <p class="text-xs uppercase tracking-wide text-base-content/60">Pages CMS</p>
                <p class="text-3xl font-bold text-primary">{{ cms.pages }}</p>
            </div>
            <div class="rounded-box border border-base-300 bg-base-100/50 p-4">
                <p class="text-xs uppercase tracking-wide text-base-content/60">Sections</p>
                <p class="text-3xl font-bold text-primary">{{ cms.sections }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div
                v-for="entity in entities"
                :key="entity.key"
                class="rounded-box border border-base-300 bg-base-100/40 p-3"
            >
                <div class="flex items-center justify-between gap-2 mb-2">
                    <h2 class="text-sm font-semibold">{{ entity.label }}</h2>
                    <span class="badge badge-ghost badge-sm">{{ entity.total }}</span>
                </div>
                <AdminDoughnutChart
                    v-bind="chartForEntity(entity)"
                    :title="''"
                    :height="180"
                />
            </div>
        </div>
    </div>
</template>
