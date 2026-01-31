<script setup>
/**
 * ScrappingDashboard (Organism)
 *
 * @description
 * Dashboard de scrapping refondu pour la page `/scrapping`.
 * Flow:
 * - Choix de l'entité (header)
 * - Filtres (au moins IDs + name, + filtres dépendants)
 * - Recherche (collect-only) → tableau résultat + sélection
 * - Actions sur sélection: reset / simuler / importer
 * - Options d'import + historique type "invite de commande"
 */
import { computed, onMounted, ref, watch } from "vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import CheckboxField from "@/Pages/Molecules/data-input/CheckboxField.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

const notificationStore = useNotificationStore();
const { success, error: showError, info } = notificationStore;

const loadingMeta = ref(true);
const loadingConfig = ref(true);
const searching = ref(false);
const importing = ref(false);
const fetchAllRunning = ref(false);
const fetchAllAbort = ref(null);

const metaEntityTypes = ref([]);
const configEntitiesByKey = ref({});
const selectedEntityType = ref("monster");

// Filtres principaux demandés
const filterIds = ref(""); // "1" | "1,2,3" | "1-50"
const filterName = ref("");

// Filtres dépendants (optionnels)
const knownTypesLoading = ref(false);
const knownTypeOptions = ref([]); // { value:number|string, label:string }
const selectedKnownTypeInclude = ref("");
const selectedKnownTypeExclude = ref("");

const filterTypeIds = ref([]); // number[]
const filterTypeIdsNot = ref([]); // number[]

const filterRaceId = ref("");
const filterBreedId = ref("");
const filterLevelMin = ref("");
const filterLevelMax = ref("");

// Options d'import (UI)
const optSkipCache = ref(false);
const optWithImages = ref(true);
const optForceUpdate = ref(false);
const optManualChoice = ref(false); // => validate_only
const optIncludeRelations = ref(true);

// Résultats
const rawItems = ref([]);
const tableSearch = ref("");
const selectedIds = ref(new Set());
const lastMeta = ref(null);

// Analyse des effets (unmapped)
const effectsAnalysisLoading = ref(false);
const effectsAnalysisEntityId = ref(null);
const effectsAnalysisType = ref(null);
const effectsAnalysisUnmapped = ref([]);
const effectsAnalysisSummary = ref(null);

// Pagination / gros volumes
const pageLimit = ref(100);
const pageOffset = ref(0); // start_skip
const pageMaxItems = ref(0); // 0 = illimité côté UI
const fetchAll = ref(false);

// Historique (console)
const historyLines = ref([]);

const pushHistory = (line) => {
    const ts = new Date().toLocaleString("fr-FR");
    historyLines.value.unshift(`[${ts}] ${line}`);
};

const loadKnownTypes = async () => {
    const t = String(selectedEntityType.value || "");
    const endpoint = (() => {
        if (t === "resource") return "/api/scrapping/resource-types?decision=allowed";
        if (t === "consumable") return "/api/scrapping/consumable-types?decision=allowed";
        if (t === "item" || t === "equipment") return "/api/scrapping/item-types?decision=allowed";
        return null;
    })();

    knownTypeOptions.value = [];
    selectedKnownTypeInclude.value = "";
    selectedKnownTypeExclude.value = "";
    filterTypeIds.value = [];
    filterTypeIdsNot.value = [];

    if (!endpoint) return;

    knownTypesLoading.value = true;
    try {
        const res = await fetch(endpoint, { headers: { Accept: "application/json" } });
        const json = await res.json();
        if (!res.ok || !json?.success) {
            throw new Error(json?.message || "Chargement des types impossible");
        }
        const rows = Array.isArray(json.data) ? json.data : [];
        knownTypeOptions.value = rows
            .map((r) => ({
                value: Number(r?.dofusdb_type_id),
                label: String(r?.name || `DofusDB type #${r?.dofusdb_type_id}`),
            }))
            .filter((o) => Number.isFinite(Number(o.value)) && Number(o.value) > 0)
            .sort((a, b) => String(a.label).localeCompare(String(b.label), "fr-FR"));
    } catch (e) {
        showError("Types: " + e.message);
    } finally {
        knownTypesLoading.value = false;
    }
};

const entityOptions = computed(() => {
    // On propose uniquement les entités à la fois:
    // - déclarées en config (donc supportées par /api/scrapping/search/{entity})
    // - importables (import endpoints existants)
    const allowed = new Set(["class", "monster", "equipment", "consumable", "resource", "item", "spell", "panoply"]);
    const fromMeta = Array.isArray(metaEntityTypes.value) ? metaEntityTypes.value : [];
    return fromMeta
        .filter((e) => e?.type && allowed.has(String(e.type)) && configEntitiesByKey.value?.[String(e.type)])
        .map((e) => ({
            value: String(e.type),
            label: String(e.label || e.type),
        }));
});

const selectedEntityLabel = computed(() => {
    const opt = entityOptions.value.find((o) => o.value === selectedEntityType.value);
    return opt?.label || selectedEntityType.value;
});

const supportedFilters = computed(() => {
    const cfg = configEntitiesByKey.value?.[String(selectedEntityType.value)] || null;
    const supported = cfg?.filters?.supported;
    return Array.isArray(supported) ? supported : [];
});

const supports = (key) => supportedFilters.value.some((f) => String(f?.key || "") === key);

const knownTypeLabelById = computed(() => {
    const map = new Map();
    for (const opt of knownTypeOptions.value || []) {
        const id = Number(opt?.value);
        if (!Number.isFinite(id) || id <= 0) continue;
        map.set(id, String(opt?.label || `#${id}`));
    }
    return map;
});

const labelForTypeId = (id) => {
    const n = Number(id);
    if (!Number.isFinite(n) || n <= 0) return "—";
    return knownTypeLabelById.value.get(n) || `#${n}`;
};

const visibleItems = computed(() => {
    const items = Array.isArray(rawItems.value) ? rawItems.value : [];
    const q = String(tableSearch.value || "").trim().toLowerCase();
    if (!q) return items;

    const norm = (v) => String(v ?? "").toLowerCase();
    return items.filter((it) => {
        const id = norm(it?.id);
        const name = norm(it?.name?.fr || it?.name?.en || it?.name);
        return id.includes(q) || name.includes(q);
    });
});

const selectedCount = computed(() => selectedIds.value.size);
const allSelected = computed(() => {
    const ids = visibleItems.value.map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));
    if (!ids.length) return false;
    return ids.every((id) => selectedIds.value.has(id));
});

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

const parseIdsFilter = () => {
    const txt = String(filterIds.value || "").trim();
    if (!txt) return {};

    // Range: "a-b"
    const m = txt.match(/^(\d+)\s*-\s*(\d+)$/);
    if (m) {
        const a = Number(m[1]);
        const b = Number(m[2]);
        if (Number.isFinite(a) && Number.isFinite(b)) {
            return { idMin: Math.min(a, b), idMax: Math.max(a, b) };
        }
    }

    // List: "1,2,3"
    if (txt.includes(",")) {
        const parts = txt.split(",").map((p) => p.trim()).filter(Boolean);
        return { ids: parts.join(",") };
    }

    // Single id
    if (/^\d+$/.test(txt)) {
        return { id: txt };
    }

    return {};
};

const parseNumberCsv = (txt) => {
    const s = String(txt || "").trim();
    if (!s) return [];
    const parts = s
        .split(",")
        .map((p) => p.trim())
        .filter(Boolean)
        .map((p) => Number(p))
        .filter((n) => Number.isFinite(n) && n > 0)
        .map((n) => Math.floor(n));
    return Array.from(new Set(parts));
};

const addKnownTypeTo = (target) => {
    const selected = target === "exclude" ? selectedKnownTypeExclude.value : selectedKnownTypeInclude.value;
    const id = Number(selected);
    if (!Number.isFinite(id) || id <= 0) return;

    if (target === "exclude") {
        const next = new Set(filterTypeIdsNot.value);
        next.add(id);
        filterTypeIdsNot.value = Array.from(next);
        selectedKnownTypeExclude.value = "";
        return;
    }

    const next = new Set(filterTypeIds.value);
    next.add(id);
    filterTypeIds.value = Array.from(next);
    selectedKnownTypeInclude.value = "";
};

const removeKnownTypeFrom = (target, id) => {
    const n = Number(id);
    if (!Number.isFinite(n) || n <= 0) return;
    if (target === "exclude") {
        filterTypeIdsNot.value = filterTypeIdsNot.value.filter((x) => Number(x) !== n);
        return;
    }
    filterTypeIds.value = filterTypeIds.value.filter((x) => Number(x) !== n);
};

const buildSearchQuery = () => {
    const q = new URLSearchParams();
    if (optSkipCache.value) q.set("skip_cache", "true");

    const idsPart = parseIdsFilter();
    for (const [k, v] of Object.entries(idsPart)) q.set(k, String(v));

    if (String(filterName.value || "").trim() !== "") q.set("name", String(filterName.value).trim());

    // Filtres de types (sélection sur types connus)
    const includeTypeIds = supports("typeIds") ? (filterTypeIds.value || []) : [];
    const excludeTypeIds = supports("typeIdsNot") ? (filterTypeIdsNot.value || []) : [];
    if (Array.isArray(includeTypeIds) && includeTypeIds.length) q.set("typeIds", includeTypeIds.join(","));
    if (Array.isArray(excludeTypeIds) && excludeTypeIds.length) q.set("typeIdsNot", excludeTypeIds.join(","));

    if (supports("raceId") && String(filterRaceId.value || "").trim() !== "") q.set("raceId", String(filterRaceId.value).trim());
    if (supports("breedId") && String(filterBreedId.value || "").trim() !== "") q.set("breedId", String(filterBreedId.value).trim());
    if (supports("levelMin") && String(filterLevelMin.value || "").trim() !== "") q.set("levelMin", String(filterLevelMin.value).trim());
    if (supports("levelMax") && String(filterLevelMax.value || "").trim() !== "") q.set("levelMax", String(filterLevelMax.value).trim());

    // Pagination
    q.set("limit", String(Math.max(1, Math.min(200, Number(pageLimit.value) || 100))));
    q.set("start_skip", String(Math.max(0, Number(pageOffset.value) || 0)));

    // Par défaut, un seul "page" (pour éviter une réponse trop grosse)
    // En mode "Tout récupérer", on pagine côté frontend (boucle).
    q.set("max_pages", "1");
    const maxItems = Number(pageMaxItems.value);
    if (Number.isFinite(maxItems) && maxItems > 0) q.set("max_items", String(Math.min(20000, Math.floor(maxItems))));

    return q.toString();
};

const loadMeta = async () => {
    loadingMeta.value = true;
    try {
        const res = await fetch("/api/scrapping/meta", { headers: { Accept: "application/json" } });
        const data = await res.json();
        if (res.ok && data.success) {
            metaEntityTypes.value = data.data || [];
        } else {
            showError(data.message || "Impossible de charger les entités");
        }
    } catch (e) {
        showError("Erreur chargement meta : " + e.message);
    } finally {
        loadingMeta.value = false;
    }
};

const loadConfig = async () => {
    loadingConfig.value = true;
    try {
        const res = await fetch("/api/scrapping/config", { headers: { Accept: "application/json" } });
        const data = await res.json();
        if (res.ok && data.success) {
            const map = {};
            const entities = data.data?.entities || [];
            for (const e of entities) {
                if (e?.entity) map[String(e.entity)] = e;
            }
            configEntitiesByKey.value = map;
        } else {
            showError(data.message || "Impossible de charger la config scrapping");
        }
    } catch (e) {
        showError("Erreur chargement config : " + e.message);
    } finally {
        loadingConfig.value = false;
    }
};

onMounted(async () => {
    await Promise.all([loadMeta(), loadConfig()]);
    await loadKnownTypes();
});

watch(
    () => selectedEntityType.value,
    async () => {
        // Reset UI state liée aux types + recharge la liste "connue"
        await loadKnownTypes();
    }
);

const resetTable = () => {
    rawItems.value = [];
    selectedIds.value = new Set();
    lastMeta.value = null;
    tableSearch.value = "";
    pushHistory("Réinitialisation du tableau.");
};

const toggleSelectAll = () => {
    const next = new Set(selectedIds.value);
    const ids = visibleItems.value.map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));
    const shouldSelectAll = !allSelected.value;
    for (const id of ids) {
        if (shouldSelectAll) next.add(id);
        else next.delete(id);
    }
    selectedIds.value = next;
};

const toggleSelectOne = (id) => {
    const n = Number(id);
    if (!Number.isFinite(n)) return;
    const next = new Set(selectedIds.value);
    if (next.has(n)) next.delete(n);
    else next.add(n);
    selectedIds.value = next;
};

const runSearch = async () => {
    if (fetchAllRunning.value) return;
    searching.value = true;
    selectedIds.value = new Set();
    try {
        if (!fetchAll.value) {
            const qs = buildSearchQuery();
            const url = `/api/scrapping/search/${selectedEntityType.value}${qs ? `?${qs}` : ""}`;
            pushHistory(`Recherche ${selectedEntityType.value} (${selectedEntityLabel.value}) : ${qs || "sans filtres"}`);

            const res = await fetch(url, { headers: { Accept: "application/json" } });
            const data = await res.json();

            if (res.ok && data.success) {
                rawItems.value = data.data?.items || [];
                lastMeta.value = data.data?.meta || null;
                success(`Recherche OK (${rawItems.value.length} résultat(s))`);
                pushHistory(`→ OK: ${rawItems.value.length} résultat(s).`);
            } else {
                showError(data.message || "Erreur lors de la recherche");
                pushHistory(`→ ERREUR: ${data.message || "recherche"}`);
            }
            return;
        }

        // Mode "Tout récupérer": pagination côté frontend
        fetchAllRunning.value = true;
        rawItems.value = [];
        lastMeta.value = null;
        const seen = new Map();

        const baseQs = buildSearchQuery();
        const baseParams = new URLSearchParams(baseQs);
        const limit = Number(baseParams.get("limit") || 100);
        const startSkip = Number(baseParams.get("start_skip") || 0);
        const uiMax = Number(pageMaxItems.value) || 0; // 0 = illimité

        const ctrl = new AbortController();
        fetchAllAbort.value = ctrl;

        pushHistory(`Recherche ALL ${selectedEntityType.value} (limit=${limit}, start_skip=${startSkip}, max_items=${uiMax || "∞"})`);

        let skip = Math.max(0, startSkip);
        let loops = 0;
        let total = null;

        while (true) {
            loops += 1;
            if (loops > 100000) {
                throw new Error("Stop sécurité: trop de pages");
            }

            const params = new URLSearchParams(baseParams);
            params.set("start_skip", String(skip));
            params.set("max_pages", "1");
            // max_items côté endpoint garde un garde-fou; on gère aussi côté UI.
            const url = `/api/scrapping/search/${selectedEntityType.value}?${params.toString()}`;

            const res = await fetch(url, { headers: { Accept: "application/json" }, signal: ctrl.signal });
            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data?.message || "Erreur lors de la recherche");
            }

            const items = Array.isArray(data.data?.items) ? data.data.items : [];
            const meta = data.data?.meta || null;
            if (meta && typeof meta.total === "number") total = meta.total;
            const effectiveLimit = Number(meta?.limit || limit) || limit;

            for (const it of items) {
                const id = Number(it?.id);
                if (!Number.isFinite(id)) continue;
                if (!seen.has(id)) {
                    seen.set(id, it);
                    rawItems.value.push(it);
                }
            }

            lastMeta.value = meta;
            pushHistory(`→ page skip=${skip}: +${items.length} (total unique=${rawItems.value.length}${total !== null ? ` / total=${total}` : ""})`);

            // Stop conditions
            if (items.length < effectiveLimit) break;
            if (total !== null && skip + effectiveLimit >= total) break;
            if (uiMax > 0 && rawItems.value.length >= uiMax) break;

            skip += effectiveLimit;
        }

        success(`Recherche ALL OK (${rawItems.value.length} résultat(s) uniques)`);
    } catch (e) {
        if (e?.name === "AbortError") {
            info("Recherche interrompue.", { duration: 2000 });
            pushHistory("→ STOP: recherche interrompue.");
        } else {
        showError("Erreur lors de la recherche : " + e.message);
        pushHistory(`→ ERREUR: ${e.message}`);
        }
    } finally {
        searching.value = false;
        fetchAllRunning.value = false;
        fetchAllAbort.value = null;
    }
};

const stopFetchAll = () => {
    try {
        fetchAllAbort.value?.abort?.();
    } catch {
        // ignore
    }
};

const buildBatchPayload = (dryRun) => {
    const ids = selectedCount.value
        ? Array.from(selectedIds.value)
        : visibleItems.value.map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));

    const entities = ids.map((id) => ({ type: selectedEntityType.value, id }));

    return {
        entities,
        skip_cache: !!optSkipCache.value,
        force_update: !!optForceUpdate.value,
        dry_run: !!dryRun,
        validate_only: !!optManualChoice.value,
        include_relations: !!optIncludeRelations.value,
        with_images: !!optWithImages.value,
    };
};

const runBatch = async (mode) => {
    // mode: 'simulate' | 'import'
    if (!visibleItems.value.length) {
        showError("Aucun résultat à traiter.");
        return;
    }

    importing.value = true;
    const csrf = getCsrfToken();
    if (!csrf) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        importing.value = false;
        return;
    }

    const dryRun = mode === "simulate";
    const payload = buildBatchPayload(dryRun);
    const targetCount = payload.entities.length;
    if (targetCount < 1) {
        showError("Aucune entité sélectionnée.");
        importing.value = false;
        return;
    }

    const label = dryRun ? "Simulation" : "Import";
    pushHistory(`${label} batch (${selectedEntityType.value}) sur ${targetCount} entité(s).`);
    info(`${label} en cours…`, { duration: 1500 });

    try {
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
            success(`${label}: ${s.success ?? 0}/${s.total ?? targetCount} (erreurs: ${s.errors ?? 0})`);
            pushHistory(`→ ${label.toUpperCase()} OK: ${s.success ?? 0}/${s.total ?? targetCount} (erreurs: ${s.errors ?? 0})`);
        } else {
            showError(data.message || `Erreur ${label.toLowerCase()}`);
            pushHistory(`→ ${label.toUpperCase()} ERREUR: ${data.message || "batch"}`);
        }
    } catch (e) {
        showError(`Erreur ${label.toLowerCase()} : ` + e.message);
        pushHistory(`→ ${label.toUpperCase()} ERREUR: ${e.message}`);
    } finally {
        importing.value = false;
    }
};

const formatName = (name) => {
    if (!name) return "—";
    if (typeof name === "string") return name;
    if (typeof name === "object") return name.fr || name.en || name.de || name.es || name.pt || "—";
    return "—";
};

const existsLabel = (it) => (it?.exists ? "Existe" : "Nouveau");
const existsTooltip = (it) => {
    if (!it?.exists) return "Aucune entrée trouvée en base (par dofusdb_id).";
    const internal = it?.existing?.id ? `ID Krosmoz: ${it.existing.id}` : "Entrée trouvée en base.";
    return internal;
};

const canAnalyzeEffects = computed(() => {
    const t = String(selectedEntityType.value || "");
    return (t === "item" || t === "spell" || t === "equipment" || t === "consumable" || t === "resource") && selectedCount.value > 0;
});

const parseJsonSafe = (v) => {
    try {
        if (typeof v !== "string") return null;
        const s = v.trim();
        if (!s) return null;
        return JSON.parse(s);
    } catch {
        return null;
    }
};

const extractUnmappedFromConverted = (converted) => {
    // item.effect = JSON bonuses
    // spell.effect = JSON pack { normalized, bonuses }
    const parsed = parseJsonSafe(converted?.effect);
    if (!parsed) return { unmapped: [], summary: null };

    if (Array.isArray(parsed?.unmapped)) {
        return { unmapped: parsed.unmapped, summary: parsed };
    }
    if (Array.isArray(parsed?.bonuses?.unmapped)) {
        return { unmapped: parsed.bonuses.unmapped, summary: parsed.bonuses };
    }
    return { unmapped: [], summary: parsed };
};

const analyzeEffects = async () => {
    if (!canAnalyzeEffects.value) return;
    const id = Array.from(selectedIds.value)[0];
    if (!Number.isFinite(Number(id))) return;

    effectsAnalysisLoading.value = true;
    effectsAnalysisEntityId.value = Number(id);
    effectsAnalysisType.value = String(selectedEntityType.value);
    effectsAnalysisUnmapped.value = [];
    effectsAnalysisSummary.value = null;

    pushHistory(`Analyse effets: preview ${effectsAnalysisType.value} #${effectsAnalysisEntityId.value}`);

    try {
        const url = `/api/scrapping/preview/${effectsAnalysisType.value}/${effectsAnalysisEntityId.value}`;
        const res = await fetch(url, { headers: { Accept: "application/json" } });
        const json = await res.json();
        if (!res.ok || !json?.success) {
            throw new Error(json?.message || "Prévisualisation impossible");
        }

        const preview = json?.data || {};
        const converted = preview?.converted || {};
        const { unmapped, summary } = extractUnmappedFromConverted(converted);

        effectsAnalysisUnmapped.value = Array.isArray(unmapped) ? unmapped : [];
        effectsAnalysisSummary.value = summary || null;

        success(`Analyse effets OK (${effectsAnalysisUnmapped.value.length} non mappé(s))`);
        pushHistory(`→ Analyse OK: ${effectsAnalysisUnmapped.value.length} effet(s) non mappé(s).`);
    } catch (e) {
        showError("Analyse effets : " + e.message);
        pushHistory(`→ ERREUR analyse effets: ${e.message}`);
    } finally {
        effectsAnalysisLoading.value = false;
    }
};

const clearEffectsAnalysis = () => {
    effectsAnalysisEntityId.value = null;
    effectsAnalysisType.value = null;
    effectsAnalysisUnmapped.value = [];
    effectsAnalysisSummary.value = null;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Header: entité + filtres -->
        <Card class="p-6 space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-primary-100">Scrapping</h2>
                    <p class="text-sm text-primary-300 mt-1">
                        Choisis une entité, filtre, recherche, puis simule ou importe.
                    </p>
                </div>
                <div class="min-w-[260px]">
                    <SelectField
                        label="Entité"
                        v-model="selectedEntityType"
                        :options="entityOptions"
                        placeholder="Choisir…"
                        :disabled="loadingMeta || loadingConfig"
                    />
                </div>
            </div>

            <div v-if="loadingMeta || loadingConfig" class="py-4 flex items-center gap-3 text-primary-300">
                <Loading />
                <span>Chargement…</span>
            </div>

            <template v-else>
                <div class="grid gap-3 md:grid-cols-3">
                    <InputField
                        v-model="filterIds"
                        label="IDs"
                        placeholder="Ex: 12 | 12,13,14 | 12-50"
                        helper="IDs: un seul, une liste séparée par ',' ou une plage avec '-'"
                    />
                    <InputField
                        v-model="filterName"
                        label="Nom"
                        placeholder="Ex: Bouftou"
                    />

                    <div v-if="supports('typeIds') || supports('typeIdsNot')" class="md:col-span-3 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-sm text-primary-300">
                                Filtre par types (types connus uniquement)
                            </div>
                            <div v-if="knownTypesLoading" class="text-xs text-primary-300 flex items-center gap-2">
                                <Loading />
                                <span>Chargement des types…</span>
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div v-if="supports('typeIds')" class="space-y-2">
                                <div class="flex gap-2 items-end">
                                    <SelectField
                                        class="flex-1"
                                        label="Types (inclure)"
                                        v-model="selectedKnownTypeInclude"
                                        :options="knownTypeOptions"
                                        placeholder="Choisir un type…"
                                        :disabled="knownTypesLoading"
                                    />
                                    <Btn
                                        size="sm"
                                        variant="outline"
                                        type="button"
                                        :disabled="!selectedKnownTypeInclude"
                                        @click="addKnownTypeTo('include')"
                                    >
                                        Ajouter
                                    </Btn>
                                </div>
                                <div v-if="filterTypeIds.length" class="flex flex-wrap gap-2">
                                    <span
                                        v-for="id in filterTypeIds"
                                        :key="`inc-${id}`"
                                        class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded border border-base-300 bg-base-200/40"
                                    >
                                        <span>{{ labelForTypeId(id) }}</span>
                                        <button type="button" class="btn btn-ghost btn-xs" @click="removeKnownTypeFrom('include', id)">
                                            ✕
                                        </button>
                                    </span>
                                </div>
                                <div v-else class="text-xs text-primary-300 italic">
                                    Aucun type inclus.
                                </div>
                            </div>

                            <div v-if="supports('typeIdsNot')" class="space-y-2">
                                <div class="flex gap-2 items-end">
                                    <SelectField
                                        class="flex-1"
                                        label="Types (exclure)"
                                        v-model="selectedKnownTypeExclude"
                                        :options="knownTypeOptions"
                                        placeholder="Choisir un type…"
                                        :disabled="knownTypesLoading"
                                    />
                                    <Btn
                                        size="sm"
                                        variant="outline"
                                        type="button"
                                        :disabled="!selectedKnownTypeExclude"
                                        @click="addKnownTypeTo('exclude')"
                                    >
                                        Ajouter
                                    </Btn>
                                </div>
                                <div v-if="filterTypeIdsNot.length" class="flex flex-wrap gap-2">
                                    <span
                                        v-for="id in filterTypeIdsNot"
                                        :key="`exc-${id}`"
                                        class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded border border-base-300 bg-base-200/40"
                                    >
                                        <span>{{ labelForTypeId(id) }}</span>
                                        <button type="button" class="btn btn-ghost btn-xs" @click="removeKnownTypeFrom('exclude', id)">
                                            ✕
                                        </button>
                                    </span>
                                </div>
                                <div v-else class="text-xs text-primary-300 italic">
                                    Aucun type exclu.
                                </div>
                            </div>
                        </div>
                    </div>
                    <InputField
                        v-if="supports('raceId')"
                        v-model="filterRaceId"
                        label="raceId"
                        type="number"
                    />
                    <InputField
                        v-if="supports('breedId')"
                        v-model="filterBreedId"
                        label="breedId"
                        type="number"
                    />
                    <InputField
                        v-if="supports('levelMin')"
                        v-model="filterLevelMin"
                        label="Niveau min"
                        type="number"
                    />
                    <InputField
                        v-if="supports('levelMax')"
                        v-model="filterLevelMax"
                        label="Niveau max"
                        type="number"
                    />
                </div>

                <div class="grid gap-3 md:grid-cols-4">
                    <InputField v-model="pageLimit" label="Limit" type="number" helper="Taille des pages (1-200)" />
                    <InputField v-model="pageOffset" label="Offset" type="number" helper="start_skip (>=0)" />
                    <InputField v-model="pageMaxItems" label="Max items" type="number" helper="0 = illimité (UI)" />
                    <CheckboxField
                        :model-value="fetchAll"
                        @update:model-value="fetchAll = $event"
                        label="Tout récupérer (pagination)"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <Btn color="primary" :disabled="searching" @click="runSearch">
                        <Loading v-if="searching" class="mr-2" />
                        <Icon v-else source="fa-solid fa-magnifying-glass" alt="Rechercher" pack="solid" class="mr-2" />
                        Rechercher
                    </Btn>
                    <Btn v-if="fetchAllRunning" variant="ghost" @click="stopFetchAll">
                        Arrêter
                    </Btn>
                    <div v-if="lastMeta" class="text-xs text-primary-300">
                        <Badge :content="String(lastMeta.returned ?? rawItems.length)" color="primary" />
                        <span class="ml-2">résultat(s)</span>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Corps: tableau -->
        <Card class="p-6 space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-primary-100">Résultats</h3>
                    <Badge :content="String(visibleItems.length)" color="neutral" />
                    <span v-if="selectedCount" class="text-sm text-primary-300">· sélection: {{ selectedCount }}</span>
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <InputField v-model="tableSearch" label="Recherche dans le tableau" placeholder="id ou nom…" />
                </div>
            </div>

            <div class="flex flex-wrap gap-2 items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    <Btn variant="ghost" :disabled="!rawItems.length" @click="resetTable">
                        Réinitialiser
                    </Btn>
                    <Btn color="secondary" :disabled="importing || !rawItems.length" @click="runBatch('simulate')">
                        <Loading v-if="importing" class="mr-2" />
                        Simuler
                    </Btn>
                    <Btn color="success" :disabled="importing || !rawItems.length" @click="runBatch('import')">
                        <Loading v-if="importing" class="mr-2" />
                        Importer
                    </Btn>
                    <Btn
                        variant="ghost"
                        :disabled="effectsAnalysisLoading || !canAnalyzeEffects"
                        @click="analyzeEffects"
                        title="Disponible pour item/sort (sur l’ID sélectionné)"
                    >
                        <Loading v-if="effectsAnalysisLoading" class="mr-2" />
                        Analyser effets (non mappés)
                    </Btn>
                </div>

                <div class="flex items-center gap-2">
                    <Btn size="sm" variant="ghost" :disabled="!rawItems.length" @click="toggleSelectAll">
                        {{ allSelected ? "Tout décocher" : "Tout cocher" }}
                    </Btn>
                </div>
            </div>

            <div v-if="!rawItems.length" class="text-sm text-primary-300 italic">
                Aucun résultat. Lance une recherche.
            </div>

            <div v-else class="overflow-x-auto rounded-lg border border-base-300">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox" class="checkbox checkbox-sm" :checked="allSelected" @change="toggleSelectAll" />
                            </th>
                            <th class="w-24">ID</th>
                            <th>Nom</th>
                            <th class="w-28">Existe</th>
                            <th v-if="supports('typeId') || supports('typeIds') || supports('typeIdsNot')" class="w-48">Type</th>
                            <th v-if="supports('raceId')" class="w-24">raceId</th>
                            <th v-if="supports('breedId')" class="w-24">breedId</th>
                            <th v-if="supports('levelMin') || supports('levelMax')" class="w-24">Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="it in visibleItems" :key="String(it.id)">
                            <td>
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                    :checked="selectedIds.has(Number(it.id))"
                                    @change="toggleSelectOne(it.id)"
                                />
                            </td>
                            <td class="font-mono">{{ it.id }}</td>
                            <td class="font-medium">{{ formatName(it.name) }}</td>
                            <td>
                                <Tooltip :content="existsTooltip(it)">
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="text-xs px-2 py-1 rounded border"
                                            :class="it.exists ? 'border-success/30 bg-success/10 text-success' : 'border-base-300 bg-base-200/40 text-primary-300'"
                                        >
                                            {{ existsLabel(it) }}
                                        </span>
                                    </span>
                                </Tooltip>
                            </td>
                            <td v-if="supports('typeId') || supports('typeIds') || supports('typeIdsNot')">
                                <span v-if="it.typeName" class="text-sm">{{ it.typeName }}</span>
                                <span v-else class="text-primary-300 text-sm italic">—</span>
                                <span v-if="it.typeId" class="ml-2 text-xs text-primary-300 font-mono">#{{ it.typeId }}</span>
                                <span
                                    v-if="it.typeDecision === 'pending'"
                                    class="ml-2 badge badge-warning badge-xs"
                                    title="Ce type est en attente de validation (il sera proposé dans la section de revue des types)."
                                >
                                    À valider
                                </span>
                            </td>
                            <td v-if="supports('raceId')">{{ it.raceId ?? "—" }}</td>
                            <td v-if="supports('breedId')">{{ it.breedId ?? "—" }}</td>
                            <td v-if="supports('levelMin') || supports('levelMax')">{{ it.level ?? "—" }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <!-- Analyse des effets non mappés -->
        <Card v-if="effectsAnalysisEntityId !== null" class="p-6 space-y-4">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-primary-100">Analyse des effets non mappés</h3>
                    <p class="text-xs text-primary-300 mt-1">
                        {{ effectsAnalysisType }} #{{ effectsAnalysisEntityId }}
                        <span v-if="effectsAnalysisSummary && typeof effectsAnalysisSummary === 'object'">
                            · unmapped: {{ Array.isArray(effectsAnalysisUnmapped) ? effectsAnalysisUnmapped.length : 0 }}
                        </span>
                    </p>
                </div>
                <Btn size="sm" variant="ghost" @click="clearEffectsAnalysis">Fermer</Btn>
            </div>

            <div v-if="effectsAnalysisLoading" class="flex items-center gap-2 text-primary-300">
                <Loading />
                <span>Analyse en cours…</span>
            </div>

            <div v-else-if="!effectsAnalysisUnmapped.length" class="text-sm text-primary-300 italic">
                Aucun effet “unmapped” (ou format d’effets non reconnu).
            </div>

            <div v-else class="overflow-x-auto rounded-lg border border-base-300">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="w-24">effectId</th>
                            <th class="w-24">min</th>
                            <th class="w-24">max</th>
                            <th>Description (FR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(u, idx) in effectsAnalysisUnmapped" :key="String(u?.effectId ?? idx)">
                            <td class="font-mono">{{ u?.effectId ?? "—" }}</td>
                            <td class="font-mono">{{ u?.min ?? "—" }}</td>
                            <td class="font-mono">{{ u?.max ?? "—" }}</td>
                            <td class="text-sm">
                                {{ u?.meta?.description_fr || "—" }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <!-- Options + historique -->
        <div class="grid gap-6 lg:grid-cols-2">
            <Card class="p-6 space-y-3">
                <h3 class="font-semibold text-primary-100">Options d’import</h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    <CheckboxField
                        :model-value="optSkipCache"
                        @update:model-value="optSkipCache = $event"
                        label="Ignorer le cache"
                    />
                    <CheckboxField
                        :model-value="optWithImages"
                        @update:model-value="optWithImages = $event"
                        label="Images"
                    />
                    <CheckboxField
                        :model-value="optForceUpdate"
                        @update:model-value="optForceUpdate = $event"
                        label="Écraser si existe déjà"
                    />
                    <CheckboxField
                        :model-value="optManualChoice"
                        @update:model-value="optManualChoice = $event"
                        label="Choix manuel (validation uniquement)"
                    />
                    <CheckboxField
                        :model-value="optIncludeRelations"
                        @update:model-value="optIncludeRelations = $event"
                        label="Inclure les relations"
                    />
                </div>
                <p class="text-xs text-primary-300">
                    “Simuler” = dry-run. “Choix manuel” = ne fait pas l’intégration (validate_only).
                </p>
            </Card>

            <Card class="p-6 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="font-semibold text-primary-100">Historique</h3>
                    <Btn size="sm" variant="ghost" :disabled="!historyLines.length" @click="historyLines = []">
                        Vider
                    </Btn>
                </div>
                <pre class="text-xs bg-base-300/30 border border-base-300 rounded p-3 max-h-80 overflow-auto whitespace-pre-wrap break-words">{{ historyLines.join("\n") }}</pre>
            </Card>
        </div>
    </div>
</template>

