<script setup>
/**
 * ResourceType Index Page
 *
 * @description
 * Table de gestion des types de ressources (incluant la registry DofusDB).
 */
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";

import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import EntityTanStackTable from "@/Pages/Organismes/table/EntityTanStackTable.vue";
import CreateEntityModal from "@/Pages/Organismes/entity/CreateEntityModal.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import EntityQuickEditPanel from "@/Pages/Organismes/entity/EntityQuickEditPanel.vue";
import { createResourceTypesTanStackTableConfig } from "./resource-types-tanstack-table-config";
import { adaptResourceTypeEntitiesTableResponse } from "@/Entities/resource-type/resource-type-adapter";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    resourceTypes: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    can: { type: Object, default: () => ({}) },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Types de ressources");

// (legacy) canModify inutilisé : garder uniquement les versions "Resolved"
const { canUpdateAny, canCreate } = usePermissions();
const canModifyResolved = computed(() => Boolean(props.can?.updateAny ?? canUpdateAny('resource-types')));
const canCreateResolved = computed(() => Boolean(props.can?.create ?? canCreate('resource-types')));
// (legacy) canManage inutilisé : garder uniquement les versions "Resolved"

const { bulkPatchJson } = useBulkRequest();

const selectedEntity = ref(null);
const editOpen = ref(false);
const createOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createResourceTypesTanStackTableConfig());
const serverUrl = computed(() => `${route("api.tables.resource-types")}?limit=5000&format=entities&_t=${refreshToken.value}`);

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
    const ok = await bulkPatchJson({ url: "/api/scrapping/resource-types/bulk", payload });
    if (!ok) return;
    refreshToken.value++;
    selectedIds.value = [];
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
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    return (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
});

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const resourceTypeDescriptors = computed(() => getResourceTypeFieldDescriptors({ capabilities: props.can || {} }));
const fieldsConfig = computed(() => createFieldsConfigFromDescriptors(resourceTypeDescriptors.value, { meta: {}, capabilities: props.can || {} }));
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(resourceTypeDescriptors.value));
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
            :class="{ 'xl:grid-cols-[minmax(0,1fr)_380px]': selectedEntities.length >= 1 }"
        >
            <div class="min-w-0">
                <EntityTanStackTable
                    entity-type="resource-types"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptResourceTypeEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <div v-if="canModifyResolved && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="resource-types"
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
            :default-entity="defaultEntity"
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


