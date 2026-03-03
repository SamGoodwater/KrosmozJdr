<script setup>
/**
 * ScrappingSearchTableSection
 *
 * @description
 * Recherche par filtres via `/api/scrapping/search/{entity}` + affichage en table + import batch.
 * Conçu pour le mode "scrapper une grosse partie d'une entité".
 */
import { computed, ref, watch } from "vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import Alert from "@/Pages/Atoms/feedback/Alert.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import TanStackTable from "@/Pages/Organismes/table/TanStackTable.vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { downloadCsvFromRows, filenameForBatchErrors, filenameForBatchPreview, buildCsvFromErrorResults, buildCsvFromPreviewResults } from "@/Composables/utils/useCsvDownload";

const props = defineProps({
    entityType: { type: String, required: true },
    /**
     * Ex: [{key:'name',type:'string'}, {key:'ids',type:'number[]'}]
     */
    supportedFilters: { type: Array, default: () => [] },
    /**
     * Options import globales depuis ScrappingSection (skipCache, dryRun, includeRelations, ...)
     */
    importOptions: { type: Object, required: true },
    /**
     * Désactive la section si le backend ne supporte pas /search pour ce type
     */
    enabled: { type: Boolean, default: true },
});

const emit = defineEmits(["preview-selected"]);

const notificationStore = useNotificationStore();
const { success, error: showError } = notificationStore;

const searching = ref(false);
const importing = ref(false);
const lastResult = ref(null);
const selectedIds = ref([]);
const lastBatchResults = ref(null);
const lastBatchErrorResults = computed(() => {
    const list = lastBatchResults.value;
    if (!Array.isArray(list)) return [];
    return list.filter((r) => r && r.success === false);
});

/** Prévisualisation batch : chargement + résultats (ID, nom, statut, message). */
const batchPreviewLoading = ref(false);
const batchPreviewResults = ref([]);
const nameByIdFromSearch = computed(() => {
    const items = lastResult.value?.data?.items || [];
    const map = {};
    items.forEach((it) => {
        if (it && Number.isFinite(Number(it.id))) map[Number(it.id)] = normalizeName(it.name);
    });
    return map;
});

const filterValues = ref({
    id: "",
    ids: "",
    name: "",
    typeId: "",
    raceId: "",
    breedId: "",
    levelMin: "",
    levelMax: "",
});

const paging = ref({
    limit: 50,
    start_skip: 0,
    max_pages: 1,
    max_items: 200,
});

watch(
    () => props.entityType,
    () => {
        selectedIds.value = [];
        lastResult.value = null;
        lastBatchResults.value = null;
        paging.value.start_skip = 0;
    }
);

const supportedKeys = computed(() => {
    const arr = Array.isArray(props.supportedFilters) ? props.supportedFilters : [];
    return new Set(arr.map((f) => String(f?.key || "")).filter(Boolean));
});

const hasFilter = (key) => supportedKeys.value.has(key);

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

const buildQuery = () => {
    const q = new URLSearchParams();

    // Options paging
    q.set("limit", String(paging.value.limit));
    q.set("start_skip", String(paging.value.start_skip));
    q.set("max_pages", String(paging.value.max_pages));
    q.set("max_items", String(paging.value.max_items));

    if (props.importOptions?.skipCache) q.set("skip_cache", "true");

    // Filtres (uniquement si supportés)
    for (const k of ["id", "ids", "name", "typeId", "raceId", "breedId", "levelMin", "levelMax"]) {
        if (!hasFilter(k) && k !== "ids") continue;
        const v = String(filterValues.value?.[k] ?? "").trim();
        if (!v) continue;
        q.set(k, v);
    }

    return q.toString();
};

const normalizeName = (obj) => {
    if (!obj) return "—";
    if (typeof obj === "string") return obj;
    if (typeof obj === "object") return obj.fr || obj.en || obj.de || obj.es || obj.pt || "—";
    return "—";
};

const tableColumns = computed(() => {
    const t = String(props.entityType || "");
    // Colonnes minimales, adaptables par type
    if (t === "monster") return ["id", "name", "level", "raceId"];
    if (t === "item") return ["id", "name", "typeId", "level"];
    if (t === "spell") return ["id", "name", "breedId"];
    if (t === "class") return ["id", "name"];
    if (t === "panoply") return ["id", "name", "level"];
    return ["id", "name"];
});

const tableConfig = computed(() => {
    const cols = tableColumns.value.map((id) => ({
        id,
        label: id,
        hideable: true,
        sort: { enabled: false },
        search: { enabled: false },
        cell: { type: "text" },
    }));

    return {
        ui: { size: "sm", color: "primary", tableVariant: "zebra" },
        columns: cols,
        features: {
            selection: { enabled: true, clickToSelect: true },
            export: { csv: false },
        },
    };
});

const rows = computed(() => {
    const items = lastResult.value?.data?.items || [];
    if (!Array.isArray(items)) return [];

    return items
        .filter((it) => it && typeof it === "object")
        .map((it) => {
            const id = Number(it.id);
            const cells = {
                id: { type: "text", value: Number.isFinite(id) ? id : "—", params: { sortValue: id, searchValue: String(id) } },
                name: { type: "text", value: normalizeName(it.name), params: { searchValue: normalizeName(it.name) } },
                typeId: { type: "text", value: typeof it.typeId !== "undefined" ? String(it.typeId) : "—", params: {} },
                raceId: { type: "text", value: typeof it.raceId !== "undefined" ? String(it.raceId) : "—", params: {} },
                breedId: { type: "text", value: typeof it.breedId !== "undefined" ? String(it.breedId) : "—", params: {} },
                level: { type: "text", value: typeof it.level !== "undefined" ? String(it.level) : "—", params: {} },
            };

            return {
                id: Number.isFinite(id) ? id : Math.random(),
                rowParams: { raw: it },
                cells,
            };
        });
});

const runSearch = async () => {
    if (!props.enabled) return;
    searching.value = true;
    selectedIds.value = [];
    try {
        const qs = buildQuery();
        const url = `/api/scrapping/search/${props.entityType}${qs ? `?${qs}` : ""}`;
        const res = await fetch(url, { headers: { Accept: "application/json" } });
        const data = await res.json();
        if (res.ok && data.success) {
            lastResult.value = data;
            success(`Recherche OK: ${data.data?.meta?.returned ?? (data.data?.items?.length ?? 0)} résultat(s)`);
        } else {
            showError(data.message || "Erreur lors de la recherche");
        }
    } catch (e) {
        showError("Erreur lors de la recherche : " + e.message);
    } finally {
        searching.value = false;
    }
};

const importSelected = async () => {
    const ids = Array.isArray(selectedIds.value) ? selectedIds.value : [];
    if (!ids.length) {
        showError("Aucun ID sélectionné");
        return;
    }
    importing.value = true;
    const csrf = getCsrfToken();
    if (!csrf) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        importing.value = false;
        return;
    }

    try {
        // importBatch ne supporte pas resource/consumable ici (uniquement class/monster/item/spell/panoply)
        const entities = ids.map((id) => ({ type: props.entityType, id: Number(id) }));

        const payload = {
            entities,
            skip_cache: !!props.importOptions?.skipCache,
            force_update: !!props.importOptions?.forceUpdate,
            dry_run: !!props.importOptions?.dryRun,
            validate_only: !!props.importOptions?.validateOnly,
            include_relations: !!props.importOptions?.includeRelations,
        };

        const res = await fetch("/api/scrapping/import/batch", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (res.ok) {
            const s = data.summary || {};
            success(`Import batch: ${s.success ?? 0}/${s.total ?? entities.length} (erreurs: ${s.errors ?? 0})`);
            lastBatchResults.value = (s.errors ?? 0) > 0 ? (data.results ?? []) : null;
        } else {
            showError(data.message || "Erreur lors de l'import batch");
            lastBatchResults.value = null;
        }
    } catch (e) {
        showError("Erreur lors de l'import batch : " + e.message);
        lastBatchResults.value = null;
    } finally {
        importing.value = false;
    }
};

const previewSelected = () => {
    const ids = Array.isArray(selectedIds.value) ? selectedIds.value : [];
    if (!ids.length) {
        showError("Aucun ID sélectionné");
        return;
    }
    emit("preview-selected", Number(ids[0]));
};

/** Prévisualisation en lot : appelle preview/batch et remplit batchPreviewResults (ID | Nom | Statut | Message). */
const runBatchPreview = async () => {
    const raw = selectedIds?.value ?? selectedIds;
    const ids = Array.isArray(raw) ? raw : Array.from(raw || []);
    const idList = ids.slice(0, 100).map((id) => Number(id)).filter((n) => Number.isFinite(n) && n > 0);
    if (!idList.length) {
        showError("Aucun ID sélectionné (max 100)");
        return;
    }
    batchPreviewLoading.value = true;
    batchPreviewResults.value = [];
    try {
        const res = await fetch("/api/scrapping/preview/batch", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": getCsrfToken() || "",
            },
            body: JSON.stringify({ type: props.entityType, ids: idList }),
        });
        const data = await res.json();
        const items = res.ok && data.success ? data.data?.items || [] : [];
        const nameById = nameByIdFromSearch.value;
        batchPreviewResults.value = items.map((item) => {
            const sim = item.spell_effects_simulation || [];
            const createCount = sim.filter((s) => s.action === "create").length;
            const reuseCount = sim.filter((s) => s.action === "reuse").length;
            const effectsSummary =
                props.entityType === "spell" && sim.length > 0
                    ? `${sim.length} effet(s) (${createCount} créat., ${reuseCount} réutil.)`
                    : null;
            return {
                id: item.id,
                name: nameById[item.id] ?? "—",
                status: item.error ? "error" : "ok",
                error: item.error ?? null,
                converted: item.converted ?? null,
                spell_effects_simulation: item.spell_effects_simulation ?? null,
                effects_summary: effectsSummary,
            };
        });
        if (!res.ok) showError(data.message || "Erreur prévisualisation batch");
        else if (batchPreviewResults.value.length) success(`Prévisualisation : ${batchPreviewResults.value.filter((r) => r.status === "ok").length}/${batchPreviewResults.value.length} OK`);
    } catch (e) {
        showError("Prévisualisation batch : " + (e?.message ?? "erreur"));
        batchPreviewResults.value = [];
    } finally {
        batchPreviewLoading.value = false;
    }
};

const exportBatchErrorsCsv = () => {
    const { headers, rows } = buildCsvFromErrorResults(lastBatchErrorResults.value);
    downloadCsvFromRows(headers, rows, filenameForBatchErrors());
    success("Export CSV des erreurs téléchargé.");
};

const exportBatchPreviewCsv = () => {
    const { headers, rows } = buildCsvFromPreviewResults(batchPreviewResults.value);
    downloadCsvFromRows(headers, rows, filenameForBatchPreview());
    success("Export CSV de la prévisualisation téléchargé.");
};
</script>

<template>
    <Card class="p-6 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-primary-100">Recherche (filtres)</h2>
                <p class="text-sm text-primary-300">
                    Rechercher via l’API DofusDB (collect-only), puis importer une sélection.
                </p>
            </div>
        </div>

        <Alert v-if="!enabled" color="warning" class="text-sm">
            La recherche par filtres n’est pas disponible pour <strong>{{ entityType }}</strong>.
        </Alert>

        <div v-else class="space-y-3">
            <div class="grid gap-3 md:grid-cols-3">
                <InputField v-if="hasFilter('name')" v-model="filterValues.name" label="Nom" placeholder="Ex: Bouftou" />
                <InputField v-if="hasFilter('id')" v-model="filterValues.id" label="ID" type="number" placeholder="Ex: 31" />
                <InputField v-if="hasFilter('ids')" v-model="filterValues.ids" label="IDs" placeholder="Ex: 31,32,33" />

                <InputField v-if="hasFilter('typeId')" v-model="filterValues.typeId" label="typeId" type="number" />
                <InputField v-if="hasFilter('raceId')" v-model="filterValues.raceId" label="raceId" type="number" />
                <InputField v-if="hasFilter('breedId')" v-model="filterValues.breedId" label="breedId" type="number" />

                <InputField v-if="hasFilter('levelMin')" v-model="filterValues.levelMin" label="Niveau min" type="number" />
                <InputField v-if="hasFilter('levelMax')" v-model="filterValues.levelMax" label="Niveau max" type="number" />
            </div>

            <div class="grid gap-3 md:grid-cols-4">
                <InputField v-model="paging.limit" label="limit" type="number" />
                <InputField v-model="paging.start_skip" label="start_skip" type="number" />
                <InputField v-model="paging.max_pages" label="max_pages (0=∞)" type="number" />
                <InputField v-model="paging.max_items" label="max_items (0=∞)" type="number" />
            </div>

            <div class="flex flex-wrap gap-2">
                <Btn color="primary" :disabled="searching" @click="runSearch">
                    <Loading v-if="searching" class="mr-2" />
                    Lancer la recherche
                </Btn>
                <Btn color="secondary" :disabled="!selectedIds.length" @click="previewSelected">
                    Prévisualiser ({{ selectedIds.length ? selectedIds[0] : "—" }})
                </Btn>
                <Btn color="secondary" variant="outline" :disabled="batchPreviewLoading || !selectedIds.length" @click="runBatchPreview">
                    <Loading v-if="batchPreviewLoading" class="mr-2" />
                    Prévisualiser la sélection ({{ Math.min(selectedIds.length, 100) }})
                </Btn>
                <Btn color="success" :disabled="importing || !selectedIds.length" @click="importSelected">
                    <Loading v-if="importing" class="mr-2" />
                    Importer la sélection ({{ selectedIds.length }})
                </Btn>
                <Btn variant="ghost" :disabled="!selectedIds.length" @click="selectedIds = []">
                    Vider la sélection
                </Btn>
            </div>

            <div v-if="lastResult?.data?.meta" class="text-xs text-primary-300">
                meta: returned={{ lastResult.data.meta.returned }} · pages={{ lastResult.data.meta.pages }} · total={{ lastResult.data.meta.total ?? '—' }}
            </div>

            <TanStackTable
                v-if="rows.length"
                :config="tableConfig"
                :rows="rows"
                :loading="searching"
                :selected-ids="selectedIds"
                @update:selected-ids="(ids) => selectedIds = ids"
            />

            <div v-else class="text-sm text-primary-300 italic">
                Aucun résultat (lance une recherche).
            </div>

            <!-- Résultat prévisualisation batch (ID | Nom | Statut | Message) -->
            <div v-if="batchPreviewResults.length > 0" class="mt-4 overflow-hidden rounded-lg border border-base-300 bg-base-100">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-base-300 px-3 py-2">
                    <span class="font-semibold text-primary-100 text-sm">Résultat prévisualisation ({{ batchPreviewResults.length }} ligne(s))</span>
                    <div class="flex items-center gap-2">
                        <Btn size="sm" variant="outline" title="Télécharger en CSV" @click="exportBatchPreviewCsv">Exporter (CSV)</Btn>
                        <Btn size="sm" variant="ghost" @click="batchPreviewResults = []">Fermer</Btn>
                    </div>
                </div>
                <div class="overflow-x-auto p-3 max-h-56 overflow-y-auto">
                    <table class="table table-zebra table-pin-rows table-xs">
                        <thead>
                            <tr class="bg-base-300/70 text-primary-200">
                                <th class="w-16 font-semibold">ID</th>
                                <th class="font-semibold">Nom</th>
                                <th class="w-20 font-semibold">Statut</th>
                                <th v-if="entityType === 'spell'" class="w-44 font-semibold">Effets (simul.)</th>
                                <th class="font-semibold">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, idx) in batchPreviewResults" :key="idx" :class="row.status === 'error' ? 'hover:bg-error/5' : ''">
                                <td class="font-mono font-medium text-primary-100">{{ row.id }}</td>
                                <td class="text-primary-200">{{ row.name }}</td>
                                <td>
                                    <Badge :content="row.status === 'ok' ? 'OK' : 'Erreur'" :color="row.status === 'ok' ? 'success' : 'error'" size="xs" />
                                </td>
                                <td v-if="entityType === 'spell'" class="text-xs text-primary-300" :title="row.effects_summary || 'Aucun effet'">
                                    {{ row.effects_summary || '—' }}
                                </td>
                                <td class="text-xs text-primary-300">{{ row.error || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Détail des erreurs du dernier import batch -->
            <div v-if="lastBatchErrorResults.length > 0" class="mt-4 overflow-hidden rounded-lg border border-error/30 bg-error/5">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-error/20 bg-error/10 px-3 py-2">
                    <div class="flex items-center gap-2">
                        <Icon source="fa-solid fa-triangle-exclamation" alt="" pack="solid" class="text-error text-sm" />
                        <span class="font-semibold text-primary-100 text-sm">Erreurs import</span>
                        <Badge :content="String(lastBatchErrorResults.length)" color="error" size="xs" />
                    </div>
                    <div class="flex items-center gap-2">
                        <Btn size="sm" variant="outline" class="text-error-200" title="Télécharger les erreurs en CSV" @click="exportBatchErrorsCsv">Exporter (CSV)</Btn>
                        <Btn size="sm" variant="ghost" class="text-error-200 hover:bg-error/10" @click="lastBatchResults = null">Fermer</Btn>
                    </div>
                </div>
                <Alert color="error" variant="soft" class="mx-3 mt-2 mb-0 text-xs rounded">
                    {{ lastBatchErrorResults.length }} entité(s) en échec. Détail ci-dessous.
                </Alert>
                <div class="overflow-x-auto p-3 max-h-44 overflow-y-auto">
                    <table class="table table-zebra table-pin-rows table-xs">
                        <thead>
                            <tr class="bg-base-300/70 text-primary-200">
                                <th class="w-20 font-semibold">Type</th>
                                <th class="w-16 font-semibold">ID</th>
                                <th class="w-14 font-semibold">Statut</th>
                                <th class="font-semibold">Message / Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, idx) in lastBatchErrorResults" :key="idx" class="hover:bg-error/5">
                                <td><Badge :content="row.type" color="neutral" size="xs" class="font-mono" /></td>
                                <td class="font-mono font-medium text-primary-100">{{ row.id }}</td>
                                <td><Badge content="Erreur" color="error" size="xs" /></td>
                                <td class="text-xs">
                                    <span class="text-error-200 font-medium">{{ row.error || '—' }}</span>
                                    <ul v-if="row.validation_errors?.length" class="list-disc list-inside mt-1 text-primary-400 space-y-0.5">
                                        <li v-for="(ve, i) in row.validation_errors" :key="i"><span class="font-mono text-[11px]">{{ ve.path || '—' }}</span> : {{ ve.message || '—' }}</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Card>
</template>

