<script setup>
/**
 * Bloc graphique de conversion (d → k) : fetch prévisualisation, chart en grand, options axes masquables.
 * Options à droite (desktop) ou en dessous (mobile), masquées par défaut.
 */
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import FormulaChart from '@/Pages/Admin/characteristics/FormulaChart.vue';

const props = defineProps({
    /** Formule de conversion (ex. floor([d]/10)). */
    formula: { type: String, default: '' },
    /** Bornes issues des échantillons { dMin, dMax, kMin, kMax }. */
    conversionBounds: { type: Object, default: null },
    characteristicKey: { type: String, required: true },
    entityKey: { type: String, default: '*' },
    chartXLabel: { type: String, default: 'd (Dofus)' },
    chartYLabel: { type: String, default: 'k (JDR)' },
    /** Hauteur du graphique en px. */
    chartHeight: { type: Number, default: 280 },
});

const points = ref([]);
const axisBounds = ref(null);
const loading = ref(false);
const showOptions = ref(false);
const overrideAxis = ref({ xMin: '', xMax: '', yMin: '', yMax: '' });
/** Options d'apparence du graphique (affichage uniquement, non enregistrées). */
const chartAppearance = ref({
    tension: 0.2,
    pointRadius: 2,
    showGrid: true,
    borderWidth: 2,
});
let debounceTimer = null;

function fetchPreview() {
    if (!props.characteristicKey || !props.entityKey) return;
    const params = {
        characteristic_id: props.characteristicKey,
        entity: props.entityKey,
    };
    if (props.conversionBounds && Number.isFinite(props.conversionBounds.dMin) && Number.isFinite(props.conversionBounds.dMax)) {
        params.d_min = props.conversionBounds.dMin;
        params.d_max = props.conversionBounds.dMax;
    }
    if (props.conversionBounds && Number.isFinite(props.conversionBounds.kMin) && Number.isFinite(props.conversionBounds.kMax)) {
        params.k_min = props.conversionBounds.kMin;
        params.k_max = props.conversionBounds.kMax;
    }
    if (props.formula != null && String(props.formula).trim() !== '') {
        params.conversion_formula = props.formula;
    }
    loading.value = true;
    axios
        .get(route('admin.dofus-conversion-formulas.formula-preview'), { params })
        .then((res) => {
            points.value = res.data.points ?? [];
            axisBounds.value = res.data.axisBounds ?? null;
            // Ne pas pré-remplir overrideAxis : on laisse FormulaChart appliquer withPadding(axisBounds)
            // par défaut. L'utilisateur peut saisir des bornes dans « Options » pour surcharger.
        })
        .catch(() => {
            points.value = [];
            axisBounds.value = null;
        })
        .finally(() => {
            loading.value = false;
        });
}

function debouncedFetch() {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        fetchPreview();
        debounceTimer = null;
    }, 400);
}

watch(
    () => [props.formula, props.conversionBounds, props.characteristicKey, props.entityKey],
    () => {
        debouncedFetch();
    },
    { immediate: true, deep: true }
);

const hasPoints = computed(() => Array.isArray(points.value) && points.value.length > 1);

const axisBoundsOverride = computed(() => {
    const o = overrideAxis.value;
    const xMin = o.xMin !== '' && o.xMin != null ? Number(o.xMin) : null;
    const xMax = o.xMax !== '' && o.xMax != null ? Number(o.xMax) : null;
    const yMin = o.yMin !== '' && o.yMin != null ? Number(o.yMin) : null;
    const yMax = o.yMax !== '' && o.yMax != null ? Number(o.yMax) : null;
    if (Number.isFinite(xMin) && Number.isFinite(xMax) && Number.isFinite(yMin) && Number.isFinite(yMax)) {
        return { xMin, xMax, yMin, yMax };
    }
    return null;
});

function onRefresh() {
    fetchPreview();
}
</script>

<template>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-stretch">
        <div class="min-h-0 min-w-0 flex-1 rounded-lg border border-base-300 bg-base-200/30 p-3 border-glass-sm relative overflow-hidden">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium">Aperçu ({{ chartXLabel }} → {{ chartYLabel }})</span>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="btn btn-ghost btn-xs"
                        :aria-expanded="showOptions"
                        :title="showOptions ? 'Masquer les options' : 'Options du graphique'"
                        @click="showOptions = !showOptions"
                    >
                        {{ showOptions ? 'Masquer options' : 'Options' }}
                    </button>
                    <button type="button" class="btn btn-ghost btn-xs" :disabled="loading" @click="onRefresh">
                        {{ loading ? '…' : 'Actualiser' }}
                    </button>
                </div>
            </div>
            <div v-if="loading" class="flex flex-1 items-center justify-center text-sm text-base-content/60" :style="{ minHeight: `${chartHeight}px` }">
                Chargement…
            </div>
            <div v-else class="w-full" :style="{ minHeight: `${chartHeight}px` }">
                <FormulaChart
                    :points="points"
                    :x-label="chartXLabel"
                    :y-label="chartYLabel"
                    :height="chartHeight"
                    :axis-bounds="axisBounds"
                    :axis-bounds-override="axisBoundsOverride"
                    :tension="chartAppearance.tension"
                    :point-radius="chartAppearance.pointRadius"
                    :point-hover-radius="Math.max(chartAppearance.pointRadius, 4)"
                    :border-width="chartAppearance.borderWidth"
                    :show-grid="chartAppearance.showGrid"
                />
            </div>
        </div>
        <div
            v-show="showOptions"
            class="flex flex-col gap-2 rounded-lg border border-base-300 bg-base-200/50 p-3 lg:w-48 lg:shrink-0 border-glass-sm relative overflow-hidden"
        >
            <span class="text-xs font-medium text-base-content/70">Bornes affichage (non enregistrées)</span>
            <div class="grid grid-cols-2 gap-2 text-xs lg:grid-cols-1">
                <label class="flex items-center gap-1">
                    <span class="w-8 shrink-0">X min</span>
                    <input v-model.number="overrideAxis.xMin" type="number" class="input input-bordered input-sm w-full" placeholder="auto" />
                </label>
                <label class="flex items-center gap-1">
                    <span class="w-8 shrink-0">X max</span>
                    <input v-model.number="overrideAxis.xMax" type="number" class="input input-bordered input-sm w-full" placeholder="auto" />
                </label>
                <label class="flex items-center gap-1">
                    <span class="w-8 shrink-0">Y min</span>
                    <input v-model.number="overrideAxis.yMin" type="number" class="input input-bordered input-sm w-full" placeholder="auto" />
                </label>
                <label class="flex items-center gap-1">
                    <span class="w-8 shrink-0">Y max</span>
                    <input v-model.number="overrideAxis.yMax" type="number" class="input input-bordered input-sm w-full" placeholder="auto" />
                </label>
            </div>
            <p class="text-xs text-base-content/50">Vide = automatique (samples + 10 %).</p>

            <span class="text-xs font-medium text-base-content/70 pt-1 border-t border-base-300 mt-2">Apparence (non enregistrée)</span>
            <div class="flex flex-col gap-2 text-xs">
                <label class="flex items-center justify-between gap-2">
                    <span>Tension courbe</span>
                    <select v-model.number="chartAppearance.tension" class="select select-bordered select-sm w-24">
                        <option :value="0">Droite</option>
                        <option :value="0.2">Lisse</option>
                        <option :value="0.4">Très lisse</option>
                    </select>
                </label>
                <label class="flex items-center justify-between gap-2">
                    <span>Points</span>
                    <select v-model.number="chartAppearance.pointRadius" class="select select-bordered select-sm w-24">
                        <option :value="0">Masqués</option>
                        <option :value="2">Petits</option>
                        <option :value="4">Moyens</option>
                        <option :value="6">Grands</option>
                    </select>
                </label>
                <label class="flex items-center justify-between gap-2">
                    <span>Épaisseur ligne</span>
                    <select v-model.number="chartAppearance.borderWidth" class="select select-bordered select-sm w-24">
                        <option :value="1">Fine</option>
                        <option :value="2">Normale</option>
                        <option :value="3">Épaisse</option>
                    </select>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="chartAppearance.showGrid" type="checkbox" class="checkbox checkbox-sm" />
                    <span>Grille</span>
                </label>
            </div>
        </div>
    </div>
</template>
