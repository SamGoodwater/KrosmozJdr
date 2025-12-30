<script setup>
/**
 * Item Index Page
 * 
 * @description
 * Page de liste des items avec tableau et modal
 * 
 * @props {Object} items - Collection paginée des items
 */
import { Head } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Item } from "@/Models/Entity/Item";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import EntityQuickEditPanel from "@/Pages/Organismes/entity/EntityQuickEditPanel.vue";
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { createItemsTanStackTableConfig } from './items-tanstack-table-config';
import { adaptItemEntitiesTableResponse } from "@/Entities/item/item-adapter";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

// Props Inertia (gardées à titre documentaire, même si non utilisées directement ici)
defineProps({
    items: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Objets');

const { bulkPatchJson } = useBulkRequest();

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('items'));
const canModify = computed(() => canUpdateAny('items'));
// const canManage = computed(() => canManageAny('items'));

// Table v2 state (client-first)
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const serverUrl = computed(() => `${route('api.tables.items')}?limit=5000&format=entities&_t=${refreshToken.value}`);

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Item.fromArray(raw);
});

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);

// Sécurité UX: si l'utilisateur perd le droit de modifier, on coupe les modes d'édition.
watch(
    () => canModify.value,
    (allowed) => {
        if (allowed) return;
        selectedIds.value = [];
    },
    { immediate: true }
);

const tableConfig = computed(() => {
    // Sélection sera gated par updateAny via wrapper.
    return createItemsTanStackTableConfig({ selectionEnabled: true });
});

// Handlers
const handleView = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'large';
    modalOpen.value = true;
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Item.fromArray([raw])[0] || null;
    if (!model) return;
    handleView(model);
};

const handleCreate = () => {
    createModalOpen.value = true;
};

const handleCloseCreateModal = () => {
    createModalOpen.value = false;
};

const handleEntityCreated = () => {
    createModalOpen.value = false;
    // Le rechargement est géré par CreateEntityModal
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};

// Descriptors -> fieldsConfig (source de vérité unique)
const itemDescriptors = computed(() => getItemFieldDescriptors({ capabilities: { updateAny: canModify.value } }));
const fieldsConfig = computed(() => createFieldsConfigFromDescriptors(itemDescriptors.value, { meta: {}, capabilities: { updateAny: canModify.value } }));
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(itemDescriptors.value));

const handleBulkApplied = async (payload) => {
    const ok = await bulkPatchJson({ url: "/api/entities/items/bulk", payload });
    if (!ok) return;
    refreshToken.value++;
    selectedIds.value = [];
};

const clearSelection = () => {
    selectedIds.value = [];
};

// const handleRefreshAll = () => refreshToken.value++;
</script>

<template>
    <Head title="Liste des Objets" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Objets</h1>
                <p class="text-primary-200 mt-2">Gérez les objets et équipements</p>
            </div>
            <div class="flex gap-2 items-center">
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un objet
                </Btn>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[minmax(0,1fr)_380px]': selectedEntities.length >= 1 }"
        >
            <div class="min-w-0">
                <EntityTanStackTable
                    entity-type="items"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptItemEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="items"
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    mode="client"
                    :filtered-ids="selectedIds"
                    @applied="handleBulkApplied"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="item"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="item"
            :view="modalView"
            :open="modalOpen"
            :use-stored-format="true"
            @close="closeModal"
        />
    </Container>
</template>

