<script setup>
/**
 * Vue d’ensemble — gestion du contenu (camemberts entités × statuts, CMS).
 */
import { computed } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useProjectConsoleJob } from "@/Composables/admin/useProjectConsoleJob";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import AdminDoughnutChart from "@/Pages/Molecules/data-display/AdminDoughnutChart.vue";
import AdminConsoleJobPanel from "@/Pages/Admin/_components/AdminConsoleJobPanel.vue";
import AdminCommandMeta from "@/Pages/Admin/_components/AdminCommandMeta.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

defineOptions({ layout: AdminArea });

const props = defineProps({
    overview: { type: Object, required: true },
    stateLabels: { type: Object, required: true },
    stateColors: { type: Object, required: true },
    rulesDownloads: { type: Object, default: () => ({ generated_at: null, available: 0, missing: 0 }) },
    consoleJob: { type: Object, default: null },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Gestion du contenu");

const page = usePage();
const form = useForm({});
const { liveJob, pollError, busy, cancelJob, cancelling } = useProjectConsoleJob(props, {
    title: "Compilation du livre de règles",
});

const cms = computed(() => props.overview?.cms ?? { pages: 0, sections: 0 });
const entities = computed(() => props.overview?.entities ?? []);

const generatedLabel = computed(() => {
    const iso = props.rulesDownloads?.generated_at;
    if (!iso) {
        return "Jamais compilé";
    }
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) {
        return "Jamais compilé";
    }
    return d.toLocaleString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

const chartForEntity = (entity) => {
    const labels = Object.keys(props.stateLabels).map((k) => props.stateLabels[k]);
    const values = Object.keys(props.stateLabels).map((k) => entity.byState?.[k] ?? 0);
    const colors = Object.keys(props.stateLabels).map((k) => props.stateColors[k] ?? "#94a3b8");
    return { labels, values, colors };
};

function submitCompile() {
    form.post(route("admin.content.rules-downloads.run"), { preserveScroll: true });
}
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

        <p
            v-if="page.props.flash?.success"
            class="text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2"
        >
            {{ page.props.flash.success }}
        </p>
        <p
            v-if="page.props.flash?.error"
            class="text-error text-sm rounded-box border border-error/30 bg-error/10 px-3 py-2"
        >
            {{ page.props.flash.error }}
        </p>

        <section class="rounded-box border border-base-300 bg-base-100/50 p-4 space-y-3">
            <h2 class="text-lg font-semibold">Livre de règles (PDF et ODT)</h2>
            <p class="text-sm text-base-content/70 max-w-3xl">
                Compile les chapitres Markdown en fichiers téléchargeables, stockés sur le disque public.
                Les joueurs les récupèrent depuis la page Ressources du menu Règles. La génération n’a lieu
                que sur ce bouton, après un import des règles, ou via
                <code class="rounded bg-base-300 px-1">php artisan rules:compile-downloads</code>.
            </p>
            <AdminCommandMeta signature="rules:compile-downloads" />
            <p class="text-sm">
                Dernière compilation :
                <strong>{{ generatedLabel }}</strong>
                <span class="opacity-70">
                    ({{ rulesDownloads.available ?? 0 }} fichier(s) prêt(s)
                    <template v-if="rulesDownloads.missing">, {{ rulesDownloads.missing }} manquant(s)</template>)
                </span>
            </p>
            <Btn
                type="button"
                color="primary"
                :disabled="busy || form.processing"
                @click="submitCompile"
            >
                {{ busy || form.processing ? "Compilation en cours…" : "Compiler le livre de règles" }}
            </Btn>
            <AdminConsoleJobPanel
                :job="liveJob"
                :poll-error="pollError"
                :cancelling="cancelling"
                @cancel="cancelJob"
            />
        </section>

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
