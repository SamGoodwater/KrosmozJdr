<script setup>
/**
 * Panoply Index Page
 * 
 * @description
 * Page de liste des panoplies avec tableau et modal
 * 
 * @props {Object} panoplies - Collection paginée des panoplies
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Panoply } from "@/Models/Entity/Panoply";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import { createPanopliesTanStackTableConfig } from './panoplies-tanstack-table-config';
import { adaptPanoplyEntitiesTableResponse } from "@/Entities/panoply/panoply-adapter";
import { getPanoplyFieldDescriptors } from "@/Entities/panoply/panoply-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    panoplies: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Panoplies');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('panoplies'));
const canModify = computed(() => canUpdateAny('panoplies'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);

// Table v2
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createPanopliesTanStackTableConfig());
const serverUrl = computed(() => `${route('api.tables.panoplies')}?format=entities&limit=5000&_t=${refreshToken.value}`);

// Fields config pour les formulaires (généré depuis les descriptors)
const fieldsConfig = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createFieldsConfigFromDescriptors(getPanoplyFieldDescriptors(ctx));
});

const defaultEntity = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createDefaultEntityFromDescriptors(getPanoplyFieldDescriptors(ctx));
});

// Bulk edit
const handleBulkUpdate = async (payload) => {
  await bulkPatchJson('/api/entities/panoplies/bulk', payload);
  refreshToken.value++;
};

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Panoply.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = 'large';
    modalOpen.value = true;
};

const handleCreate = () => {
    createModalOpen.value = true;
};

const handleCloseCreateModal = () => {
    createModalOpen.value = false;
};

const handleEntityCreated = () => {
    createModalOpen.value = false;
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Panoplies" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Panoplies</h1>
                <p class="text-primary-200 mt-2">Gérez les panoplies (ensembles d'équipements)</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une panoplie
            </Btn>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div class="xl:grid xl:grid-cols-[minmax(0,1fr)_380px] xl:gap-6">
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="panoplies"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptPanoplyEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <!-- Quick Edit Panel -->
            <EntityQuickEditPanel
                v-if="canModify && selectedIds.length > 0"
                entity-type="panoplies"
                :selected-ids="selectedIds"
                :fields-config="fieldsConfig"
                :default-entity="defaultEntity"
                @update="handleBulkUpdate"
            />
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="panoply"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="panoply"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </div>
</template>
