<script setup>
/**
 * Npc Index Page
 * 
 * @description
 * Page de liste des NPCs avec tableau et modal
 * 
 * @props {Object} npcs - Collection paginée des NPCs
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Npc } from "@/Models/Entity/Npc";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import { createNpcsTanStackTableConfig } from './npcs-tanstack-table-config';
import { adaptNpcEntitiesTableResponse } from "@/Entities/npc/npc-adapter";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    npcs: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des NPCs');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('npcs'));
const canModify = computed(() => canUpdateAny('npcs'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createNpcsTanStackTableConfig());
const serverUrl = computed(() => `${route('api.tables.npcs')}?format=entities&limit=5000&_t=${refreshToken.value}`);

// Fields config pour les formulaires (généré depuis les descriptors)
const fieldsConfig = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createFieldsConfigFromDescriptors(getNpcFieldDescriptors(ctx));
});

const defaultEntity = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createDefaultEntityFromDescriptors(getNpcFieldDescriptors(ctx));
});

// Bulk edit
const handleBulkUpdate = async (payload) => {
  await bulkPatchJson('/api/entities/npcs/bulk', payload);
  refreshToken.value++;
};

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Npc.fromArray([raw])[0] || null;
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
    <Head title="Liste des NPCs" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des NPCs</h1>
                <p class="text-primary-200 mt-2">Gérez les personnages non-joueurs</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un NPC
            </Btn>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div class="xl:grid xl:grid-cols-[minmax(0,1fr)_380px] xl:gap-6">
            <div class="min-w-0">
                <EntityTanStackTable
                    entity-type="npcs"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptNpcEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <!-- Quick Edit Panel -->
            <EntityQuickEditPanel
                v-if="canModify && selectedIds.length > 0"
                entity-type="npcs"
                :selected-ids="selectedIds"
                :fields-config="fieldsConfig"
                :default-entity="defaultEntity"
                @update="handleBulkUpdate"
            />
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="npc"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="npc"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
