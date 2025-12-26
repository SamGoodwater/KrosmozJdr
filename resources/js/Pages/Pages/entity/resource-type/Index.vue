<script setup>
/**
 * ResourceType Index Page
 *
 * @description
 * Table de gestion des types de ressources (incluant la registry DofusDB).
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { usePermissions } from "@/Composables/permissions/usePermissions";

import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import EntityTanStackTable from "@/Pages/Organismes/table/EntityTanStackTable.vue";
import CreateEntityModal from "@/Pages/Organismes/entity/CreateEntityModal.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import ResourceTypeBulkEditPanel from "./components/ResourceTypeBulkEditPanel.vue";
import { createResourceTypesTanStackTableConfig } from "./resource-types-tanstack-table-config";

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
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createResourceTypesTanStackTableConfig());
const serverUrl = computed(() => `${route("api.tables.resource-types")}?limit=5000&_t=${refreshToken.value}`);

const handleRefreshAll = () => {
    refreshToken.value++;
};

const handleEdit = (entity) => {
    selectedEntity.value = entity;
    editOpen.value = true;
};

const handleRowDoubleClick = (row) => {
    if (!canModifyResolved.value) return;
    const entity = row?.rowParams?.entity;
    if (!entity) return;
    handleEdit(entity);
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

        // Table v2: recharger le dataset
        refreshToken.value++;

        // Clear selection
        selectedIds.value = [];
    } catch (e) {
        notifyError("Erreur bulk: " + (e?.message || "unknown"));
    }
};

const clearSelection = () => {
    selectedIds.value = [];
};

const closeEdit = () => {
    editOpen.value = false;
    selectedEntity.value = null;
};

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    const idSet = new Set(selectedIds.value);
    return (tableRows.value || [])
        .filter((r) => idSet.has(r?.id))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
});

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
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
                <Btn variant="ghost" @click="handleRefreshAll" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
                </Btn>

                <Btn v-if="canCreateResolved" @click="createOpen = true" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un type
                </Btn>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[1fr_380px]': selectedEntities.length >= 1 }"
        >
            <div>
                <EntityTanStackTable
                    entity-type="resource-types"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <div v-if="canModifyResolved && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <ResourceTypeBulkEditPanel
                    :selected-entities="selectedEntities"
                    :is-admin="canModifyResolved"
                    mode="client"
                    :filtered-ids="selectedIds"
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


