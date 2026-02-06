<script setup>
/**
 * Organisme : FormulaOrTableField + aperçu graphique (FormulaChart).
 * - Desktop (lg) : champ à gauche, graph à droite.
 * - Tablette / mobile : champ au-dessus, graph en dessous.
 * Le graph est chargé via l’API de prévisualisation (formule ou conversion Dofus).
 */
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import FormulaOrTableField from '@/Pages/Molecules/data-input/FormulaOrTableField.vue';
import FormulaChart from '@/Pages/Admin/characteristics/FormulaChart.vue';
import { decodeFormulaConfig, isFormulaTable } from '@/Utils/characteristic/formulaConfig';

const props = defineProps({
    /** Valeur formule (v-model). */
    modelValue: { type: String, default: '' },
    /** Options pour le select "Caractéristique de référence" dans la table. */
    characteristicOptions: { type: Array, default: () => [] },
    label: { type: String, default: '' },
    placeholder: { type: String, default: 'ex: [level]*2 ou 42' },
    /**
     * Config de l’aperçu. Si absent, le graph n’est pas affiché.
     * @type {{ characteristicKey: string, entity: string, variable?: string, mode: 'formula'|'conversion' }}
     */
    preview: { type: Object, default: null },
    /** Libellé axe X du graph (ex. "level", "d (Dofus)"). */
    chartXLabel: { type: String, default: 'level' },
    /** Libellé axe Y du graph. */
    chartYLabel: { type: String, default: 'Résultat' },
});

const emit = defineEmits(['update:modelValue']);

const points = ref([]);
const loading = ref(false);
let debounceTimer = null;

const xLabel = computed(() => {
    if (props.preview?.mode === 'conversion') return props.chartXLabel || 'd (Dofus)';
    if (props.modelValue && isFormulaTable(props.modelValue)) {
        const d = decodeFormulaConfig(props.modelValue);
        return d.characteristic || props.chartXLabel || 'level';
    }
    return props.chartXLabel || 'level';
});

function fetchPreview() {
    if (!props.preview?.characteristicKey || !props.preview?.entity) return;
    const routeName =
        props.preview.mode === 'conversion'
            ? 'admin.dofus-conversion-formulas.formula-preview'
            : 'admin.characteristics.formula-preview';
    const params = {
        characteristic_id: props.preview.characteristicKey,
        entity: props.preview.entity,
    };
    if (props.preview.mode === 'conversion') {
        if (props.modelValue != null && String(props.modelValue).trim() !== '') {
            params.conversion_formula = props.modelValue;
        }
    } else {
        params.variable = props.preview.variable ?? 'level';
        if (props.modelValue != null && String(props.modelValue).trim() !== '') {
            params.formula = props.modelValue;
        }
    }
    loading.value = true;
    axios
        .get(route(routeName), { params })
        .then((res) => {
            points.value = res.data.points ?? [];
        })
        .catch(() => {
            points.value = [];
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

const showChart = computed(() => props.preview != null);

watch(
    () => [props.modelValue, props.preview],
    () => {
        if (!showChart.value) return;
        debouncedFetch();
    },
    { immediate: true, deep: true }
);

function onRefresh() {
    fetchPreview();
}

const hasPoints = computed(() => Array.isArray(points.value) && points.value.length > 1);
</script>

<template>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-stretch">
        <!-- Champ formule : à gauche sur desktop, en haut sur mobile -->
        <div class="min-w-0 flex-1">
            <FormulaOrTableField
                :model-value="modelValue"
                @update:model-value="emit('update:modelValue', $event)"
                :characteristic-options="characteristicOptions"
                :label="label"
                :placeholder="placeholder"
            />
        </div>
        <!-- Graph : à droite sur desktop, en bas sur mobile -->
        <div
            v-if="showChart"
            class="flex min-h-32 flex-1 flex-col rounded-lg border border-base-300 bg-base-200/30 p-3 lg:min-w-[280px]"
        >
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium">Aperçu (variable : {{ xLabel }})</span>
                <button
                    type="button"
                    class="btn btn-ghost btn-xs"
                    :disabled="loading"
                    @click="onRefresh"
                >
                    {{ loading ? 'Chargement…' : 'Actualiser' }}
                </button>
            </div>
            <div v-if="loading" class="flex flex-1 items-center justify-center text-sm text-base-content/60">
                Chargement…
            </div>
            <div v-else class="h-32 min-h-32 w-full flex-1">
                <FormulaChart
                    :points="points"
                    :x-label="xLabel"
                    :y-label="chartYLabel"
                    :height="128"
                />
            </div>
        </div>
    </div>
</template>
