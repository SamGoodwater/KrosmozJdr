<script setup>
/**
 * ResourceType Index Page
 *
 * @description
 * Table de gestion des types de ressources (incluant la registry DofusDB).
 */
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { useHybridEntityTable } from "@/Composables/entity/useHybridEntityTable";
import { applyPatchToDataset } from "@/Composables/entity/applyPatchToDataset";
import { usePermissions } from "@/Composables/permissions/usePermissions";

import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Alert from "@/Pages/Atoms/feedback/Alert.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import EntityTable from "@/Pages/Molecules/data-display/EntityTable.vue";
import CreateEntityModal from "@/Pages/Organismes/entity/CreateEntityModal.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import ResourceTypeBulkEditPanel from "./components/ResourceTypeBulkEditPanel.vue";

const props = defineProps({
    resourceTypes: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    can: { type: Object, default: () => ({}) },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Types de ressources");

const canModify = computed(() => Boolean(props.can?.updateAny));
const { canUpdateAny, canCreate, canManageAny } = usePermissions();
const canModifyResolved = computed(() => Boolean(props.can?.updateAny ?? canUpdateAny('resource-types')));
const canCreateResolved = computed(() => Boolean(props.can?.create ?? canCreate('resource-types')));
const canManage = computed(() => Boolean(props.can?.manageAny ?? canManageAny('resource-types')));

const notificationStore = useNotificationStore();
const { success: notifySuccess, error: notifyError } = notificationStore;

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

const selectedEntity = ref(null);
const editOpen = ref(false);
const createOpen = ref(false);
const selectedEntities = ref([]);

const search = ref(props.filters.search || "");
const filters = ref(props.filters || {});

const serverSort = ref("");
const serverOrder = ref("desc");
try {
    const qs = new URLSearchParams(window.location.search);
    serverSort.value = qs.get("sort") || "";
    serverOrder.value = qs.get("order") || "desc";
} catch (e) {
    // ignore
}

const { tableMode, allRows: allResourceTypes, loadingAll, baseServerQuery, loadClientMode, reloadClientDataset, switchToServerMode } =
    useHybridEntityTable({
        entityKey: "resource-types",
        search,
        filters,
        serverSort,
        serverOrder,
        notifySuccess: (msg) => notifySuccess(msg),
        notifyError: (msg) => notifyError(msg),
        limit: 5000,
    });

const decisionLabel = (decision) => {
    return decision === "allowed"
        ? "Utilisé"
        : decision === "blocked"
            ? "Non utilisé"
            : "En attente";
};

const columns = computed(() => [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Nom", sortable: true, isMain: true },
    { key: "dofusdb_type_id", label: "DofusDB typeId", sortable: true, format: (v) => v ?? "-" },
    {
        key: "decision",
        label: "Statut",
        sortable: true,
        type: canModifyResolved.value ? "inline-select" : "badge",
        badgeColor: "primary",
        format: decisionLabel,
        disabled: !canModifyResolved.value,
        options: [
            { value: "pending", label: "En attente" },
            { value: "allowed", label: "Utilisé" },
            { value: "blocked", label: "Non utilisé" },
        ],
    },
    { key: "seen_count", label: "Détections", sortable: true, format: (v) => v ?? 0 },
    { key: "last_seen_at", label: "Dernière détection", sortable: true, format: (v) => v ? new Date(v).toLocaleString("fr-FR") : "-" },
    { key: "resources_count", label: "Ressources", sortable: true, format: (v) => v ?? 0 },
    { key: "actions", label: "Actions", sortable: false },
]);

const filterableColumns = computed(() => [
    {
        key: "decision",
        label: "Statut",
        options: [
            { value: "", label: "Tous" },
            { value: "pending", label: "En attente" },
            { value: "allowed", label: "Utilisé" },
            { value: "blocked", label: "Non utilisé" },
        ],
    },
]);

let searchTimeout = null;
const handleSearchUpdate = (value) => {
    search.value = value;
    if (tableMode.value === "client") return;
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route("entities.resource-types.index"), { search: value, ...filters.value }, { preserveState: true, preserveScroll: true });
    }, 300);
};

onBeforeUnmount(() => {
    if (searchTimeout) clearTimeout(searchTimeout);
});

const handleFiltersUpdate = (newFilters) => {
    filters.value = newFilters;
    if (tableMode.value === "client") return;
    router.get(route("entities.resource-types.index"), { search: search.value, ...newFilters }, { preserveState: true, preserveScroll: true });
};

const handleSort = ({ column, order }) => {
    if (tableMode.value === "client") return;
    serverSort.value = column;
    serverOrder.value = order;
    router.get(route("entities.resource-types.index"), { sort: column, order, search: search.value, ...filters.value }, { preserveState: true, preserveScroll: true });
};

const handlePageChange = (url) => {
    if (!url) return;
    if (tableMode.value === "client") return;
    router.visit(url, { preserveState: true, preserveScroll: true });
};

const handleRefreshAll = () => {
    router.reload({ preserveState: true, preserveScroll: true });
};

const handleLoadAllForClientMode = async () => loadClientMode();
const handleReloadClientDataset = async () => reloadClientDataset();
const handleSwitchToServerMode = () => switchToServerMode();

const handleEdit = (entity) => {
    selectedEntity.value = entity;
    editOpen.value = true;
};

const handleView = (entity) => {
    router.visit(route("entities.resource-types.show", entity.id));
};

const handleDelete = (entity) => {
    if (confirm(`Supprimer le type "${entity.name}" ?`)) {
        router.delete(route("entities.resource-types.delete", { resourceType: entity.id }), { preserveScroll: true });
    }
};

const handleCellUpdate = async ({ entity, key, value }) => {
    // Inline edit: decision
    if (key !== "decision") return;
    if (!canModifyResolved.value) return;

    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        notifyError("Token CSRF introuvable. Recharge la page.");
        return;
    }

    const newDecision = String(value);
    if (!["pending", "allowed", "blocked"].includes(newDecision)) return;

    try {
        const response = await fetch(`/api/scrapping/resource-types/${entity.id}/decision`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json",
            },
            body: JSON.stringify({ decision: newDecision }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            notifyError(data.message || "Impossible de mettre à jour le statut.");
            return;
        }

        notifySuccess("Statut mis à jour");

        // Mise à jour optimiste côté client
        if (tableMode.value === "client") {
            allResourceTypes.value = (allResourceTypes.value || []).map((r) => {
                if (String(r.id) !== String(entity.id)) return r;
                return { ...r, decision: newDecision };
            });
        } else {
            // Mode serveur: recharger la page pour refléter le changement
            router.reload({ preserveState: true, preserveScroll: true });
        }
    } catch (e) {
        notifyError("Erreur lors de la mise à jour : " + (e?.message || "unknown"));
    }
};

const handleBulkApplied = async (payload) => {
    // payload: { ids, decision?, usable?, is_visible? }
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        notifyError("Token CSRF introuvable. Recharge la page.");
        return;
    }

    try {
        const response = await fetch("/api/scrapping/resource-types/bulk", {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json",
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            notifyError(data.message || `Bulk update: ${data?.summary?.errors ?? 1} erreur(s)`);
            return;
        }

        notifySuccess(`Mis à jour: ${data.summary.updated}/${data.summary.requested}`);

        // Mise à jour locale (mode client) ou reload (mode serveur)
        if (tableMode.value === "client") {
            allResourceTypes.value = applyPatchToDataset(allResourceTypes.value, payload, {
                normalize: {
                    usable: (v) => (v ? 1 : 0),
                },
            });
        } else {
            router.reload({ preserveState: true, preserveScroll: true });
        }

        // Clear selection
        selectedEntities.value = [];
    } catch (e) {
        notifyError("Erreur bulk: " + (e?.message || "unknown"));
    }
};

const clearSelection = () => {
    selectedEntities.value = [];
};

const closeEdit = () => {
    editOpen.value = false;
    selectedEntity.value = null;
};

const fieldsConfig = computed(() => ({
    name: { type: "text", label: "Nom", required: true, showInCompact: true },
    dofusdb_type_id: { type: "number", label: "DofusDB typeId", required: false, showInCompact: true },
    decision: {
        type: "select",
        label: "Statut",
        required: false,
        showInCompact: true,
        options: [
            { value: "pending", label: "En attente" },
            { value: "allowed", label: "Utilisé" },
            { value: "blocked", label: "Non utilisé" },
        ],
    },
    usable: { type: "checkbox", label: "Utilisable", required: false, showInCompact: true },
    is_visible: {
        type: "select",
        label: "Visibilité",
        required: false,
        showInCompact: true,
        options: [
            { value: "guest", label: "Invité" },
            { value: "super_admin", label: "Super admin" },
        ],
    },
}));
</script>

<template>
    <Head title="Types de ressources" />

    <Container class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Types de ressources</h1>
                <p class="text-primary-200 mt-2">
                    Gérer les types métiers et la registry DofusDB (utilisé / non utilisé / en attente).
                </p>
            </div>
            <div class="flex gap-2">
                <Btn
                    v-if="tableMode === 'server'"
                    variant="ghost"
                    :loading="loadingAll"
                    @click="handleLoadAllForClientMode"
                    :title="'Charge un lot (limité) et active tri/filtre/pagination côté client'"
                >
                    <i class="fa-solid fa-bolt mr-2"></i>
                    Mode client (charger tout)
                </Btn>
                <Btn
                    v-else
                    variant="ghost"
                    :loading="loadingAll"
                    @click="handleReloadClientDataset"
                    :disabled="!baseServerQuery"
                    :title="'Recharge le dataset (même sous-ensemble serveur) et conserve tes filtres client'"
                >
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger dataset
                </Btn>
                <Btn
                    v-if="tableMode === 'client'"
                    variant="ghost"
                    @click="handleSwitchToServerMode"
                    :title="'Revient au mode serveur (pagination/filtrage backend)'"
                >
                    <i class="fa-solid fa-server mr-2"></i>
                    Mode serveur
                </Btn>

                <Btn v-if="canCreateResolved" @click="createOpen = true" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un type
                </Btn>
            </div>
        </div>

        <Alert
            v-if="tableMode === 'client' && baseServerQuery"
            color="info"
            variant="soft"
        >
            <template #content>
                <div class="space-y-1">
                    <div class="font-semibold">Sous-ensemble chargé depuis le serveur</div>
                    <div class="text-sm opacity-80">
                        <span v-if="baseServerQuery.search">Recherche: "<b>{{ baseServerQuery.search }}</b>"</span>
                        <span v-else>Recherche: —</span>
                        <span class="mx-2">•</span>
                        <span>Filtres: {{ Object.keys(baseServerQuery.filters || {}).length || 0 }}</span>
                        <span class="mx-2">•</span>
                        <span>Tri: {{ baseServerQuery.sort ? `${baseServerQuery.sort} (${baseServerQuery.order || 'desc'})` : '—' }}</span>
                    </div>
                    <div class="text-sm opacity-80">
                        Tu peux maintenant appliquer des filtres/tri supplémentaires côté client (sans requête serveur).
                    </div>
                </div>
            </template>
        </Alert>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[1fr_380px]': selectedEntities.length >= 1 }"
        >
            <div>
                <EntityTable
                    v-model:selected-entities="selectedEntities"
                    :entities="tableMode === 'client' ? (allResourceTypes || []) : (resourceTypes.data || [])"
                    :columns="columns"
                    entity-type="resource-types"
                    :pagination="resourceTypes"
                    :show-filters="true"
                    :show-selection="canModifyResolved"
                    :can-manage="canManage"
                    :search="search"
                    :filters="filters"
                    :filterable-columns="filterableColumns"
                    :mode="tableMode"
                    @view="handleView"
                    @edit="handleEdit"
                    @quick-edit="handleEdit"
                    @delete="handleDelete"
                    @cell-update="handleCellUpdate"
                    @sort="handleSort"
                    @page-change="handlePageChange"
                    @update:search="handleSearchUpdate"
                    @update:filters="handleFiltersUpdate"
                    @refresh-all="handleRefreshAll"
                />
            </div>

            <div v-if="canModifyResolved && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <ResourceTypeBulkEditPanel
                    :selected-entities="selectedEntities"
                    :is-admin="canModifyResolved"
                    @applied="handleBulkApplied"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <CreateEntityModal
            :open="createOpen"
            entity-type="resourceType"
            :fields-config="fieldsConfig"
            :default-entity="{ usable: true, is_visible: 'guest', decision: 'pending' }"
            route-name-base="entities.resource-types"
            route-param-key="resourceType"
            @close="createOpen = false"
            @created="createOpen = false"
        />

        <Modal :open="editOpen" size="xl" placement="middle-center" close-on-esc @close="closeEdit">
            <template #header>
                <h3 class="text-2xl font-bold text-primary-100">Éditer type de ressource</h3>
            </template>
            <div class="max-h-[70vh] overflow-y-auto pr-2" v-if="selectedEntity">
                <EntityEditForm
                    :entity="selectedEntity"
                    entity-type="resourceType"
                    :fields-config="fieldsConfig"
                    :is-updating="true"
                    route-name-base="entities.resource-types"
                    route-param-key="resourceType"
                    @submit="closeEdit"
                    @cancel="closeEdit"
                />
            </div>
        </Modal>
    </Container>
</template>


