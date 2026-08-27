<script setup>
/**
 * Lecture du tableau vivant des bonus d’équipement (un tableau par emplacement).
 *
 * @example
 * <SectionEquipmentBonusTableRead :section="section" :settings="{}" />
 */
import { onMounted, ref } from "vue";
import axios from "axios";
import { getCharacteristicColorStyle } from "@/Utils/color/Color";

defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const loading = ref(false);
const error = ref(null);
const bands = ref([]);
const groups = ref([]);

async function fetchTable() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await axios.get("/api/characteristics/equipment-bonus-table");
        bands.value = Array.isArray(data?.bands) ? data.bands : [];
        groups.value = Array.isArray(data?.groups) ? data.groups : [];
    } catch (e) {
        error.value = resolveLoadError(e);
        bands.value = [];
        groups.value = [];
    } finally {
        loading.value = false;
    }
}

/**
 * @param {unknown} value
 * @returns {string}
 */
function fmtNumber(value) {
    if (value === null || value === undefined || value === "") {
        return "—";
    }
    const n = Number(value);
    if (!Number.isFinite(n)) {
        return String(value);
    }
    return new Intl.NumberFormat("fr-FR").format(n);
}

/**
 * @param {unknown} value
 * @returns {string}
 */
function fmtBand(value) {
    if (value === null || value === undefined || value === "") {
        return "—";
    }
    return fmtNumber(value);
}

/**
 * @param {unknown} e
 * @returns {string}
 */
function resolveLoadError(e) {
    const apiError = e?.response?.data?.error;
    if (typeof apiError === "string" && apiError.trim() !== "") {
        return apiError;
    }
    const status = e?.response?.status;
    if (status === 403) {
        return "Ce tableau est réservé aux meneurs de jeu.";
    }
    if (Number.isFinite(status)) {
        return `Impossible de charger le tableau des bonus d’équipement (HTTP ${status}).`;
    }
    return "Impossible de charger le tableau des bonus d’équipement.";
}

onMounted(fetchTable);
</script>

<template>
    <div class="space-y-6">
        <div v-if="loading" class="flex items-center justify-center py-8">
            <span class="loading loading-spinner loading-md" />
        </div>

        <div v-else-if="error" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>{{ error }}</span>
        </div>

        <div v-else-if="groups.length === 0" class="alert alert-info">
            <i class="fa-solid fa-circle-info" />
            <span>Aucun bonus d’équipement à afficher.</span>
        </div>

        <section
            v-for="group in groups"
            :key="group.item_type_id ?? 'all-types'"
            class="space-y-2"
        >
            <h3 class="text-lg font-semibold">{{ group.item_type_name }}</h3>
            <div class="overflow-x-auto rounded-box border border-base-content/20 bg-base-100/40">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr class="bg-base-200/95">
                            <th class="sticky left-0 z-10 min-w-40 bg-inherit">Caractéristique</th>
                            <th
                                v-for="band in bands"
                                :key="band.label"
                                class="whitespace-nowrap text-center"
                            >
                                {{ band.label }}
                            </th>
                            <th class="whitespace-nowrap text-end">Prix / unité</th>
                            <th class="whitespace-nowrap text-end">FM max</th>
                            <th class="whitespace-nowrap text-end">Prix rune</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in group.rows" :key="`${group.item_type_id ?? 'all'}-${row.key}`">
                            <th class="sticky left-0 z-10 bg-inherit font-medium">
                                <span
                                    class="inline-flex items-center gap-2"
                                    :style="getCharacteristicColorStyle(row.color) || {}"
                                >
                                    {{ row.name }}
                                </span>
                            </th>
                            <td
                                v-for="(band, index) in bands"
                                :key="`${row.key}-${band.label}`"
                                class="text-center tabular-nums"
                            >
                                {{ fmtBand(row.bands?.[index]) }}
                            </td>
                            <td class="text-end tabular-nums">{{ fmtNumber(row.price_per_unit) }}</td>
                            <td class="text-end tabular-nums">{{ fmtNumber(row.forgemagie_max) }}</td>
                            <td class="text-end tabular-nums">{{ fmtNumber(row.rune_price) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
