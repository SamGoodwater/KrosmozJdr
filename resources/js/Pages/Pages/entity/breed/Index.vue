<script setup>
/**
 * Breed Index Page (affichée « Liste des Classes »)
 *
 * @props {Object} breeds - Collection paginée des breeds
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Breed } from "@/Models/Entity/Breed";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useScrapping } from "@/Composables/utils/useScrapping";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import EntityQuickEditModal from '@/Pages/Organismes/entity/EntityQuickEditModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    breeds: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Classes');

const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('breeds'));
const canModify = computed(() => canUpdateAny('breeds'));

const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();
const { refreshEntity } = useScrapping();

const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => {
    const ctx = {
        capabilities: {
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
    };
    const descriptors = getBreedFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.breeds')}?format=entities&limit=5000&_t=${refreshToken.value}`);

const fieldsConfig = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createFieldsConfigFromDescriptors(getBreedFieldDescriptors(ctx));
});

const defaultEntity = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createDefaultEntityFromDescriptors(getBreedFieldDescriptors(ctx));
});

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Breed.fromArray(raw);
});

const handleBulkUpdate = async (payload) => {
  const ok = await bulkPatchJson('/api/entities/breeds/bulk', payload);
  if (!ok) return;
  refreshToken.value++;
  selectedIds.value = [];
};

const clearSelection = () => {
    selectedIds.value = [];
};

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = raw instanceof Breed ? raw : Breed.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = 'large';
    modalOpen.value = true;
};

const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const quickEditModalOpen = ref(false);
const quickEditEntity = ref(null);

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

const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;

    const model = targetEntity instanceof Breed ? targetEntity : Breed.fromArray([targetEntity])[0] || null;
    if (!model) return;

    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.breeds.show', { breed: entityId }));
            break;

        case 'quick-view':
            selectedEntity.value = model;
            modalView.value = 'large';
            modalOpen.value = true;
            break;

        case 'edit':
            router.visit(route('entities.breeds.edit', { breed: entityId }));
            break;

        case 'quick-edit':
            quickEditEntity.value = model;
            quickEditModalOpen.value = true;
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('breed');
            const url = resolveEntityRouteUrl('breed', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            break;

        case 'refresh':
            await refreshEntity('breed', entityId, { forceUpdate: true });
            refreshToken.value++;
            break;

        case 'delete':
            break;
    }
};

const handleModalQuickEdit = (entity) => {
    quickEditEntity.value = entity;
    quickEditModalOpen.value = true;
    closeModal();
};

const handleModalExpand = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    router.visit(route('entities.breeds.show', { breed: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('breed');
    const url = resolveEntityRouteUrl('breed', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'entité copié !");
    }
};

const handleModalDownloadPdf = (entity) => {};

const handleModalRefresh = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    await refreshEntity('breed', entityId, { forceUpdate: true });
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = (entity) => {};

const handleQuickEditSubmit = () => {
    refreshToken.value++;
    quickEditEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Classes" />

    <div class="space-y-6 pb-8 w-full">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Classes</h1>
                <p class="text-primary-200 mt-2">Gérez les classes jouables</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une classe
            </Btn>
        </div>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[minmax(0,1fr)_380px]': selectedEntities.length >= 1 }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="breeds"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('breeds')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @action="handleTableAction"
                />
            </div>

            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="breeds"
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    mode="client"
                    :filtered-ids="selectedIds"
                    @applied="handleBulkUpdate"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <CreateEntityModal
            :open="createModalOpen"
            entity-type="breed"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="breed"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
            @quick-edit="handleModalQuickEdit"
            @expand="handleModalExpand"
            @copy-link="handleModalCopyLink"
            @download-pdf="handleModalDownloadPdf"
            @refresh="handleModalRefresh"
            @delete="handleModalDelete"
        />

        <EntityQuickEditModal
            v-if="quickEditEntity"
            :entity="quickEditEntity"
            entity-type="breed"
            :fields-config="fieldsConfig"
            :open="quickEditModalOpen"
            @close="quickEditModalOpen = false"
            @submit="handleQuickEditSubmit"
        />
    </div>
</template>
