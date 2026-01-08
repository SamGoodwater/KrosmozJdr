<script setup>
/**
 * Capability Index Page
 * 
 * @description
 * Page de liste des capacités avec tableau et modal
 * 
 * @props {Object} capabilities - Collection paginée des capacités
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Capability } from "@/Models/Entity/Capability";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import { createCapabilitiesTanStackTableConfig } from './capabilities-tanstack-table-config';
import { adaptCapabilityEntitiesTableResponse } from "@/Entities/capability/capability-adapter";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    capabilities: {
        type: Object,
        required: true
    },
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Capacités');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('capabilities'));
const canModify = computed(() => canUpdateAny('capabilities'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();

// Table v2
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createCapabilitiesTanStackTableConfig());
const serverUrl = computed(() => `${route('api.tables.capabilities')}?format=entities&limit=5000&_t=${refreshToken.value}`);

// Fields config pour les formulaires (généré depuis les descriptors)
const fieldsConfig = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createFieldsConfigFromDescriptors(getCapabilityFieldDescriptors(ctx));
});

const defaultEntity = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createDefaultEntityFromDescriptors(getCapabilityFieldDescriptors(ctx));
});

// Bulk edit
const handleBulkUpdate = async (payload) => {
  await bulkPatchJson('/api/entities/capabilities/bulk', payload);
  refreshToken.value++;
};

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Capability.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = 'large';
    modalOpen.value = true;
};

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');

const handleCreate = () => {
    router.visit(route('entities.capabilities.create'));
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Capacités" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Capacités</h1>
                <p class="text-primary-200 mt-2">Gérez les capacités spéciales</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une capacité
            </Btn>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div class="xl:grid xl:grid-cols-[minmax(0,1fr)_380px] xl:gap-6">
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="capabilities"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptCapabilityEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <!-- Quick Edit Panel -->
            <EntityQuickEditPanel
                v-if="canModify && selectedIds.length > 0"
                entity-type="capabilities"
                :selected-ids="selectedIds"
                :fields-config="fieldsConfig"
                :default-entity="defaultEntity"
                @update="handleBulkUpdate"
            />
        </div>

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="capability"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </div>
</template>
