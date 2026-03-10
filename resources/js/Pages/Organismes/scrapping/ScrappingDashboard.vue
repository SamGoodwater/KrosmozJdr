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
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import TypeManagerTable from "@/Pages/Organismes/type-management/TypeManagerTable.vue";
import CompareModal from "@/Pages/Organismes/scrapping/CompareModal.vue";
import ScrappingFilters from "@/Pages/Organismes/scrapping/ScrappingFilters.vue";
import ScrappingOptionsPanel from "@/Pages/Organismes/scrapping/ScrappingOptionsPanel.vue";
import ScrappingResultsTable from "@/Pages/Organismes/scrapping/ScrappingResultsTable.vue";
import TanStackTablePagination from "@/Pages/Molecules/table/TanStackTablePagination.vue";
import EntityModal from "@/Pages/Organismes/entity/EntityModal.vue";
import { Monster } from "@/Models/Entity/Monster";
import { Item } from "@/Models/Entity/Item";
import { Spell } from "@/Models/Entity/Spell";
import { Consumable } from "@/Models/Entity/Consumable";
import { Resource } from "@/Models/Entity/Resource";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import {
    useScrappingPreferences,
    DEFAULTS as SCRAP_DEFAULTS,
    loadScrappingPreferences,
} from "@/Composables/store/useScrappingPreferences";
import {
    downloadCsvFromRows,
    filenameForBatchErrors,
    buildCsvFromErrorResults,
} from "@/Composables/utils/useCsvDownload";
import { RELATION_TYPE_LABELS } from "@/config/scrapping/relationConfig";
import { getCsrfToken, getJson, postJson } from "@/utils/scrapping/api";
import { useScrappingItemStatus } from "@/Composables/scrapping/useScrappingItemStatus";
import { useScrappingSearch } from "@/Composables/scrapping/useScrappingSearch";
import { useScrappingPreview } from "@/Composables/scrapping/useScrappingPreview";
import { useScrappingCompare } from "@/Composables/scrapping/useScrappingCompare";
import { useScrappingBatch } from "@/Composables/scrapping/useScrappingBatch";
import { useScrappingJobManager } from "@/Composables/scrapping/useScrappingJobManager";
import { getEntityConfigStatus } from "@/Composables/scrapping/useScrappingEntityConfigStatus";

const _initialScrapPrefs = loadScrappingPreferences();
const _pref = (k) => _initialScrapPrefs[k] ?? SCRAP_DEFAULTS[k];

const notificationStore = useNotificationStore();
const { success, error: showError, info } = notificationStore;
const jobManager = useScrappingJobManager();

const scrapJobsList = ref({ active: [], recent_finished: [] });
const scrapJobsLoading = ref(false);
const scrapJobsPollInterval = ref(null);
const SCRAP_JOBS_POLL_MS = 2500;

async function fetchScrapJobs() {
    scrapJobsLoading.value = true;
    try {
        const result = await getJson("/api/scrapping/jobs");
        if (result.ok && result.data?.success) {
            scrapJobsList.value = result.data?.data ?? { active: [], recent_finished: [] };
        }
    } finally {
        scrapJobsLoading.value = false;
    }
}

function startScrapJobsPolling() {
    if (scrapJobsPollInterval.value) return;
    scrapJobsPollInterval.value = setInterval(() => {
        if ((scrapJobsList.value?.active?.length ?? 0) > 0) {
            fetchScrapJobs();
        } else {
            clearInterval(scrapJobsPollInterval.value);
            scrapJobsPollInterval.value = null;
        }
    }, SCRAP_JOBS_POLL_MS);
}

function stopScrapJobsPolling() {
    if (scrapJobsPollInterval.value) {
        clearInterval(scrapJobsPollInterval.value);
        scrapJobsPollInterval.value = null;
    }
}

async function cancelScrapJob(jobId) {
    const token = getCsrfToken();
    if (!token) return;
    try {
        const res = await postJson(
            `/api/scrapping/jobs/${encodeURIComponent(jobId)}/cancel`,
            {},
            { headers: { "X-CSRF-TOKEN": token } }
        );
        if (res.ok) {
            success("Job annulé.");
            await fetchScrapJobs();
        } else {
            showError(res.error || "Impossible d'annuler le job");
        }
    } catch (e) {
        showError("Erreur : " + (e?.message ?? "annulation impossible"));
    }
}

function scrapJobStatusLabel(status) {
    const map = { queued: "En attente", running: "En cours", succeeded: "Terminé", failed: "Échec", cancelled: "Annulé" };
    return map[status] ?? status;
}

function scrapJobStatusColor(status) {
    if (status === "running" || status === "queued") return "info";
    if (status === "succeeded") return "success";
    if (status === "failed") return "error";
    if (status === "cancelled") return "warning";
    return "neutral";
}

const hasActiveScrapJobs = computed(() => (scrapJobsList.value?.active?.length ?? 0) > 0);
const hasAnyScrapJobs = computed(
    () => hasActiveScrapJobs.value || (scrapJobsList.value?.recent_finished?.length ?? 0) > 0
);

const loadingMeta = ref(true);
const loadingConfig = ref(true);
const configLoadError = ref("");
const configHealth = ref(null);

const metaEntityTypes = ref([]);
const configEntitiesByKey = ref({});
const selectedEntityType = ref(_pref("selectedEntityType"));
const showOnlyErrorEntities = ref(false);

/** Charge les métadonnées des types d'entité (liste pour le select Entité). */
const loadMeta = async () => {
    loadingMeta.value = true;
    try {
        const result = await getJson("/api/scrapping/meta");
        if (result.ok && result.data?.success) {
            metaEntityTypes.value = result.data?.data ?? [];
        } else {
            showError(result.error || "Impossible de charger les métadonnées des entités");
        }
    } catch (e) {
        showError("Métadonnées : " + (e?.message ?? "erreur"));
    } finally {
        loadingMeta.value = false;
    }
};

/** Charge la config scrapping par type d'entité (filtres supportés, etc.). */
const loadConfig = async () => {
    loadingConfig.value = true;
    configLoadError.value = "";
    configHealth.value = null;
    try {
        const result = await getJson("/api/scrapping/config");
        if (result.ok && result.data?.success) {
            const map = {};
            const entities = result.data?.data?.entities ?? [];
            for (const e of entities) {
                if (e?.entity) map[String(e.entity)] = e;
            }
            configEntitiesByKey.value = map;
            configHealth.value = result.data?.data?.health ?? null;
        } else {
            configEntitiesByKey.value = {};
            configLoadError.value = result.error || "Impossible de charger la config scrapping";
            showError(configLoadError.value);
        }
    } catch (e) {
        configEntitiesByKey.value = {};
        configLoadError.value = "Config : " + (e?.message ?? "erreur");
        showError(configLoadError.value);
    } finally {
        loadingConfig.value = false;
    }
};

onMounted(async () => {
    await loadMeta();
    await loadConfig();
    await fetchScrapJobs();
    if (hasActiveScrapJobs.value) startScrapJobsPolling();
    const url = new URL(window.location.href);
    if (url.searchParams.get("resume") === "1") {
        await nextTick();
        await runSearchAndPreview();
        if (typeof history !== "undefined" && history.replaceState) {
            history.replaceState(null, "", url.pathname);
        }
    }
});

// Gestion des types/races (modal)
const typeManagerOpen = ref(false);
/** Incrémenté à l'ouverture du modal « Gérer les types » et après une recherche pour forcer le rechargement de la liste. */
const typeManagerRefreshTrigger = ref(0);
function handleOpenTypeManager() {
    typeManagerOpen.value = true;
    typeManagerRefreshTrigger.value += 1;
}
const typeManagerConfig = computed(() => {
    const t = selectedEntityTypeStr.value;
    if (t === "resource") {
        return {
            title: "Types DofusDB (Ressources)",
            description: "Décider quels typeId DofusDB sont autorisés pour l’import de ressources.",
            mode: "decision",
            listUrl: "/api/scrapping/resource-types",
            bulkUrl: "/api/scrapping/resource-types/bulk",
            moveCategoryUrlBase: "/api/scrapping/resource-types",
            currentCategory: "resource",
        };
    }
    if (t === "consumable") {
        return {
            title: "Types DofusDB (Consommables)",
            description: "Décider quels typeId DofusDB sont autorisés pour l’import de consommables.",
            mode: "decision",
            listUrl: "/api/scrapping/consumable-types",
            bulkUrl: "/api/scrapping/consumable-types/bulk",
            moveCategoryUrlBase: "/api/scrapping/consumable-types",
            currentCategory: "consumable",
        };
    }
    if (t === "equipment") {
        return {
            title: "Types DofusDB (Équipements)",
            description: "Décider quels typeId DofusDB sont autorisés pour l’import d’équipements.",
            mode: "decision",
            listUrl: "/api/scrapping/item-types",
            bulkUrl: "/api/scrapping/item-types/bulk",
            moveCategoryUrlBase: "/api/scrapping/item-types",
            currentCategory: "equipment",
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

// Filtres principaux (persistés)
const filterIds = ref(_pref("filterIds"));
const filterName = ref(_pref("filterName"));

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

// Options d'import (UI) — valeur initiale depuis localStorage (déjà migré) ou défauts
const optIncludeRelations = ref(_pref("optIncludeRelations"));
const optUpdateMode = ref(_pref("optUpdateMode"));
const optPropertyWhitelist = ref(_pref("optPropertyWhitelist"));
const optPropertyBlacklist = ref(_pref("optPropertyBlacklist"));
const optSkipCache = ref(_pref("optSkipCache"));
const optForceUpdate = ref(_pref("optForceUpdate"));
const optManualChoice = ref(_pref("optManualChoice"));

// Résultats
const tableSearch = ref("");
const selectedIds = ref(new Set());
const expandedRowKey = ref(null);

// Analyse des effets (unmapped)
const effectsAnalysisLoading = ref(false);
const effectsAnalysisEntityId = ref(null);
const effectsAnalysisType = ref(null);
const effectsAnalysisUnmapped = ref([]);
const effectsAnalysisSummary = ref(null);

/** Fallback statique id → nom (keywords DofusDB). Synchronisé avec dofusdb_characteristic_to_krosmoz.json et DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md. */
const DEFAULT_CHARACTERISTIC_LABELS = {
    "-1": "unknown",
    0: "hitPoints",
    1: "actionPoints",
    3: "statsPoints",
    4: "spellsPoints",
    5: "level",
    10: "strength",
    11: "vitality",
    12: "wisdom",
    13: "chance",
    14: "agility",
    15: "intelligence",
    16: "allDamageBonus",
    17: "damageFactor",
    18: "criticalHit",
    19: "range",
    20: "magicalReduction",
    21: "physicalReduction",
    22: "experienceBoost",
    23: "movementPoints",
    24: "invisibility",
    25: "damagePercent",
    26: "maxSummonedCreaturesBoost",
    27: "DodgeApLostProbability",
    28: "DodgeMpLostProbability",
    29: "energyPoints",
    30: "alignementValue",
    31: "weaponDamagePercent",
    32: "physicalDamageBonus",
    33: "earthElementResistPercent",
    34: "fireElementResistPercent",
    35: "waterElementResistPercent",
    36: "airElementResistPercent",
    37: "neutralElementResistPercent",
    39: "criticalMiss",
    40: "weight",
    41: "restrictionOnPlayer",
    42: "restrictionOnOthers",
    43: "alignementSide",
    44: "initiative",
    45: "shopPercentReduction",
    46: "alignementRank",
    47: "maxEnergyPoints",
    48: "magicFind",
    49: "healBonus",
    50: "reflectDamage",
    51: "energyLoose",
    52: "honourPoints",
    53: "disgracePoints",
    54: "earthElementReduction",
    55: "fireElementReduction",
    56: "waterElementReduction",
    57: "airElementReduction",
    58: "neutralElementReduction",
    69: "trapDamageBonusPercent",
    70: "trapDamageBonus",
    71: "fakeSkillForStates",
    72: "soulCaptureBonus",
    73: "rideXPBonus",
    74: "confusion",
    75: "permanentDamagePercent",
    76: "unlucky",
    77: "maximizeRoll",
    78: "tackleEvade",
    79: "tackleBlock",
    80: "allianceAutoAgressRange",
    81: "allianceAutoAgressResist",
    82: "apReduction",
    83: "mpReduction",
    84: "pushDamageBonus",
    85: "pushDamageReduction",
    86: "criticalDamageBonus",
    87: "criticalDamageReduction",
    88: "earthDamageBonus",
    89: "fireDamageBonus",
    90: "waterDamageBonus",
    91: "airDamageBonus",
    92: "neutralDamageBonus",
    93: "maxBomb",
    94: "bombComboBonus",
    95: "maxLifePoints",
    96: "shield",
    97: "hitPointLoss",
    98: "damagePercentSpell",
    99: "extraScale",
    100: "passTurn",
    101: "resistPercent",
    102: "curPermanentDamage",
    103: "weaponPower",
    104: "incomingPercentDamageMultiplicator",
    105: "incomingPercentHealMultiplicator",
    106: "glyphPower",
    107: "dealtDamageMultiplier",
    108: "stopXP",
    109: "hunter",
    110: "runePower",
    120: "dealtDamageMultiplierDistance",
    121: "receivedDamageMultiplierDistance",
    122: "dealtDamageMultiplierWeapon",
    123: "dealtDamageMultiplierSpells",
    124: "receivedDamageMultiplierMelee",
    125: "dealtDamageMultiplierMelee",
    126: "agilityInitialPercent",
    127: "strengthInitialPercent",
    128: "chanceInitialPercent",
    129: "intelligenceInitialPercent",
    130: "vitalityInitialPercent",
    131: "wisdomInitialPercent",
    132: "tackleBlockInitialPercent",
    133: "tackleEvadeInitialPercent",
    134: "actionPointsInitialPercent",
    135: "movementPointsInitialPercent",
    136: "apAttackInitialPercent",
    137: "mpAttackInitialPercent",
    138: "dodgeApLostProbabilityInitialPercent",
    139: "dodgeMpLostProbabilityInitialPercent",
    140: "extraScalePercent",
    141: "receivedDamageMultiplierSpells",
    142: "receivedDamageMultiplierWeapon",
    143: "dealtHealMultiplier",
    150: "allDamageMultiplier",
    158: "pushDamagePercent",
    199: "StopDrop",
};

const pageNumber = ref(1);
const perPage = ref(_pref("perPage"));

// Préférences persistées (localStorage)
const prefsRefs = {
    selectedEntityType,
    optIncludeRelations,
    optUpdateMode,
    optSkipCache,
    optForceUpdate,
    optManualChoice,
    perPage,
    filterIds,
    filterName,
    optPropertyWhitelist,
    optPropertyBlacklist,
};
const { hydrate: hydratePrefs } = useScrappingPreferences(prefsRefs);
// Appliquer les préférences (et défauts) avant le premier rendu pour que le toggle/radios affichent la bonne valeur
hydratePrefs();

// Import par plage de pages (ex: "1-6" ou "4,5" ou toutes)
const pageRangeInput = ref("");
/** Périmètre pour Simuler / Importer : 'selection' | 'all' | 'pages' */
const batchScope = ref("selection");

// Historique (console)
const historyLines = jobManager.historyLines;
const lastRunId = ref(null);
const showOptionsAndHistory = ref(false); // masqué par défaut

const pushHistory = (line) => {
    jobManager.appendHistory(line);
};

const copyLastRunId = async () => {
    const runId = String(currentRunId.value || "").trim();
    if (!runId) {
        showError("Aucun run_id à copier.");
        return;
    }
    try {
        await navigator.clipboard.writeText(runId);
        success("run_id copié.");
    } catch {
        showError("Impossible de copier le run_id.");
    }
};

const loadKnownTypes = async () => {
    const t = selectedEntityTypeStr.value;
    const endpoint = (() => {
        if (t === "resource") return "/api/scrapping/resource-types";
        if (t === "consumable") return "/api/scrapping/consumable-types";
        if (t === "equipment") return "/api/scrapping/item-types";
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
        const result = await getJson(endpoint);
        if (!result.ok || !result.data?.success) {
            showError(result.error || result.data?.message || "Chargement des types impossible");
            return;
        }
        const rows = Array.isArray(result.data?.data) ? result.data.data : [];
        knownTypeOptions.value = rows
            .map((r) => ({
                value: Number(r?.dofusdb_type_id),
                label: String(r?.name || `DofusDB type #${r?.dofusdb_type_id}`),
            }))
            .filter((o) => Number.isFinite(Number(o.value)) && Number(o.value) > 0)
            .sort((a, b) => String(a.label).localeCompare(String(b.label), "fr-FR"));
    } catch (e) {
        showError("Types: " + (e?.message ?? "erreur"));
    } finally {
        knownTypesLoading.value = false;
    }
};

const loadKnownRaces = async () => {
    const t = selectedEntityTypeStr.value;
    knownRaceOptions.value = [];
    selectedKnownRace.value = "";
    filterRaceIds.value = [];

    if (t !== "monster") return;

    knownRacesLoading.value = true;
    try {
        const result = await getJson("/api/types/monster-races");
        if (!result.ok || !result.data?.success) {
            showError(result.error || result.data?.message || "Chargement des races impossible");
            return;
        }
        const rows = Array.isArray(result.data?.data) ? result.data.data : [];
        knownRaceOptions.value = rows
            .map((r) => ({
                value: Number(r?.dofusdb_race_id),
                label: String(r?.name || `#${r?.dofusdb_race_id}`),
            }))
            .filter((o) => Number.isFinite(Number(o.value)) && Number(o.value) !== 0)
            .sort((a, b) => String(a.label).localeCompare(String(b.label), "fr-FR"));
    } catch (e) {
        showError("Races: " + (e?.message ?? "erreur"));
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

const handleTypeManagerClose = async () => {
    typeManagerOpen.value = false;
    const t = selectedEntityTypeStr.value;
    if (t === "resource" || t === "consumable" || t === "equipment") await loadKnownTypes();
    if (t === "monster") await loadKnownRaces();
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

/** Type d'entité normalisé en chaîne (évite [object Object] si le select émet un objet). */
const selectedEntityTypeStr = computed(() => {
    const v = selectedEntityType.value;
    if (typeof v === "string") return v;
    if (v && typeof v === "object" && typeof v.value === "string") return v.value;
    return String(v ?? "");
});

watch(
    () => selectedEntityTypeStr.value,
    (t) => {
        if (t === "monster") loadKnownRaces();
    },
    { immediate: true }
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
        // Ne pas dépendre strictement de /api/scrapping/config : si ce endpoint échoue,
        // on garde un select d'entités utilisable basé sur /api/scrapping/meta.
        .filter((e) => e?.type && allowed.has(String(e.type)))
        .map((e) => {
            const type = String(e.type);
            const baseLabel = String(labelOverrides[type] || e.label || type);
            const { hasConfigError } = getEntityConfigStatus(configEntitiesByKey.value, type);
            return {
                value: type,
                label: hasConfigError ? `${baseLabel} (!)` : baseLabel,
                hasConfigError,
            };
        })
        .filter((opt) => !showOnlyErrorEntities.value || opt.hasConfigError)
        .map(({ value, label }) => ({ value, label }));
});

const entitiesWithConfigError = computed(() => {
    const entries = Object.entries(configEntitiesByKey.value || {});
    return entries
        .filter(([, cfg]) => Boolean(cfg?.configError))
        .map(([entity, cfg]) => {
            const warnings = Array.isArray(cfg?.mappingDiagnostics?.warnings) ? cfg.mappingDiagnostics.warnings : [];
            const blockingWarning = warnings.find((w) => w?.severity === "blocking") || null;
            return {
                entity,
                label: String(cfg?.label || entity),
                message: String(cfg?.configError || ""),
                actionUrl: blockingWarning?.actionUrl || "",
            };
        });
});
const entitiesWithConfigErrorCount = computed(() => entitiesWithConfigError.value.length);

watch(
    [entityOptions, () => selectedEntityTypeStr.value],
    ([options, current]) => {
        const values = (Array.isArray(options) ? options : []).map((o) => String(o?.value ?? ""));
        if (values.length === 0) {
            return;
        }
        if (!values.includes(String(current || ""))) {
            selectedEntityType.value = values[0];
        }
    },
    { immediate: true }
);

// Composables Phase 1
const status = useScrappingItemStatus({ entityTypeRef: selectedEntityTypeStr });
const search = useScrappingSearch({
    entityTypeRef: selectedEntityTypeStr,
    configRef: configEntitiesByKey,
    filterRefs: {
        filterIds,
        filterName,
        optSkipCache,
        typeMode,
        filterTypeIds,
        filterTypeIdsNot,
        raceMode,
        filterRaceIds,
        filterRaceId,
        filterBreedId,
        filterLevelMin,
        filterLevelMax,
        pageNumber,
        perPage,
    },
    notifyError: showError,
});
const preview = useScrappingPreview({
    entityTypeRef: selectedEntityTypeStr,
    rawItemsRef: search.rawItems,
    notifyError: showError,
    getCsrfToken,
    itemStatusByKeyRef: status.itemStatusByKey,
    includeRelationsRef: optIncludeRelations,
    onProgress: (p) => {
        jobManager.updateProgress({
            phase: p.phase,
            done: p.done,
            total: p.total,
            label: p.phase === "relations" ? "Chargement des relations" : "Conversion des valeurs",
        });
    },
    onRunMeta: ({ runId, unknownCharacteristics, jobId }) => {
        if (runId) lastRunId.value = runId;
        jobManager.setRunMeta(runId ?? null, unknownCharacteristics ?? null, jobId ?? null);
    },
});
const compare = useScrappingCompare({
    convertedByItemIdRef: preview.convertedByItemId,
    configRef: configEntitiesByKey,
    entityTypeRef: selectedEntityTypeStr,
});

const supportedFilters = computed(() => {
    const cfg = configEntitiesByKey.value?.[selectedEntityTypeStr.value] || null;
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
    const items = Array.isArray(search.rawItems.value) ? search.rawItems.value : [];
    const q = String(tableSearch.value || "").trim().toLowerCase();
    const filtered = !q
        ? items
        : items.filter((it) => {
              const norm = (v) => String(v ?? "").toLowerCase();
              const id = norm(it?.id);
              const name = norm(it?.name?.fr || it?.name?.en || it?.name);
              return id.includes(q) || name.includes(q);
          });
    const byId = preview.convertedByItemId.value;
    return filtered.map((it) => ({
        ...it,
        exists: !!byId[Number(it?.id)]?.existing,
        existing: byId[Number(it?.id)]?.existing?.record ?? null,
    }));
});

/** Données de conversion pour une ligne (entité principale ou relation). */
function getConvertedForRow(row) {
    if (row?.isRelation && row?.relation) {
        return preview.convertedByRelationKey?.value?.[`${row.relation.type}-${row.relation.id}`] ?? null;
    }
    const id = Number(row?.item?.id);
    return Number.isFinite(id) ? (preview.convertedByItemId?.value?.[id] ?? null) : null;
}

/** Clé d’expansion unique (entité ou relation). */
function getExpandKey(row) {
    if (row?.isRelation && row?.relation) return `${row.relation.type}-${row.relation.id}`;
    return row?.item?.id != null ? String(row.item.id) : "";
}

/** Lignes du tableau : chaque item suivi de ses relations (si "Inclure les relations" activé). */
const visibleRowsWithRelations = computed(() => {
    const items = visibleItems.value;
    const includeRelations = optIncludeRelations.value !== false;
    const relByKey = preview.lastBatchRelationsByKey.value;
    const convRel = preview.convertedByRelationKey?.value ?? {};
    const entityType = selectedEntityTypeStr.value;
    const out = [];
    for (const it of items) {
        out.push({ isRelation: false, item: it });
        if (!includeRelations) continue;
        const key = `${entityType}-${Number(it?.id)}`;
        const relations = relByKey[key];
        if (Array.isArray(relations) && relations.length) {
            for (const rel of relations) {
                const rk = `${rel.type}-${rel.id}`;
                const data = convRel[rk];
                const nameFromConverted = data?.converted && compare?.convertedName
                    ? compare.convertedName(data.converted, rel.type)
                    : null;
                const block = data?.converted && compare?.extractFirstBlock
                    ? compare.extractFirstBlock(data.converted)
                    : null;
                const syntheticItem = {
                    id: rel.id,
                    type: rel.type,
                    name: nameFromConverted ?? undefined,
                    exists: !!data?.existing,
                    existing: data?.existing?.record ?? null,
                    typeId: block?.type_id ?? block?.typeId ?? undefined,
                    typeName: block?.type_name ?? block?.typeName ?? (block?.type?.name != null ? String(block.type.name) : undefined),
                    raceId: block?.race_id ?? block?.raceId ?? undefined,
                    raceName: block?.race_name ?? block?.raceName ?? (block?.race?.name != null ? String(block.race.name) : undefined),
                };
                out.push({ isRelation: true, parent: it, relation: rel, item: syntheticItem });
            }
        }
    }
    return out;
});

/** Libellé du type de relation ; utilise le type résolu (resource/consumable/equipment) quand disponible. */
function relationTypeLabel(rel, resolvedEntityType = null) {
    const type = resolvedEntityType ?? rel?.type;
    return RELATION_TYPE_LABELS[type] ?? type ?? "—";
}

const batch = useScrappingBatch({
    entityTypeRef: selectedEntityTypeStr,
    rawItemsRef: search.rawItems,
    visibleItemsRef: visibleItems,
    selectedIdsRef: selectedIds,
    batchScopeRef: batchScope,
    pageRangeRef: pageRangeInput,
    pageNumberRef: pageNumber,
    getTotalPages: () => {
        const tp = search.totalPages?.value ?? null;
        if (tp != null && Number.isFinite(tp)) return Math.max(1, Math.floor(tp));
        return Math.max(1, Math.floor(Number(pageNumber.value) || 1));
    },
    optUpdateMode,
    optIncludeRelations,
    optPropertyWhitelist,
    optPropertyBlacklist,
    optSkipCache,
    optManualChoice,
    getCsrfToken,
    setStatusForEntities: status.setStatusForEntities,
    setStatusFromBatchResults: status.setStatusFromBatchResults,
    lastBatchRelationsByKeyRef: preview.lastBatchRelationsByKey,
    notifyError: showError,
    notifySuccess: success,
    notifyInfo: info,
    pushHistory,
    runSearch: (opts = {}) => runSearchAndPreview(opts),
    onBatchErrors: () => { showOptionsAndHistory.value = true; },
    onProgress: (p) => {
        jobManager.updateProgress({
            phase: p.phase,
            done: p.done,
            total: p.total,
            label: p.label,
        });
    },
    onRunMeta: ({ runId, unknownCharacteristics, jobId }) => {
        if (runId) lastRunId.value = runId;
        jobManager.setRunMeta(runId ?? null, unknownCharacteristics ?? null, jobId ?? null);
    },
});

watch(
    () => batch.importing?.value === true,
    (isImporting) => {
        if (isImporting) {
            fetchScrapJobs();
            startScrapJobsPolling();
        } else {
            fetchScrapJobs();
        }
    }
);

const currentRunId = computed(() => jobManager.state.value.runId || batch.lastRunId?.value || preview.lastRunId?.value || lastRunId.value || null);
const currentUnknownCharacteristics = computed(() => jobManager.state.value.unknownCharacteristics || batch.lastUnknownCharacteristics?.value || preview.lastUnknownCharacteristics?.value || null);

const selectedCount = computed(() => selectedIds.value?.size ?? 0);
const allSelected = computed(() => {
    const ids = visibleItems.value.map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));
    return ids.length > 0 && ids.every((id) => selectedIds.value.has(id));
});

function toggleSelectAll() {
    const ids = visibleItems.value.map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));
    if (allSelected.value) {
        const next = new Set(selectedIds.value);
        ids.forEach((id) => next.delete(id));
        selectedIds.value = next;
    } else {
        selectedIds.value = new Set(ids);
    }
}

function toggleSelectOne(id) {
    const n = Number(id);
    if (!Number.isFinite(n)) return;
    const next = new Set(selectedIds.value);
    if (next.has(n)) next.delete(n);
    else next.add(n);
    selectedIds.value = next;
}

function toggleExpandedRow(key) {
    const current = expandedRowKey.value;
    expandedRowKey.value = current === key ? null : key;
}

function resetTable() {
    search.rawItems.value = [];
    search.lastMeta.value = null;
    preview.convertedByItemId.value = {};
    preview.convertedByRelationKey.value = {};
    preview.lastBatchRelationsByKey.value = {};
    selectedIds.value = new Set();
    expandedRowKey.value = null;
    status.clearStatusForEntityType(selectedEntityTypeStr.value);
}

/** Pour le template : refs du composable search ne sont pas auto-unwrapped (objet non réactif). */
const rawItemsLength = computed(() => (search.rawItems?.value ?? []).length);
const hasRawItems = computed(() => rawItemsLength.value > 0);
const lastMetaUnwrapped = computed(() => search.lastMeta?.value ?? null);
const searchingUnwrapped = computed(() => search.searching?.value === true);

/** Booléens explicites pour les enfants (évite ref non déballée dans les props). */
const loadingMetaUnwrapped = computed(() => loadingMeta.value === true);
const loadingConfigUnwrapped = computed(() => loadingConfig.value === true);
const canPrevUnwrapped = computed(() => search.canPrev?.value === true);
const canNextUnwrapped = computed(() => search.canNext?.value === true);
const batchImportingUnwrapped = computed(() => batch.importing?.value === true);
const batchImportByPagesProgressUnwrapped = computed(() => batch.importByPagesProgress?.value ?? null);
const loadingConvertedUnwrapped = computed(() => preview.loadingConverted?.value === true);
const conversionProgressUnwrapped = computed(() => preview.conversionProgress?.value ?? null);

const pageIndex = computed(() => Math.max(0, Math.floor(Number(pageNumber.value) || 1) - 1));
const pageCount = computed(() => {
    const tp = search.totalPages?.value ?? null;
    if (tp == null || !Number.isFinite(tp)) return Math.max(1, Math.floor(Number(pageNumber.value) || 1));
    return Math.max(1, Math.floor(tp));
});
const totalRows = computed(() => search.totalRows?.value ?? 0);

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

/** Contrôleur d'annulation pour la recherche + conversion en cours. */
const searchAbortControllerRef = ref(/** @type {AbortController | null} */ (null));
const batchAbortControllerRef = ref(/** @type {AbortController | null} */ (null));

async function applyStatusAndPreview(/** @type {AbortSignal | null | undefined} */ signal) {
    if (!search.rawItems.value?.length) return;
    const entityType = selectedEntityTypeStr.value;
    const entities = search.rawItems.value.map((it) => ({ type: entityType, id: Number(it?.id) })).filter((e) => Number.isFinite(e.id) && e.id > 0);
    const toUpdate = entities.filter((e) => !status.TERMINAL_STATUSES.has(status.itemStatusByKey.value[status.statusKey({ id: e.id })]?.status));
    status.setStatusForEntities(toUpdate, "recherché");
    await preview.fetchConvertedBatch({ signal: signal ?? undefined });
}

function cancelSearchAndConversion() {
    searchAbortControllerRef.value?.abort();
    searchAbortControllerRef.value = null;
    batchAbortControllerRef.value?.abort();
    batchAbortControllerRef.value = null;
    jobManager.requestCancel();
}

async function runSearchAndPreview(options = {}) {
    const useExistingSignal = options.signal instanceof AbortSignal;
    const silentJob = options.silentJob === true;
    searchAbortControllerRef.value = useExistingSignal ? searchAbortControllerRef.value : new AbortController();
    const sig = useExistingSignal ? options.signal : searchAbortControllerRef.value.signal;
    if (!silentJob) {
        jobManager.startJob({
            kind: "search-preview",
            label: "Recherche et previsualisation",
            canCancel: true,
            cancelHandler: () => searchAbortControllerRef.value?.abort(),
        });
    }
    try {
        if (!silentJob) {
            jobManager.updateProgress({ phase: "search", done: 0, total: 1, label: "Recherche" });
        }
        await search.runSearch({ signal: sig });
        if (sig.aborted) {
            if (!silentJob) jobManager.finishCancelled();
            return;
        }
        if (!silentJob) {
            jobManager.updateProgress({ phase: "search", done: 1, total: 1, label: "Recherche terminee" });
        }
        await nextTick();
        await applyStatusAndPreview(sig);
        if (sig.aborted) {
            if (!silentJob) jobManager.finishCancelled();
            return;
        }
        if (!silentJob) {
            jobManager.finishSuccess("Recherche/preview terminee");
        }
    } catch (e) {
        if (e?.name === "AbortError" || sig.aborted) {
            if (!silentJob) jobManager.finishCancelled();
            return;
        }
        if (!silentJob) {
            jobManager.finishError(e?.message ?? "Erreur recherche/preview");
        }
        throw e;
    } finally {
        if (!useExistingSignal) {
            searchAbortControllerRef.value = null;
        }
    }
    typeManagerRefreshTrigger.value += 1;
}

const goPrev = async () => {
    searchAbortControllerRef.value = new AbortController();
    const sig = searchAbortControllerRef.value.signal;
    try {
        await search.goPrev({ signal: sig });
        if (sig.aborted) return;
        await nextTick();
        await applyStatusAndPreview(sig);
    } finally {
        searchAbortControllerRef.value = null;
    }
};
const goNext = async () => {
    searchAbortControllerRef.value = new AbortController();
    const sig = searchAbortControllerRef.value.signal;
    try {
        await search.goNext({ signal: sig });
        if (sig.aborted) return;
        await nextTick();
        await applyStatusAndPreview(sig);
    } finally {
        searchAbortControllerRef.value = null;
    }
};
const handlePaginationGo = async (pIdx) => {
    searchAbortControllerRef.value = new AbortController();
    const sig = searchAbortControllerRef.value.signal;
    try {
        await search.goToPage(pIdx, { signal: sig });
        if (sig.aborted) return;
        await nextTick();
        await applyStatusAndPreview(sig);
    } finally {
        searchAbortControllerRef.value = null;
    }
};
const handleSetPageSize = async (v) => {
    searchAbortControllerRef.value = new AbortController();
    const sig = searchAbortControllerRef.value.signal;
    try {
        await search.setPageSize(v, { signal: sig });
        if (sig.aborted) return;
        await nextTick();
        await applyStatusAndPreview(sig);
    } finally {
        searchAbortControllerRef.value = null;
    }
};
const handleFirst = async () => {
    pageNumber.value = 1;
    await runSearchAndPreview();
};
const handleLast = async () => {
    const tp = search.totalPages?.value ?? null;
    if (tp != null && Number.isFinite(tp)) await handlePaginationGo(tp - 1);
};

async function runBatchAction(mode) {
    if (batch.importing?.value === true) return;
    batchAbortControllerRef.value?.abort();
    batchAbortControllerRef.value = new AbortController();
    const sig = batchAbortControllerRef.value.signal;
    const label = mode === "simulate" ? "Simulation" : "Import";
    jobManager.startJob({
        kind: mode === "simulate" ? "batch-simulate" : "batch-import",
        label,
        canCancel: true,
        cancelHandler: () => batchAbortControllerRef.value?.abort(),
    });
    try {
        await batch.runBatchOrByPages(mode, { signal: sig });
        if (sig.aborted) {
            jobManager.finishCancelled();
            return;
        }
        if (batch.lastBatchErrorResults.value.length > 0) {
            jobManager.finishError(`${batch.lastBatchErrorResults.value.length} erreur(s)`);
            return;
        }
        jobManager.finishSuccess(`${label} termine`);
    } catch (e) {
        if (e?.name === "AbortError" || sig.aborted) {
            jobManager.finishCancelled();
            return;
        }
        jobManager.finishError(e?.message ?? `Erreur ${label.toLowerCase()}`);
    } finally {
        batchAbortControllerRef.value = null;
    }
}

onBeforeUnmount(() => {
    searchAbortControllerRef.value?.abort();
    batchAbortControllerRef.value?.abort();
    stopScrapJobsPolling();
});

const exportBatchErrorsCsv = () => {
    const { headers, rows } = buildCsvFromErrorResults(batch.lastBatchErrorResults.value);
    downloadCsvFromRows(headers, rows, filenameForBatchErrors());
    success("Export CSV des erreurs téléchargé.");
};

/** Libellé pour un id de caractéristique DofusDB (effets items). */
const getCharacteristicLabel = (id) => DEFAULT_CHARACTERISTIC_LABELS[String(id)] ?? `#${id}`;

/** Type d'entité scrapping → segment de route entité (entities.XXX.show). */
const scrappingTypeToRouteEntityType = {
    monster: "monsters",
    resource: "resources",
    consumable: "consumables",
    equipment: "items",
    spell: "spells",
};

/** Segment table API → modèle (Monster, Item, Spell, etc. pour fromArray et toCell). */
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
    const segment = scrappingTypeToRouteEntityType[selectedEntityTypeStr.value];
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
    const segment = scrappingTypeToRouteEntityType[selectedEntityTypeStr.value];
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
    const t = selectedEntityTypeStr.value;
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
    effectsAnalysisType.value = selectedEntityTypeStr.value;
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
        lastRunId.value = json?.run_id ?? lastRunId.value;
        const unknownSummary = json?.debug?.unknown_characteristics ?? null;
        const converted = preview?.converted || {};
        const { unmapped, summary } = extractUnmappedFromConverted(converted);

        effectsAnalysisUnmapped.value = Array.isArray(unmapped) ? unmapped : [];
        effectsAnalysisSummary.value = summary || null;

        success(`Analyse effets OK (${effectsAnalysisUnmapped.value.length} non mappé(s))`);
        pushHistory(`→ Analyse OK: ${effectsAnalysisUnmapped.value.length} effet(s) non mappé(s). run_id=${lastRunId.value || "n/a"}`);
        if (unknownSummary?.total_occurrences > 0) {
            const ids = Object.entries(unknownSummary.ids || {})
                .map(([id, c]) => `${id}(${c})`)
                .join(", ");
            pushHistory(`→ DEBUG unknown characteristic IDs: ${ids}. run_id=${lastRunId.value || "n/a"}`);
        }
    } catch (e) {
        showError("Analyse effets : " + e.message);
        pushHistory(`→ ERREUR analyse effets: ${e.message}. run_id=${lastRunId.value || "n/a"}`);
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

/** Lien vers l'admin mapping des effects préfiltré par effectId DofusDB. */
const effectMappingHref = (effectId) => {
    const id = Number(effectId);
    if (!Number.isFinite(id) || id <= 0) {
        return "/admin/dofusdb-effect-mappings";
    }
    return `/admin/dofusdb-effect-mappings?effect_id=${encodeURIComponent(String(id))}`;
};

// Modal Comparer Krosmoz / DofusDB
const compareModalOpen = ref(false);
const compareEntityType = ref("");
const compareDofusdbId = ref(null);
/** Ouvre le modal Comparer pour la ligne donnée (double-clic ; row = ligne entité ou relation). */
const openCompareModalForRow = (row) => {
    const entityType = row?.isRelation ? row?.relation?.type : selectedEntityTypeStr.value;
    const id = row?.isRelation ? row?.relation?.id : row?.item?.id;
    const n = id != null ? Number(id) : null;
    if (!Number.isFinite(n)) return;
    compareEntityType.value = entityType;
    compareDofusdbId.value = n;
    compareModalOpen.value = true;
};
const onCompareImported = () => {
    const type = compareEntityType.value;
    const id = compareDofusdbId.value;
    if (type && Number.isFinite(id) && id > 0) {
        status.setStatusForEntities([{ type, id }], "importé");
    }
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
            @close="handleTypeManagerClose"
        >
            <template #header>
                <div class="flex items-center justify-between gap-3 w-full">
                    <div class="font-semibold text-primary-100">
                        {{ typeManagerConfig?.title || 'Gestion des types' }}
                    </div>
                    <Btn size="sm" variant="ghost" @click="handleTypeManagerClose">
                        <Icon source="fa-solid fa-xmark" alt="Fermer" size="sm" /> <!-- pas besoin du texte car icone gérérer automatiquementfermer -->
                    </Btn>
                </div>
            </template>

            <div v-if="typeManagerConfig" class="max-h-[75vh] overflow-y-auto pr-2">
                <TypeManagerTable
                    :title="typeManagerConfig.title"
                    :description="typeManagerConfig.description"
                    :mode="typeManagerConfig.mode"
                    :list-url="typeManagerConfig.listUrl"
                    :refresh-trigger="typeManagerRefreshTrigger"
                    :bulk-url="typeManagerConfig.bulkUrl"
                    :move-category-url-base="typeManagerConfig.moveCategoryUrlBase"
                    :current-category="typeManagerConfig.currentCategory"
                    :delete-url-base="typeManagerConfig.mode === 'decision'
                        ? (selectedEntityTypeStr === 'resource'
                            ? '/api/scrapping/resource-types'
                            : selectedEntityTypeStr === 'consumable'
                                ? '/api/scrapping/consumable-types'
                                : selectedEntityTypeStr === 'equipment'
                                    ? '/api/scrapping/item-types'
                                    : '')
                        : (selectedEntityTypeStr === 'monster'
                            ? '/api/types/monster-races'
                            : selectedEntityTypeStr === 'spell'
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
        <ScrappingFilters
            :config="configEntitiesByKey"
            :loading-meta="loadingMetaUnwrapped"
            :loading-config="loadingConfigUnwrapped"
            :entity-options="entityOptions"
            v-model:selected-entity-type="selectedEntityType"
            v-model:show-only-error-entities="showOnlyErrorEntities"
            :error-entity-count="entitiesWithConfigErrorCount"
            :label-for-type-id="labelForTypeId"
            v-model:filter-ids="filterIds"
            v-model:filter-name="filterName"
            v-model:type-mode="typeMode"
            :type-mode-options="typeModeOptions"
            v-model:filter-type-ids="filterTypeIds"
            v-model:filter-type-ids-not="filterTypeIdsNot"
            v-model:selected-known-type-include="selectedKnownTypeInclude"
            v-model:selected-known-type-exclude="selectedKnownTypeExclude"
            :known-type-options="knownTypeOptions"
            :known-types-loading="knownTypesLoading"
            v-model:race-mode="raceMode"
            :race-mode-options="raceModeOptions"
            v-model:filter-race-ids="filterRaceIds"
            v-model:selected-known-race="selectedKnownRace"
            :known-race-options="knownRaceOptions"
            :known-races-loading="knownRacesLoading"
            v-model:filter-race-id="filterRaceId"
            v-model:filter-breed-id="filterBreedId"
            v-model:filter-level-min="filterLevelMin"
            v-model:filter-level-max="filterLevelMax"
            :type-manager-config="typeManagerConfig"
            :page-index="pageIndex"
            :page-count="pageCount"
            :per-page="perPage"
            :total-rows="totalRows"
            :can-prev="canPrevUnwrapped"
            :can-next="canNextUnwrapped"
            :searching="searchingUnwrapped"
            :last-meta="lastMetaUnwrapped"
            :raw-items-length="rawItemsLength"
            :cancel-visible="searchingUnwrapped || loadingConvertedUnwrapped || batchImportingUnwrapped"
            @search="runSearchAndPreview"
            @cancel="cancelSearchAndConversion"
            @open-type-manager="handleOpenTypeManager"
            @add-known-type="(target) => addKnownTypeTo(target)"
            @remove-known-type="(list, id) => removeKnownTypeFrom(list, id)"
            @add-known-race="addKnownRace"
            @remove-known-race="removeKnownRace"
            @prev="goPrev"
            @next="goNext"
            @first="handleFirst"
            @last="handleLast"
            @go="handlePaginationGo"
            @set-page-size="handleSetPageSize"
        />

        <Card
            v-if="hasAnyScrapJobs"
            class="border border-base-300 bg-base-200/80"
        >
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <Icon source="fa-gears" pack="solid" size="sm" class="text-primary" />
                    <span class="font-semibold">Jobs scrapping</span>
                    <Badge v-if="hasActiveScrapJobs" :content="String(scrapJobsList?.active?.length ?? 0)" color="info" size="xs" />
                </div>
                <Btn
                    v-if="hasActiveScrapJobs"
                    variant="ghost"
                    size="xs"
                    :disabled="scrapJobsLoading"
                    @click="fetchScrapJobs"
                >
                    <Loading v-if="scrapJobsLoading" class="mr-1" />
                    Actualiser
                </Btn>
            </div>
            <div class="space-y-2 text-sm">
                <div
                    v-for="job in scrapJobsList?.active ?? []"
                    :key="job.job_id"
                    class="flex flex-wrap items-center justify-between gap-2 p-3 rounded-lg bg-base-300/50"
                >
                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                        <Badge :content="scrapJobStatusLabel(job.status)" :color="scrapJobStatusColor(job.status)" size="xs" />
                        <span v-if="job.progress?.total > 0" class="text-base-content/70 tabular-nums">
                            {{ job.progress?.done ?? 0 }}/{{ job.progress?.total }}
                        </span>
                        <span v-if="job.run_id" class="text-xs text-base-content/50 truncate">run_id={{ job.run_id }}</span>
                    </div>
                    <Btn
                        v-if="job.status === 'queued' || job.status === 'running'"
                        color="error"
                        variant="outline"
                        size="xs"
                        @click="cancelScrapJob(job.job_id)"
                    >
                        Annuler
                    </Btn>
                </div>
                <details v-if="(scrapJobsList?.recent_finished?.length ?? 0) > 0" class="mt-2">
                    <summary class="cursor-pointer text-base-content/70 hover:text-base-content">
                        Derniers terminés ({{ scrapJobsList?.recent_finished?.length ?? 0 }})
                    </summary>
                    <div class="mt-2 space-y-1 pl-2 border-l-2 border-base-300">
                        <div
                            v-for="job in scrapJobsList?.recent_finished ?? []"
                            :key="job.job_id"
                            class="flex items-center gap-2 py-1 text-xs"
                        >
                            <Badge :content="scrapJobStatusLabel(job.status)" :color="scrapJobStatusColor(job.status)" size="xs" />
                            <span v-if="job.summary" class="text-base-content/60">
                                {{ job.summary?.success ?? 0 }}/{{ job.summary?.total ?? 0 }}
                                <template v-if="(job.summary?.errors ?? 0) > 0">({{ job.summary.errors }} erreur(s))</template>
                            </span>
                            <span v-if="job.run_id" class="text-base-content/40 truncate">run_id={{ job.run_id }}</span>
                        </div>
                    </div>
                </details>
            </div>
        </Card>

        <ScrappingOptionsPanel
            v-model:open="showOptionsAndHistory"
            v-model:opt-include-relations="optIncludeRelations"
            v-model:opt-update-mode="optUpdateMode"
            v-model:opt-property-whitelist="optPropertyWhitelist"
            v-model:opt-property-blacklist="optPropertyBlacklist"
            :history-lines="historyLines"
            :run-id="currentRunId"
            :unknown-characteristics="currentUnknownCharacteristics"
            :batch-error-results="batch.lastBatchErrorResults"
            @clear-history="historyLines = []"
            @clear-errors="batch.clearBatchErrors()"
            @export-errors-csv="exportBatchErrorsCsv"
            @copy-run-id="copyLastRunId"
        />

        <Card
            v-if="configLoadError || entitiesWithConfigErrorCount > 0"
            class="border border-warning/40 bg-warning/10 p-3 text-xs text-warning-content space-y-2"
        >
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold">Sante de configuration</span>
                    <Badge
                        v-if="entitiesWithConfigErrorCount > 0"
                        :content="`${entitiesWithConfigErrorCount} entite(s) en erreur`"
                        color="warning"
                        size="xs"
                    />
                    <Badge
                        v-if="configHealth?.status"
                        :content="`etat: ${configHealth.status}`"
                        :color="configHealth.status === 'ok' ? 'success' : (configHealth.status === 'partial' ? 'warning' : 'error')"
                        size="xs"
                    />
                    <Badge
                        v-if="configLoadError"
                        content="Mode degrade"
                        color="warning"
                        size="xs"
                    />
                </div>
                <Btn
                    v-if="configLoadError"
                    size="sm"
                    variant="outline"
                    :disabled="loadingConfigUnwrapped"
                    @click="loadConfig"
                >
                    <Loading v-if="loadingConfigUnwrapped" class="mr-2" />
                    Reessayer
                </Btn>
            </div>

            <p v-if="configLoadError" class="opacity-90">
                Config avancee indisponible temporairement, mais la recherche/import de base reste active.
            </p>
            <p v-else-if="configHealth" class="opacity-90">
                Entites saines: {{ configHealth.healthyCount }}/{{ configHealth.entityCount }}
            </p>

            <div v-if="entitiesWithConfigErrorCount > 0" class="flex flex-wrap gap-1.5">
                <a
                    v-for="row in entitiesWithConfigError"
                    :key="`cfg-err-${row.entity}`"
                    :href="row.actionUrl || '/admin/scrapping-mappings'"
                    class="btn btn-ghost btn-xs"
                    :title="row.message"
                >
                    {{ row.label }}
                </a>
            </div>

            <p v-if="configLoadError" class="opacity-70">
                {{ configLoadError }}
            </p>
        </Card>

        <!-- Corps: tableau -->
        <Card class="p-6 space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-primary-100">Résultats</h3>
                    <Badge :content="String(visibleItems.length)" color="neutral" />
                    <span v-if="lastMetaUnwrapped && typeof lastMetaUnwrapped.total === 'number'" class="text-sm text-primary-300">
                        · total filtré: {{ lastMetaUnwrapped.total }}
                    </span>
                    <span v-if="selectedCount" class="text-sm text-primary-300">· sélection: {{ selectedCount }}</span>
                    <span v-if="loadingConvertedUnwrapped" class="text-xs text-primary-300 flex flex-col gap-1">
                        <span class="flex items-center gap-2">
                            <Loading />
                            <span>{{ conversionProgressUnwrapped?.phase === 'relations' ? 'Relations…' : 'Valeurs converties…' }}</span>
                            <template v-if="conversionProgressUnwrapped && conversionProgressUnwrapped.total > 0">
                                <div class="w-24 h-1.5 rounded-full bg-base-300 overflow-hidden" role="progressbar" :aria-valuenow="conversionProgressUnwrapped.done" :aria-valuemin="0" :aria-valuemax="conversionProgressUnwrapped.total">
                                    <div
                                        class="h-full bg-primary transition-all duration-200"
                                        :style="{ width: Math.min(100, (conversionProgressUnwrapped.done / conversionProgressUnwrapped.total) * 100) + '%' }"
                                    />
                                </div>
                                <span class="text-primary-400 tabular-nums">{{ conversionProgressUnwrapped.total ? Math.round((conversionProgressUnwrapped.done / conversionProgressUnwrapped.total) * 100) : 0 }}%</span>
                            </template>
                        </span>
                        <span v-if="conversionProgressUnwrapped && conversionProgressUnwrapped.total > 0" class="text-[10px] text-primary-400/80">
                            {{ conversionProgressUnwrapped.done }} traités / {{ conversionProgressUnwrapped.total - conversionProgressUnwrapped.done }} restant
                        </span>
                    </span>
                    <Btn
                        v-if="searchingUnwrapped || loadingConvertedUnwrapped || batchImportingUnwrapped"
                        color="error"
                        variant="outline"
                        size="sm"
                        class="ml-2"
                        title="Arrêter la recherche et la conversion en cours"
                        @click="cancelSearchAndConversion"
                    >
                        <Icon source="fa-solid fa-stop" alt="" pack="solid" class="mr-1" />
                        Annuler
                    </Btn>
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <InputField v-model="tableSearch" label="Recherche dans le tableau" placeholder="id ou nom…" />
                </div>
            </div>

            <div class="flex flex-wrap gap-2 items-center justify-between">
                <div class="flex flex-wrap gap-2 items-center">
                    <Btn variant="ghost" :disabled="!hasRawItems" @click="resetTable">
                        Réinitialiser
                    </Btn>
                    <span class="text-sm text-primary-300 mr-1">Périmètre :</span>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input v-model="batchScope" type="radio" value="selection" class="radio radio-sm radio-primary" />
                        <span class="text-sm">Sélection</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input v-model="batchScope" type="radio" value="all" class="radio radio-sm radio-primary" />
                        <span class="text-sm">Tous</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input v-model="batchScope" type="radio" value="pages" class="radio radio-sm radio-primary" />
                        <span class="text-sm">Par pages</span>
                    </label>
                    <template v-if="batchScope === 'pages'">
                        <InputField
                            v-model="pageRangeInput"
                            label="Pages"
                            :disabled="batchImportingUnwrapped"
                            placeholder="ex: 1-6 ou 4,5"
                            class="w-36"
                        />
                    </template>
                    <Btn
                        color="secondary"
                        :disabled="batchImportingUnwrapped || (batchScope !== 'pages' && !hasRawItems)"
                        title="Simule l'import sans écrire en base"
                        @click="runBatchAction('simulate')"
                    >
                        <Loading v-if="batchImportingUnwrapped" class="mr-2" />
                        {{ batchScope === 'pages' && batchImportByPagesProgressUnwrapped ? `Page ${batchImportByPagesProgressUnwrapped}` : 'Simuler' }}
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="batchImportingUnwrapped || (batchScope !== 'pages' && !hasRawItems)"
                        title="Importe en base (convert + validate + integrate)"
                        @click="runBatchAction('import')"
                    >
                        <Loading v-if="batchImportingUnwrapped" class="mr-2" />
                        {{ batchScope === 'pages' && batchImportByPagesProgressUnwrapped ? `Page ${batchImportByPagesProgressUnwrapped}` : 'Importer' }}
                    </Btn>
                    <Btn
                        variant="ghost"
                        :disabled="effectsAnalysisLoading || !canAnalyzeEffects"
                        @click="analyzeEffects"
                        title="Disponible pour équipement/consommable/ressource/sort (sur l’ID sélectionné)"
                    >
                        <Loading v-if="effectsAnalysisLoading" class="mr-2" />
                        Analyser effets (non mappés)
                    </Btn>
                </div>

                <div class="flex items-center gap-2">
                    <Btn size="sm" variant="ghost" :disabled="!hasRawItems" @click="toggleSelectAll">
                        {{ allSelected ? "Tout décocher" : "Tout cocher" }}
                    </Btn>
                </div>
            </div>

            <div v-if="!hasRawItems" class="text-sm text-primary-300 italic">
                Aucun résultat. Lance une recherche.
            </div>

            <ScrappingResultsTable
                :rows="visibleRowsWithRelations"
                :selected-ids="selectedIds"
                :expanded-row-key="expandedRowKey"
                :get-expand-key="getExpandKey"
                :all-selected="allSelected"
                :get-status-entry="(item) => status.getStatusEntry(item)"
                :get-status-label="(item) => status.getStatusLabel(item)"
                :get-status-color="(item) => status.getStatusColor(item)"
                :triple-name="(row) => compare.tripleName(row.item, getConvertedForRow(row))"
                :triple-level="(row) => compare.tripleLevel(row.item, getConvertedForRow(row))"
                :triple-type="(row) => compare.tripleType(row.item, getConvertedForRow(row))"
                :comparison-rows="(row) => compare.comparisonRows(row.item, getConvertedForRow(row), row.isRelation ? (getConvertedForRow(row)?.resolvedEntityType ?? row.relation?.type) : undefined)"
                :format-compare-val="compare.formatCompareVal"
                :relation-type-label="(row) => relationTypeLabel(row?.relation, getConvertedForRow(row)?.resolvedEntityType)"
                :supports="supports"
                :format-name="(n) => (n?.fr ?? n?.en ?? (typeof n === 'string' ? n : '—'))"
                :exists-label="existsLabel"
                :exists-tooltip="existsTooltip"
                :exists-entity-href="existsEntityHref"
                :row-has-diff="(row) => compare.comparisonRows(row.item, getConvertedForRow(row), row.isRelation ? (getConvertedForRow(row)?.resolvedEntityType ?? row.relation?.type) : undefined).some(r => r.differs)"
                :has-item-effects="hasItemEffects"
                :item-effects-for-row="itemEffectsForRow"
                :get-characteristic-label="getCharacteristicLabel"
                :entity-type-str="selectedEntityTypeStr"
                :get-relation-entity-type="(row) => getConvertedForRow(row)?.resolvedEntityType"
                :entity-modal-loading="entityModalLoading"
                :entity-modal-loading-id="entityModalLoadingId"
                @update:selected-ids="(p) => p === 'toggle-all' ? toggleSelectAll() : (p?.type === 'toggle-one' && toggleSelectOne(p.id))"
                @toggle-expand="toggleExpandedRow"
                @open-compare="openCompareModalForRow"
                @open-entity="openEntityModal"
            />

            <!-- Pagination sous les résultats (visible après recherche) -->
            <div v-if="hasRawItems" class="mt-4 pt-4 border-t border-base-300">
                <TanStackTablePagination
                    :page-index="pageIndex"
                    :page-count="pageCount"
                    :page-size="Math.max(1, Math.min(200, perPage))"
                    :total-rows="totalRows"
                    :per-page-options="[50, 100, 200]"
                    :can-prev="canPrevUnwrapped"
                    :can-next="canNextUnwrapped"
                    ui-size="sm"
                    ui-color="primary"
                    @prev="goPrev"
                    @next="goNext"
                    @first="handleFirst"
                    @last="handleLast"
                    @go="handlePaginationGo"
                    @set-page-size="handleSetPageSize"
                />
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
                            <th class="w-28">Action</th>
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
                            <td>
                                <a
                                    :href="effectMappingHref(u?.effectId)"
                                    class="btn btn-ghost btn-xs"
                                >
                                    Corriger
                                </a>
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

