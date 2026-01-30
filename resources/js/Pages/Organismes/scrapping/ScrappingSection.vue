<script setup>
/**
 * ScrappingSection (Organism)
 *
 * @description
 * Composant réutilisable pour piloter le scrapping depuis l'UI.
 * Utilisable en page ou en modal :
 * - mode page : sélection du type + options + preview/import + historique
 * - mode modal : type verrouillé + ID prérempli + preview auto (optionnel)
 */
import { computed, onMounted, ref, watch } from "vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import EntityTypeSelector from "@/Pages/Pages/scrapping/components/EntityTypeSelector.vue";
import ImportOptionsSection from "@/Pages/Pages/scrapping/components/ImportOptionsSection.vue";
import SearchPreviewSection from "@/Pages/Pages/scrapping/components/SearchPreviewSection.vue";
import HistorySection from "@/Pages/Pages/scrapping/components/HistorySection.vue";
import ScrappingSearchTableSection from "@/Pages/Organismes/scrapping/ScrappingSearchTableSection.vue";

const props = defineProps({
    /**
     * 'page' | 'modal' (impact UX uniquement)
     */
    variant: { type: String, default: "page" },

    /**
     * Si fourni, force le type d'entité (ex: 'monster') et masque le sélecteur.
     */
    lockedEntityType: { type: String, default: "" },

    /**
     * Si fourni, pré-remplit l'ID (mode single) et peut verrouiller l'input.
     */
    initialId: { type: [String, Number], default: "" },

    /**
     * Options d'import initiales
     */
    initialImportOptions: {
        type: Object,
        default: () => ({
            skipCache: false,
            forceUpdate: false,
            dryRun: false,
            validateOnly: false,
            includeRelations: true,
        }),
    },

    /**
     * Afficher l'historique (souvent false en modal)
     */
    showHistory: { type: Boolean, default: true },

    /**
     * Lancer automatiquement une prévisualisation au montage (utile en modal)
     */
    autoPreview: { type: Boolean, default: false },

    /**
     * Limiter les modes (ex: ['single'] en modal)
     */
    availableModes: { type: Array, default: () => ["single", "range", "all"] },

    /**
     * Verrouille les champs de recherche (ID/range)
     */
    lockSearchInputs: { type: Boolean, default: false },
});

const emit = defineEmits([
    "import-success",
    "import-error",
    "preview-loaded",
]);

const notificationStore = useNotificationStore();
const { success, error: showError } = notificationStore;

const metaLoading = ref(true);
const configLoading = ref(true);
const entityTypes = ref([]);
const selectedEntityType = ref("class");
const loading = ref(false);
const configEntitiesByKey = ref({});

const importOptions = ref({ ...props.initialImportOptions });
const results = ref([]);

const previewOverrideSingleId = ref("");
const previewNonce = ref(0);

const selectedEntity = computed(() => {
    return entityTypes.value.find((e) => e.value === selectedEntityType.value);
});

const maxId = computed(() => selectedEntity.value?.maxId || 0);

const effectiveEntityType = computed(() => {
    const locked = String(props.lockedEntityType || "").trim();
    return locked ? locked : selectedEntityType.value;
});

const isLockedEntityType = computed(() => Boolean(String(props.lockedEntityType || "").trim()));

const getIconForType = (type) => {
    const icons = {
        class: "fa-user",
        monster: "fa-dragon",
        item: "fa-box",
        spell: "fa-wand-magic-sparkles",
        panoply: "fa-layer-group",
        resource: "fa-gem",
        consumable: "fa-utensils",
        effect: "fa-bolt",
    };
    return icons[type] || "fa-question";
};

const loadEntityMeta = async () => {
    metaLoading.value = true;
    try {
        const response = await fetch("/api/scrapping/meta", {
            headers: { Accept: "application/json" },
        });
        const data = await response.json();

        if (response.ok && data.success) {
            entityTypes.value = (data.data || []).map((item) => ({
                value: item.type,
                label: item.label,
                icon: getIconForType(item.type),
                maxId: item.maxId,
            }));
        } else {
            showError("Impossible de charger les métadonnées des entités");
        }
    } catch (err) {
        showError("Erreur lors du chargement des métadonnées : " + err.message);
    } finally {
        metaLoading.value = false;
    }
};

const loadScrappingConfig = async () => {
    configLoading.value = true;
    try {
        const response = await fetch("/api/scrapping/config", {
            headers: { Accept: "application/json" },
        });
        const data = await response.json();
        if (response.ok && data.success) {
            const map = {};
            const entities = data.data?.entities || [];
            for (const e of entities) {
                if (e?.entity) map[String(e.entity)] = e;
            }
            configEntitiesByKey.value = map;
        } else {
            showError("Impossible de charger la config de scrapping");
        }
    } catch (err) {
        showError("Erreur lors du chargement de la config : " + err.message);
    } finally {
        configLoading.value = false;
    }
};

const searchFiltersForType = computed(() => {
    const t = String(effectiveEntityType.value || "");
    const cfg = configEntitiesByKey.value?.[t] || null;
    const supported = cfg?.filters?.supported;
    return Array.isArray(supported) ? supported : [];
});

const searchEnabled = computed(() => {
    // On n'active que pour les types importables via batch / endpoints existants
    const t = String(effectiveEntityType.value || "");
    if (!["class", "monster", "item", "spell", "panoply"].includes(t)) return false;
    // Doit aussi exister côté /api/scrapping/config (sinon /search renverra 404)
    return Boolean(configEntitiesByKey.value?.[t]);
});

watch(
    () => props.lockedEntityType,
    (v) => {
        const locked = String(v || "").trim();
        if (locked) selectedEntityType.value = locked;
    },
    { immediate: true }
);

onMounted(async () => {
    await loadEntityMeta();
    await loadScrappingConfig();
    if (isLockedEntityType.value) {
        selectedEntityType.value = effectiveEntityType.value;
    }
});

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

const buildPayload = (overrides = {}) => ({
    skip_cache: importOptions.value.skipCache,
    force_update: importOptions.value.forceUpdate,
    dry_run: importOptions.value.dryRun,
    validate_only: importOptions.value.validateOnly,
    include_relations: importOptions.value.includeRelations,
    ...overrides,
});

const pushHistory = (entry) => {
    results.value.unshift({
        ...entry,
        timestamp: new Date().toISOString(),
    });
};

const handleSimulate = async (params) => {
    loading.value = true;
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        loading.value = false;
        return;
    }

    try {
        let url = "";
        let payload = buildPayload({ dry_run: true });

        if (params.mode === "single") {
            url = `/api/scrapping/import/${params.entityType}/${params.singleId}`;
        } else if (params.mode === "range") {
            url = "/api/scrapping/import/range";
            payload = { ...payload, type: params.entityType, start_id: params.rangeStart, end_id: params.rangeEnd };
        } else if (params.mode === "all") {
            url = "/api/scrapping/import/all";
            payload = { ...payload, type: params.entityType };
        }

        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();
        if (response.ok) {
            const summary = data.summary || { total: 1, success: data.success ? 1 : 0, errors: data.success ? 0 : 1 };
            success(`Simulation terminée : ${summary.success}/${summary.total} entités réussies`);

            pushHistory({
                type: params.mode === "single" ? "individual" : params.mode,
                entityType: params.entityType,
                entityId: params.singleId,
                range: params.mode === "range" ? { start: params.rangeStart, end: params.rangeEnd } : null,
                result: data,
                error: !data.success,
                simulated: true,
            });
        } else {
            showError(data.message || "Erreur lors de la simulation");
        }
    } catch (err) {
        showError("Erreur lors de la simulation : " + err.message);
    } finally {
        loading.value = false;
    }
};

const handleImport = async (params) => {
    loading.value = true;
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        loading.value = false;
        return;
    }

    try {
        let url = "";
        let payload = buildPayload();

        if (params.mode === "single") {
            url = `/api/scrapping/import/${params.entityType}/${params.singleId}`;
        } else if (params.mode === "range") {
            url = "/api/scrapping/import/range";
            payload = { ...payload, type: params.entityType, start_id: params.rangeStart, end_id: params.rangeEnd };
        } else if (params.mode === "all") {
            url = "/api/scrapping/import/all";
            payload = { ...payload, type: params.entityType };
        }

        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();
        if (response.ok && data.success !== false) {
            success(data.message || "Import terminé");

            pushHistory({
                type: params.mode === "single" ? "individual" : params.mode,
                entityType: params.entityType,
                entityId: params.singleId,
                range: params.mode === "range" ? { start: params.rangeStart, end: params.rangeEnd } : null,
                result: data,
                error: false,
                simulated: false,
            });

            emit("import-success", { params, result: data });
        } else {
            showError(data.message || "Erreur lors de l'import");
            emit("import-error", { params, result: data });
        }
    } catch (err) {
        showError("Erreur lors de l'import : " + err.message);
        emit("import-error", { params, error: err });
    } finally {
        loading.value = false;
    }
};

const handlePreview = (data) => {
    emit("preview-loaded", data);
};

const handlePreviewSelected = (id) => {
    previewOverrideSingleId.value = String(id);
    previewNonce.value += 1;
};
</script>

<template>
    <div class="space-y-4">
        <div v-if="metaLoading || configLoading" class="py-6 flex items-center gap-3 text-primary-300">
            <Loading />
            <span>Chargement…</span>
        </div>

        <template v-else>
            <EntityTypeSelector
                v-if="!isLockedEntityType"
                v-model="selectedEntityType"
                :entity-types="entityTypes"
                :loading="false"
            />

            <ImportOptionsSection v-model="importOptions" />

            <ScrappingSearchTableSection
                v-if="variant === 'page'"
                :entity-type="effectiveEntityType"
                :supported-filters="searchFiltersForType"
                :import-options="importOptions"
                :enabled="searchEnabled"
                @preview-selected="handlePreviewSelected"
            />

            <SearchPreviewSection
                :entity-type="effectiveEntityType"
                :max-id="maxId"
                :loading="loading"
                :initial-mode="(variant === 'page' && previewOverrideSingleId !== '') ? 'single' : (availableModes.includes('single') && initialId !== '' ? 'single' : (availableModes[0] || 'single'))"
                :initial-single-id="(variant === 'page' && previewOverrideSingleId !== '') ? previewOverrideSingleId : initialId"
                :available-modes="availableModes"
                :lock-inputs="lockSearchInputs"
                :auto-preview="autoPreview"
                :preview-nonce="previewNonce"
                :hide-header="variant === 'modal'"
                @preview="handlePreview"
                @simulate="handleSimulate"
                @import="handleImport"
            />

            <HistorySection
                v-if="showHistory"
                :results="results"
                @clear="results = []"
            />
        </template>
    </div>
</template>

