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
import SelectSearchField from "@/Pages/Molecules/data-input/SelectSearchField.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import TanStackTablePagination from "@/Pages/Molecules/table/TanStackTablePagination.vue";
import TypeManagerTable from "@/Pages/Organismes/type-management/TypeManagerTable.vue";
import CompareModal from "@/Pages/Organismes/scrapping/CompareModal.vue";
import EntityModal from "@/Pages/Organismes/entity/EntityModal.vue";
import { Monster } from "@/Models/Entity/Monster";
import { Item } from "@/Models/Entity/Item";
import { Spell } from "@/Models/Entity/Spell";
import { Consumable } from "@/Models/Entity/Consumable";
import { Resource } from "@/Models/Entity/Resource";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

const notificationStore = useNotificationStore();
const { success, error: showError, info } = notificationStore;

const loadingMeta = ref(true);
const loadingConfig = ref(true);
const searching = ref(false);
const importing = ref(false);

const metaEntityTypes = ref([]);
const configEntitiesByKey = ref({});
const selectedEntityType = ref("monster");

// Gestion des types/races (modal)
const typeManagerOpen = ref(false);
const typeManagerConfig = computed(() => {
    const t = String(selectedEntityType.value || "");
    if (t === "resource") {
        return {
            title: "Types DofusDB (Ressources)",
            description: "Décider quels typeId DofusDB sont autorisés pour l’import de ressources.",
            mode: "decision",
            listUrl: "/api/scrapping/resource-types",
            bulkUrl: "/api/scrapping/resource-types/bulk",
        };
    }
    if (t === "consumable") {
        return {
            title: "Types DofusDB (Consommables)",
            description: "Décider quels typeId DofusDB sont autorisés pour l’import de consommables.",
            mode: "decision",
            listUrl: "/api/scrapping/consumable-types",
            bulkUrl: "/api/scrapping/consumable-types/bulk",
        };
    }
    if (t === "equipment") {
        return {
            title: "Types DofusDB (Équipements)",
            description: "Décider quels typeId DofusDB sont autorisés pour l’import d’équipements.",
            mode: "decision",
            listUrl: "/api/scrapping/item-types",
            bulkUrl: "/api/scrapping/item-types/bulk",
        };
    }
    if (t === "monster") {
        return {
            title: "Races de monstres",
            description: "Valider ou archiver des races (champ state).",
            mode: "state",
            listUrl: "/api/types/monster-races",
            bulkUrl: "/api/types/monster-races/bulk",
        };
    }
    if (t === "spell") {
        return {
            title: "Types de sorts",
            description: "Valider ou archiver des types de sorts (champ state).",
            mode: "state",
            listUrl: "/api/types/spell-types",
            bulkUrl: "/api/types/spell-types/bulk",
        };
    }
    return null;
});

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

// Mode de filtrage par types
// - all: dérive les types depuis DofusDB (superType -> types)
// - allowed: seulement les types validés (decision=allowed)
// - selected: seulement les types cochés dans l'UI
const typeMode = ref("allowed"); // "all" | "allowed" | "selected"
const typeModeOptions = [
    { value: "all", label: "Tout récupérer (tous types DofusDB)" },
    { value: "allowed", label: "Types validés uniquement" },
    { value: "selected", label: "Types cochés (sélection UI)" },
];

// Filtre races (monsters) : même logique que les types
const raceMode = ref("allowed"); // "all" | "allowed" | "selected"
const raceModeOptions = [
    { value: "all", label: "Toutes les races" },
    { value: "allowed", label: "Races validées uniquement" },
    { value: "selected", label: "Races cochées (sélection UI)" },
];
const knownRacesLoading = ref(false);
const knownRaceOptions = ref([]); // { value:number|string, label:string }
const selectedKnownRace = ref("");
const filterRaceIds = ref([]); // number[]

// Fallback manuel (debug)
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

// Pagination UI (serveur) : blocs de 100 par défaut
const pageNumber = ref(1);
const perPage = ref(100);

// Import par plage de pages (ex: "1-6" ou "4,5" ou toutes)
const pageRangeInput = ref("");
const importAllPages = ref(false);
const importByPagesProgress = ref(null); // "2/6" ou null

// Historique (console)
const historyLines = ref([]);
const showOptionsAndHistory = ref(false); // masqué par défaut

const pushHistory = (line) => {
    const ts = new Date().toLocaleString("fr-FR");
    historyLines.value.unshift(`[${ts}] ${line}`);
};

const loadKnownTypes = async () => {
    const t = String(selectedEntityType.value || "");
    const endpoint = (() => {
        if (t === "resource") return "/api/scrapping/resource-types?decision=allowed";
        if (t === "consumable") return "/api/scrapping/consumable-types?decision=allowed";
        if (t === "equipment") return "/api/scrapping/item-types?decision=allowed";
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

const loadKnownRaces = async () => {
    const t = String(selectedEntityType.value || "");
    knownRaceOptions.value = [];
    selectedKnownRace.value = "";
    filterRaceIds.value = [];

    if (t !== "monster") return;

    knownRacesLoading.value = true;
    try {
        // liste "validée" (state=playable) : on filtre ensuite sur dofusdb_race_id
        const res = await fetch("/api/types/monster-races?state=playable", { headers: { Accept: "application/json" } });
        const json = await res.json();
        if (!res.ok || !json?.success) {
            throw new Error(json?.message || "Chargement des races impossible");
        }
        const rows = Array.isArray(json.data) ? json.data : [];
        knownRaceOptions.value = rows
            .map((r) => ({
                value: Number(r?.dofusdb_race_id),
                label: String(r?.name || `#${r?.dofusdb_race_id}`),
            }))
            .filter((o) => Number.isFinite(Number(o.value)) && Number(o.value) !== 0)
            .sort((a, b) => String(a.label).localeCompare(String(b.label), "fr-FR"));
    } catch (e) {
        showError("Races: " + e.message);
    } finally {
        knownRacesLoading.value = false;
    }
};

const addKnownRace = () => {
    const id = Number(selectedKnownRace.value);
    if (!Number.isFinite(id) || id <= 0) return;
    const next = new Set(filterRaceIds.value || []);
    next.add(id);
    filterRaceIds.value = Array.from(next);
    selectedKnownRace.value = "";
};

const removeKnownRace = (id) => {
    const n = Number(id);
    if (!Number.isFinite(n) || n <= 0) return;
    filterRaceIds.value = (filterRaceIds.value || []).filter((x) => Number(x) !== n);
};

watch(
    () => typeMode.value,
    (next) => {
        const m = String(next || "allowed");
        if (m !== "selected") {
            selectedKnownTypeInclude.value = "";
            selectedKnownTypeExclude.value = "";
            filterTypeIds.value = [];
            filterTypeIdsNot.value = [];
        }
    }
);

const entityOptions = computed(() => {
    // On propose uniquement les entités à la fois:
    // - déclarées en config (donc supportées par /api/scrapping/search/{entity})
    // - importables (import endpoints existants)
    // NB: on exclut volontairement "item" (Objet) : c'est l'entité générique DofusDB qui mélange
    // équipements/consommables/ressources + autres catégories.
    const allowed = new Set(["class", "monster", "equipment", "consumable", "resource", "spell", "panoply"]);
    const labelOverrides = {
        class: "Classes",
        monster: "Monstres",
        spell: "Sorts",
        equipment: "Équipements",
        consumable: "Consommables",
        resource: "Ressources",
        panoply: "Panoplies",
    };
    const fromMeta = Array.isArray(metaEntityTypes.value) ? metaEntityTypes.value : [];
    return fromMeta
        .filter((e) => e?.type && allowed.has(String(e.type)) && configEntitiesByKey.value?.[String(e.type)])
        .map((e) => ({
            value: String(e.type),
            label: String(labelOverrides[String(e.type)] || e.label || e.type),
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

    // Filtres de types (sélection sur types connus) + mode
    if (supports("typeIds") || supports("typeIdsNot")) {
        q.set("type_mode", String(typeMode.value || "allowed"));

        if (String(typeMode.value) === "selected") {
            const includeTypeIds = supports("typeIds") ? (filterTypeIds.value || []) : [];
            const excludeTypeIds = supports("typeIdsNot") ? (filterTypeIdsNot.value || []) : [];
            if (Array.isArray(includeTypeIds) && includeTypeIds.length) q.set("typeIds", includeTypeIds.join(","));
            if (Array.isArray(excludeTypeIds) && excludeTypeIds.length) q.set("typeIdsNot", excludeTypeIds.join(","));
        }
    }

    if (supports("raceId")) {
        q.set("race_mode", String(raceMode.value || "allowed"));
        if (String(raceMode.value) === "selected") {
            const ids = filterRaceIds.value || [];
            if (Array.isArray(ids) && ids.length) q.set("raceIds", ids.join(","));
        } else if (String(filterRaceId.value || "").trim() !== "") {
            // fallback manuel (debug)
            q.set("raceId", String(filterRaceId.value).trim());
        }
    }
    if (supports("breedId") && String(filterBreedId.value || "").trim() !== "") q.set("breedId", String(filterBreedId.value).trim());
    if (supports("levelMin") && String(filterLevelMin.value || "").trim() !== "") q.set("levelMin", String(filterLevelMin.value).trim());
    if (supports("levelMax") && String(filterLevelMax.value || "").trim() !== "") q.set("levelMax", String(filterLevelMax.value).trim());

    // Pagination serveur : blocs de 100 (par défaut)
    q.set("page", String(Math.max(1, Math.floor(Number(pageNumber.value) || 1))));
    q.set("per_page", String(Math.max(1, Math.min(200, Math.floor(Number(perPage.value) || 100)))));

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
    await Promise.all([loadKnownTypes(), loadKnownRaces()]);
});

watch(
    () => selectedEntityType.value,
    async () => {
        // Reset UI state liée aux types + recharge la liste "connue"
        typeMode.value = "allowed";
        raceMode.value = "allowed";
        pageNumber.value = 1;
        showOptionsAndHistory.value = false;
        await Promise.all([loadKnownTypes(), loadKnownRaces()]);
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
    searching.value = true;
    selectedIds.value = new Set();
    try {
        const qs = buildSearchQuery();
        const url = `/api/scrapping/search/${selectedEntityType.value}${qs ? `?${qs}` : ""}`;
        pushHistory(`Recherche ${selectedEntityType.value} (${selectedEntityLabel.value}) : ${qs || "sans filtres"}`);

        const res = await fetch(url, { headers: { Accept: "application/json" } });
        const data = await res.json();

        if (res.ok && data.success) {
            rawItems.value = data.data?.items || [];
            lastMeta.value = data.data?.meta || null;
            const total = lastMeta.value?.total;
            const returned = rawItems.value.length;
            success(`Recherche OK (${returned} résultat(s))`);
            pushHistory(`→ OK: ${returned} résultat(s)${typeof total === "number" ? ` (total=${total})` : ""}.`);
        } else {
            showError(data.message || "Erreur lors de la recherche");
            pushHistory(`→ ERREUR: ${data.message || "recherche"}`);
        }
    } catch (e) {
        showError("Erreur lors de la recherche : " + e.message);
        pushHistory(`→ ERREUR: ${e.message}`);
    } finally {
        searching.value = false;
    }
};

const totalPages = computed(() => {
    const t = Number(lastMeta.value?.total_pages);
    if (Number.isFinite(t) && t > 0) return Math.floor(t);
    const total = Number(lastMeta.value?.total);
    const pp = Math.max(1, Math.floor(Number(perPage.value) || 100));
    if (Number.isFinite(total) && total > 0) return Math.ceil(total / pp);
    return null;
});
const canPrev = computed(() => Number(pageNumber.value) > 1 && !searching.value);
const canNext = computed(() => {
    const tp = totalPages.value;
    if (tp === null) return !searching.value; // best-effort
    return Number(pageNumber.value) < tp && !searching.value;
});

const goPrev = async () => {
    if (!canPrev.value) return;
    pageNumber.value = Math.max(1, Number(pageNumber.value) - 1);
    await runSearch();
};
const goNext = async () => {
    if (!canNext.value) return;
    pageNumber.value = Number(pageNumber.value) + 1;
    await runSearch();
};

const pageIndex = computed(() => Math.max(0, Math.floor(Number(pageNumber.value) || 1) - 1));
const pageCount = computed(() => {
    const tp = totalPages.value;
    if (tp === null) return Math.max(1, Math.floor(Number(pageNumber.value) || 1));
    return Math.max(1, tp);
});
const totalRows = computed(() => {
    const t = Number(lastMeta.value?.total);
    return Number.isFinite(t) && t > 0 ? Math.floor(t) : (rawItems.value?.length || 0);
});

const handlePaginationGo = async (pIdx) => {
    const next = Math.max(0, Math.floor(Number(pIdx) || 0));
    pageNumber.value = next + 1;
    await runSearch();
};
const handleSetPageSize = async (v) => {
    const n = Math.max(1, Math.min(200, Math.floor(Number(v) || 100)));
    perPage.value = n;
    pageNumber.value = 1;
    await runSearch();
};

/**
 * Parse la plage de pages saisie (ex: "1-6", "4,5", "1-3,5,7") ou retourne [1..totalPages] si "toutes".
 * @returns {number[]} Liste ordonnée de numéros de page (1-based), ou [] si invalide.
 */
const parsePageRange = () => {
    const tp = totalPages.value;
    if (importAllPages.value) {
        if (tp === null || tp < 1) return [];
        return Array.from({ length: tp }, (_, i) => i + 1);
    }
    const raw = String(pageRangeInput.value || "").trim();
    if (!raw) return [];
    const parts = raw.split(",").map((p) => p.trim()).filter(Boolean);
    const numbers = new Set();
    for (const part of parts) {
        const dash = part.indexOf("-");
        if (dash >= 0) {
            const a = Math.max(1, Math.floor(Number(part.slice(0, dash)) || 1));
            const b = Math.max(1, Math.floor(Number(part.slice(dash + 1)) || 1));
            const lo = Math.min(a, b);
            const hi = Math.max(a, b);
            for (let p = lo; p <= hi; p++) numbers.add(p);
        } else {
            const n = Math.max(1, Math.floor(Number(part) || 0));
            if (n > 0) numbers.add(n);
        }
    }
    return Array.from(numbers).sort((a, b) => a - b);
};

/**
 * Import (ou simulation) page par page : pour chaque page de la plage, charge la page puis envoie le batch.
 */
const runImportByPages = async (simulate = false) => {
    const pages = parsePageRange();
    if (pages.length === 0) {
        if (importAllPages.value) {
            showError("Lance une recherche d'abord pour connaître le nombre de pages.");
        } else {
            showError("Saisis une plage de pages (ex: 1-6 ou 4,5).");
        }
        return;
    }
    const csrf = getCsrfToken();
    if (!csrf) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        return;
    }
    const label = simulate ? "Simulation" : "Import";
    pushHistory(`${label} par pages (${selectedEntityType.value}) : pages ${pages.join(", ")}.`);
    importing.value = true;
    importByPagesProgress.value = `0/${pages.length}`;
    let totalSuccess = 0;
    let totalErrors = 0;
    let totalEntities = 0;
    const savedPageNumber = pageNumber.value;
    try {
        for (let i = 0; i < pages.length; i++) {
            const p = pages[i];
            importByPagesProgress.value = `${i + 1}/${pages.length}`;
            pageNumber.value = p;
            await runSearch();
            if (!rawItems.value?.length) {
                pushHistory(`→ Page ${p} : aucun résultat, ignorée.`);
                continue;
            }
            const payload = buildBatchPayload(simulate, "all");
            if (payload.entities.length < 1) {
                pushHistory(`→ Page ${p} : 0 entité, ignorée.`);
                continue;
            }
            totalEntities += payload.entities.length;
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
                const ok = s.success ?? 0;
                const err = s.errors ?? 0;
                totalSuccess += ok;
                totalErrors += err;
                pushHistory(`→ Page ${p} : ${ok}/${s.total ?? payload.entities.length} (erreurs: ${err})`);
            } else {
                totalErrors += payload.entities.length;
                pushHistory(`→ Page ${p} ERREUR: ${data.message || "batch"}`);
            }
        }
        success(`${label} par pages terminé : ${totalSuccess}/${totalEntities} (erreurs: ${totalErrors})`);
        pushHistory(`→ ${label.toUpperCase()} PAR PAGES OK: ${totalSuccess}/${totalEntities} (erreurs: ${totalErrors})`);
    } catch (e) {
        showError(`${label} par pages : ` + e.message);
        pushHistory(`→ ${label.toUpperCase()} PAR PAGES ERREUR: ${e.message}`);
    } finally {
        importing.value = false;
        importByPagesProgress.value = null;
        pageNumber.value = savedPageNumber;
        await runSearch();
    }
};

const buildBatchPayload = (dryRun, scope = "auto") => {
    // scope:
    // - auto: si une sélection existe -> sélection, sinon -> items visibles (filtre table)
    // - all: ignore la sélection et utilise tous les items chargés (rawItems)
    const ids =
        scope === "all"
            ? (rawItems.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n))
            : selectedCount.value
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

const runBatch = async (mode, scope = "auto") => {
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
    const payload = buildBatchPayload(dryRun, scope);
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

/** Type d'entité scrapping → segment de route entité (entities.XXX.show). */
const scrappingTypeToRouteEntityType = {
    monster: "monsters",
    resource: "resources",
    consumable: "consumables",
    equipment: "items",
    spell: "spells",
};

/** Segment table API → classe modèle (pour fromArray et toCell). */
const segmentToModel = {
    monsters: Monster,
    items: Item,
    spells: Spell,
    consumables: Consumable,
    resources: Resource,
};

const existsLabel = (it) => (it?.exists ? "Existe" : "Nouveau");
const existsTooltip = (it) => {
    if (!it?.exists) return "Aucune entrée trouvée en base (par dofusdb_id).";
    const internal = it?.existing?.id ? `ID Krosmoz: ${it.existing.id}` : "Entrée trouvée en base.";
    return internal;
};

/** Href vers la fiche entité si l'élément existe et qu'on a un type mappé, sinon "". */
const existsEntityHref = (it) => {
    if (!it?.exists || !it?.existing?.id) return "";
    const segment = scrappingTypeToRouteEntityType[String(selectedEntityType.value || "")];
    if (!segment) return "";
    return `/entities/${segment}/${it.existing.id}`;
};

// Modal de visualisation de l'entité existante (clic "Existe")
const entityModalOpen = ref(false);
const entityModalEntity = ref(null);
const entityModalEntityType = ref("");
const entityModalLoading = ref(false);
const entityModalLoadingId = ref(null);

/** Ouvre la modal d'affichage de l'entité en chargeant ses données depuis l'API table. */
const openEntityModal = async (it) => {
    if (!it?.exists || !it?.existing?.id) return;
    const segment = scrappingTypeToRouteEntityType[String(selectedEntityType.value || "")];
    if (!segment) return;
    const id = it.existing.id;
    entityModalLoadingId.value = id;
    entityModalLoading.value = true;
    entityModalEntity.value = null;
    entityModalEntityType.value = segment;
    try {
        const url = `${route(`api.tables.${segment}`)}?format=entities&limit=1&filters[id]=${encodeURIComponent(id)}`;
        const res = await fetch(url, { headers: { Accept: "application/json" } });
        const data = await res.json();
        const entities = data?.entities ?? [];
        const raw = entities[0] ?? null;
        if (raw) {
            const ModelClass = segmentToModel[segment];
            entityModalEntity.value = ModelClass ? (ModelClass.fromArray([raw])[0] ?? raw) : raw;
            entityModalOpen.value = true;
        } else {
            showError("Entité introuvable.");
        }
    } catch (e) {
        showError("Impossible de charger l'entité : " + (e?.message ?? "erreur"));
    } finally {
        entityModalLoading.value = false;
        entityModalLoadingId.value = null;
    }
};

const closeEntityModal = () => {
    entityModalOpen.value = false;
    entityModalEntity.value = null;
    entityModalEntityType.value = "";
};

const canAnalyzeEffects = computed(() => {
    const t = String(selectedEntityType.value || "");
    return (t === "spell" || t === "equipment" || t === "consumable" || t === "resource") && selectedCount.value > 0;
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

// Modal Comparer Krosmoz / DofusDB
const compareModalOpen = ref(false);
const compareEntityType = ref("");
const compareDofusdbId = ref(null);
const canCompare = computed(() => {
    if (selectedCount.value !== 1) return false;
    const id = Array.from(selectedIds.value)[0];
    return Number.isFinite(Number(id));
});
const openCompareModal = () => {
    if (!canCompare.value) return;
    const id = Array.from(selectedIds.value)[0];
    compareEntityType.value = String(selectedEntityType.value || "");
    compareDofusdbId.value = Number(id);
    compareModalOpen.value = true;
};
/** Ouvre la modal Comparer pour la ligne donnée (double-clic sur la ligne). */
const openCompareModalForRow = (it) => {
    const id = it?.id != null ? Number(it.id) : null;
    if (!Number.isFinite(id)) return;
    compareEntityType.value = String(selectedEntityType.value || "");
    compareDofusdbId.value = id;
    compareModalOpen.value = true;
};
const onCompareImported = () => {
    success("Import avec choix effectué.");
    pushHistory("→ Comparer / Import avec choix OK.");
};
</script>

<template>
    <div class="space-y-6">
        <Modal
            :open="typeManagerOpen"
            size="xl"
            placement="middle-center"
            close-on-esc
            @close="
                async () => {
                    typeManagerOpen = false;
                    await loadKnownTypes();
                }
            "
        >
            <template #header>
                <div class="flex items-center justify-between gap-3 w-full">
                    <div class="font-semibold text-primary-100">
                        {{ typeManagerConfig?.title || 'Gestion des types' }}
                    </div>
                    <Btn size="sm" variant="ghost" @click="typeManagerOpen = false">Fermer</Btn>
                </div>
            </template>

            <div v-if="typeManagerConfig" class="max-h-[75vh] overflow-y-auto pr-2">
                <TypeManagerTable
                    :title="typeManagerConfig.title"
                    :description="typeManagerConfig.description"
                    :mode="typeManagerConfig.mode"
                    :list-url="typeManagerConfig.listUrl"
                    :bulk-url="typeManagerConfig.bulkUrl"
                    :delete-url-base="typeManagerConfig.mode === 'decision'
                        ? (String(selectedEntityType) === 'resource'
                            ? '/api/scrapping/resource-types'
                            : String(selectedEntityType) === 'consumable'
                                ? '/api/scrapping/consumable-types'
                                : String(selectedEntityType) === 'equipment'
                                    ? '/api/scrapping/item-types'
                                    : '')
                        : (String(selectedEntityType) === 'monster'
                            ? '/api/types/monster-races'
                            : String(selectedEntityType) === 'spell'
                                ? '/api/types/spell-types'
                                : '')"
                />
            </div>
            <div v-else class="text-sm text-primary-300 italic">
                Cette entité n’a pas de gestionnaire de types/races.
            </div>
        </Modal>

        <CompareModal
            :open="compareModalOpen"
            :entity-type="compareEntityType"
            :dofusdb-id="compareDofusdbId"
            @close="compareModalOpen = false"
            @imported="onCompareImported"
        />

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
                    <SelectSearchField
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
                                Filtre par types
                            </div>
                            <div v-if="knownTypesLoading" class="text-xs text-primary-300 flex items-center gap-2">
                                <Loading />
                                <span>Chargement des types…</span>
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-3">
                            <SelectField
                                class="md:col-span-1"
                                label="Mode"
                                v-model="typeMode"
                                :options="typeModeOptions"
                                :disabled="knownTypesLoading"
                            />
                            <div class="md:col-span-2 flex items-start justify-between gap-3">
                                <div class="text-xs text-primary-300 flex items-center gap-2">
                                    <span v-if="String(typeMode) === 'all'">
                                        Tous les types DofusDB (utile pour détecter de nouveaux types → “À valider”).
                                    </span>
                                    <span v-else-if="String(typeMode) === 'allowed'">
                                        Uniquement les types validés (decision=allowed).
                                    </span>
                                    <span v-else>
                                        Uniquement les types cochés ci-dessous (types connus).
                                    </span>
                                </div>
                                <Btn
                                    size="sm"
                                    variant="outline"
                                    type="button"
                                    :disabled="!typeManagerConfig"
                                    title="Ouvrir le gestionnaire de types/races"
                                    @click="typeManagerOpen = true"
                                >
                                    Gérer les types
                                </Btn>
                            </div>
                        </div>

                        <div v-if="String(typeMode) === 'selected'" class="grid gap-3 md:grid-cols-2">
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
                    <div v-if="supports('raceId')" class="md:col-span-3 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-sm text-primary-300">
                                Filtre par races
                            </div>
                            <div v-if="knownRacesLoading" class="text-xs text-primary-300 flex items-center gap-2">
                                <Loading />
                                <span>Chargement des races…</span>
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-3">
                            <SelectField
                                class="md:col-span-1"
                                label="Mode"
                                v-model="raceMode"
                                :options="raceModeOptions"
                                :disabled="knownRacesLoading"
                            />

                            <div class="md:col-span-2 flex items-start justify-between gap-3">
                                <div class="text-xs text-primary-300 flex items-center gap-2">
                                    <span v-if="String(raceMode) === 'all'">Toutes les races DofusDB.</span>
                                    <span v-else-if="String(raceMode) === 'allowed'">Uniquement les races validées (state=playable).</span>
                                    <span v-else>Uniquement les races cochées ci-dessous (races validées).</span>
                                </div>
                                <Btn
                                    size="sm"
                                    variant="outline"
                                    type="button"
                                    :disabled="String(selectedEntityType) !== 'monster'"
                                    title="Ouvrir le gestionnaire de races"
                                    @click="typeManagerOpen = true"
                                >
                                    Gérer les races
                                </Btn>
                            </div>
                        </div>

                        <div v-if="String(raceMode) === 'selected'" class="grid gap-3 md:grid-cols-2">
                            <div class="space-y-2">
                                <div class="flex gap-2 items-end">
                                    <SelectField
                                        class="flex-1"
                                        label="Races (inclure)"
                                        v-model="selectedKnownRace"
                                        :options="knownRaceOptions"
                                        placeholder="Choisir une race…"
                                        :disabled="knownRacesLoading"
                                    />
                                    <Btn
                                        size="sm"
                                        variant="outline"
                                        type="button"
                                        :disabled="!selectedKnownRace"
                                        @click="addKnownRace"
                                    >
                                        Ajouter
                                    </Btn>
                                </div>

                                <div v-if="filterRaceIds.length" class="flex flex-wrap gap-2">
                                    <span
                                        v-for="id in filterRaceIds"
                                        :key="`race-${id}`"
                                        class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded border border-base-300 bg-base-200/40"
                                    >
                                        <span>{{ knownRaceOptions.find((o) => Number(o.value) === Number(id))?.label || `#${id}` }}</span>
                                        <button type="button" class="btn btn-ghost btn-xs" @click="removeKnownRace(id)">
                                            ✕
                                        </button>
                                    </span>
                                </div>
                                <div v-else class="text-xs text-primary-300 italic">
                                    Aucune race incluse.
                                </div>
                            </div>

                            <InputField
                                v-model="filterRaceId"
                                label="raceId (manuel)"
                                type="number"
                                helper="Optionnel : utile pour debug (non recommandé)."
                            />
                        </div>
                        <div v-else class="hidden">
                            <!-- Pas d'UI additionnelle en mode all/allowed -->
                        </div>
                    </div>
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
                    <div class="md:col-span-4">
                        <TanStackTablePagination
                            :page-index="pageIndex"
                            :page-count="pageCount"
                            :page-size="Math.max(1, Math.min(200, Math.floor(Number(perPage) || 100)))"
                            :total-rows="totalRows"
                            :per-page-options="[50, 100, 200]"
                            :can-prev="canPrev"
                            :can-next="canNext"
                            ui-size="sm"
                            ui-color="primary"
                            @prev="goPrev"
                            @next="goNext"
                            @first="async () => { pageNumber = 1; await runSearch(); }"
                            @last="async () => { if (totalPages !== null) { pageNumber = totalPages; await runSearch(); } }"
                            @go="handlePaginationGo"
                            @set-page-size="handleSetPageSize"
                        />
                        <div class="mt-1 text-xs text-primary-300">
                            <span>Pagination serveur : bloc de {{ perPage }} résultat(s) par page.</span>
                            <span v-if="lastMeta && typeof lastMeta.total === 'number'">
                                · Total filtré : <span class="font-semibold">{{ lastMeta.total }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <Btn color="primary" :disabled="searching" @click="runSearch">
                        <Loading v-if="searching" class="mr-2" />
                        <Icon v-else source="fa-solid fa-magnifying-glass" alt="Rechercher" pack="solid" class="mr-2" />
                        Rechercher
                    </Btn>
                    <div v-if="lastMeta" class="text-xs text-primary-300">
                        <Badge :content="String(lastMeta.returned ?? rawItems.length)" color="primary" />
                        <span class="ml-2">retourné(s)</span>
                        <template v-if="typeof lastMeta.total === 'number'">
                            <span class="mx-2">/</span>
                            <Badge :content="String(lastMeta.total)" color="neutral" />
                            <span class="ml-2">total</span>
                        </template>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Options + historique (au-dessus des résultats), masqué par défaut -->
        <Card class="p-6 space-y-4">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-primary-100">Options & historique</h3>
                    <p class="text-xs text-primary-300 mt-1">
                        Masqué par défaut pour garder l’interface légère.
                    </p>
                </div>
                <Btn size="sm" variant="outline" @click="showOptionsAndHistory = !showOptionsAndHistory">
                    {{ showOptionsAndHistory ? "Masquer" : "Afficher" }}
                </Btn>
            </div>

            <div v-if="showOptionsAndHistory" class="grid gap-6 lg:grid-cols-2">
                <Card class="p-6 space-y-3">
                    <h4 class="font-semibold text-primary-100">Options d’import</h4>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <CheckboxField v-model="optSkipCache" label="Ignorer le cache" />
                        <CheckboxField v-model="optWithImages" label="Inclure les images" />
                        <CheckboxField v-model="optIncludeRelations" label="Inclure les relations" />
                        <CheckboxField v-model="optForceUpdate" label="Écraser si existe déjà" />
                        <CheckboxField v-model="optManualChoice" label="Choix manuel (validation uniquement)" />
                    </div>
                    <p class="text-xs text-primary-300">
                        “Simuler” = dry-run. “Choix manuel” = ne fait pas l’intégration (validate_only).
                    </p>
                </Card>

                <Card class="p-6 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="font-semibold text-primary-100">Historique</h4>
                        <Btn size="sm" variant="ghost" :disabled="!historyLines.length" @click="historyLines = []">
                            Vider
                        </Btn>
                    </div>
                    <pre class="text-xs bg-base-300/30 border border-base-300 rounded p-3 max-h-80 overflow-auto whitespace-pre-wrap break-words">{{ historyLines.join("\n") }}</pre>
                </Card>
            </div>
        </Card>

        <!-- Corps: tableau -->
        <Card class="p-6 space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-primary-100">Résultats</h3>
                    <Badge :content="String(visibleItems.length)" color="neutral" />
                    <span v-if="lastMeta && typeof lastMeta.total === 'number'" class="text-sm text-primary-300">
                        · total filtré: {{ lastMeta.total }}
                    </span>
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
                        color="success"
                        variant="outline"
                        :disabled="importing || !rawItems.length"
                        title="Importe tous les résultats chargés (ignore la sélection et le filtre du tableau)"
                        @click="runBatch('import', 'all')"
                    >
                        <Loading v-if="importing" class="mr-2" />
                        Tout importer
                    </Btn>
                    <div class="flex flex-wrap items-center gap-2 border-l border-base-300 pl-2">
                        <InputField
                            v-model="pageRangeInput"
                            label="Pages"
                            :disabled="importAllPages || importing"
                            placeholder="ex: 1-6 ou 4,5"
                            class="w-36"
                        />
                        <CheckboxField v-model="importAllPages" :disabled="importing" label="Toutes les pages" />
                        <Btn
                            color="secondary"
                            size="sm"
                            :disabled="importing"
                            title="Simule l'import page par page (plage ou toutes)"
                            @click="runImportByPages(true)"
                        >
                            <Loading v-if="importing" class="mr-2" />
                            {{ importByPagesProgress ? `Page ${importByPagesProgress}` : "Simuler par pages" }}
                        </Btn>
                        <Btn
                            color="success"
                            size="sm"
                            :disabled="importing"
                            title="Charge chaque page puis importe (plage ou toutes)"
                            @click="runImportByPages(false)"
                        >
                            <Loading v-if="importing" class="mr-2" />
                            {{ importByPagesProgress ? `Page ${importByPagesProgress}` : "Importer par pages" }}
                        </Btn>
                    </div>
                    <Btn
                        variant="ghost"
                        :disabled="effectsAnalysisLoading || !canAnalyzeEffects"
                        @click="analyzeEffects"
                        title="Disponible pour équipement/consommable/ressource/sort (sur l’ID sélectionné)"
                    >
                        <Loading v-if="effectsAnalysisLoading" class="mr-2" />
                        Analyser effets (non mappés)
                    </Btn>
                    <Btn
                        variant="outline"
                        :disabled="!canCompare"
                        title="Comparer l'existant Krosmoz avec DofusDB et choisir par propriété (1 élément sélectionné)"
                        @click="openCompareModal"
                    >
                        Comparer Krosmoz / DofusDB
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
                        <th v-if="supports('raceId')" class="w-56">Race</th>
                            <th v-if="supports('breedId')" class="w-24">breedId</th>
                            <th v-if="supports('levelMin') || supports('levelMax')" class="w-24">Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="it in visibleItems"
                            :key="String(it.id)"
                            class="cursor-pointer hover:bg-base-200/50"
                            @dblclick="openCompareModalForRow(it)"
                        >
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
                                        <button
                                            v-if="existsEntityHref(it)"
                                            type="button"
                                            class="text-xs px-2 py-1 rounded border border-success/30 bg-success/10 text-success hover:bg-success/20 hover:underline cursor-pointer"
                                            :disabled="entityModalLoading && entityModalLoadingId === it.existing?.id"
                                            @click="openEntityModal(it)"
                                        >
                                            <span v-if="entityModalLoading && entityModalLoadingId === it.existing?.id">Chargement…</span>
                                            <span v-else>{{ existsLabel(it) }}</span>
                                        </button>
                                        <span
                                            v-else
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
                            <td v-if="supports('raceId')">
                                <span class="font-semibold">{{ it.raceName ?? "—" }}</span>
                                <span v-if="it.raceName === null && (it.raceId ?? it.race) !== undefined" class="text-xs text-primary-300 ml-2">
                                    ({{ it.raceId ?? it.race }})
                                </span>
                            </td>
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

        <!-- Modal de visualisation de l'entité existante (clic "Existe") -->
        <EntityModal
            v-if="entityModalEntity"
            :entity="entityModalEntity"
            :entity-type="entityModalEntityType"
            :open="entityModalOpen"
            :use-stored-format="true"
            @close="closeEntityModal"
        />
    </div>
</template>

