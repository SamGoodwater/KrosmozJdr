<script setup>
/**
 * Lecture du tableau des runes de forgemagie.
 *
 * Les lignes viennent du référentiel des caractéristiques : toute caractéristique
 * objet forgemageable et dotée d'un prix de rune devient une ligne.
 *
 * @example
 * <SectionForgemagieRuneTableRead :section="section" :settings="{ sort_by: 'rune_price' }" />
 */
import { computed, onMounted, ref, watch } from "vue";
import axios from "axios";
import { getCharacteristicColorStyle } from "@/Utils/color/Color";

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const loading = ref(false);
const error = ref(null);
const rows = ref([]);
const priceNotice = ref("");

const showBasePrice = computed(() => Boolean(props.settings?.show_base_price));

async function fetchTable() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await axios.get("/api/characteristics/forgemagie-rune-table", {
            params: {
                sort_by: props.settings?.sort_by || "name",
                sort_dir: props.settings?.sort_dir || "asc",
            },
        });
        rows.value = Array.isArray(data?.rows) ? data.rows : [];
        priceNotice.value = data?.meta?.price_notice || "";
    } catch (e) {
        error.value = resolveLoadError(e);
        rows.value = [];
        priceNotice.value = "";
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
function fmtBonus(value) {
    const n = Number(value);
    if (!Number.isFinite(n) || n <= 0) {
        return "—";
    }
    return `+${new Intl.NumberFormat("fr-FR").format(n)}`;
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
    if (Number.isFinite(status)) {
        return `Impossible de charger le tableau des runes (HTTP ${status}).`;
    }
    return "Impossible de charger le tableau des runes.";
}

watch(
    () => [props.settings?.sort_by, props.settings?.sort_dir],
    () => fetchTable(),
);

onMounted(fetchTable);
</script>

<template>
    <div class="space-y-3">
        <div v-if="loading" class="flex items-center justify-center py-8">
            <span class="loading loading-spinner loading-md" />
        </div>

        <div v-else-if="error" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>{{ error }}</span>
        </div>

        <div v-else-if="rows.length === 0" class="alert alert-info">
            <i class="fa-solid fa-circle-info" />
            <span>Aucune rune de forgemagie à afficher.</span>
        </div>

        <template v-else>
            <div class="overflow-x-auto rounded-box border border-base-content/20 bg-base-100/40">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr class="bg-base-200/95">
                            <th class="sticky left-0 z-10 min-w-48 bg-inherit">Rune</th>
                            <th class="whitespace-nowrap text-end">Bonus max</th>
                            <th v-if="showBasePrice" class="whitespace-nowrap text-end">
                                Prix sur équipement
                            </th>
                            <th class="whitespace-nowrap text-end">Prix de la rune</th>
                            <th class="min-w-48">Équipements</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in rows" :key="row.key">
                            <th class="sticky left-0 z-10 bg-inherit font-medium">
                                <span
                                    class="inline-flex items-center gap-2"
                                    :style="getCharacteristicColorStyle(row.color) || {}"
                                >
                                    {{ row.name }}
                                </span>
                            </th>
                            <td class="text-end tabular-nums">{{ fmtBonus(row.max_bonus) }}</td>
                            <td v-if="showBasePrice" class="text-end tabular-nums">
                                {{ fmtNumber(row.base_price) }}
                            </td>
                            <td class="text-end tabular-nums">{{ fmtNumber(row.rune_price) }}</td>
                            <td>
                                <span v-if="!row.restricted" class="opacity-70">Tous</span>
                                <span v-else class="flex flex-wrap gap-1">
                                    <span
                                        v-for="type in row.item_types"
                                        :key="`${row.key}-${type}`"
                                        class="badge badge-ghost badge-sm"
                                    >
                                        {{ type }}
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-if="priceNotice" class="text-xs opacity-70">{{ priceNotice }}</p>
        </template>
    </div>
</template>
