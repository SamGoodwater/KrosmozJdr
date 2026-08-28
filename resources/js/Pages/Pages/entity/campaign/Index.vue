<script setup>
/**
 * Campaign Index Page
 * 
 * @description
 * Page de liste des campagnes avec tableau et modal
 * 
 * @props {Object} campaigns - Collection paginée des campagnes
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Campaign } from "@/Models/Entity/Campaign";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getCampaignFieldDescriptors } from "@/Entities/campaign/campaign-descriptors";

const props = defineProps({
    campaigns: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Campagnes');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('campaigns'));
const canModify = computed(() => canUpdateAny('campaigns'));

// Bulk request
const { copyToClipboard } = useCopyToClipboard();

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('full');
const createModalOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
    };
    const descriptors = getCampaignFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.campaigns')}?format=entities&limit=5000&_t=${refreshToken.value}`);



// Calcul des entités sélectionnées depuis les IDs et les rows

// Bulk edit


const tableMeta = ref({});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    // Si c'est déjà une instance Campaign, l'utiliser directement
    const model = raw instanceof Campaign ? raw : Campaign.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = "full";
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
    refreshToken.value++;
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};

const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: Campaign,
    routeShowName: "entities.campaigns.show",
    routeShowParam: "campaign",
    canModify: () => canModify.value,
    openFullModal: (model) => {
        selectedEntity.value = model;
        modalView.value = "full";
        modalOpen.value = true;
    },
    openEdit: (model) => {
        if (!model?.id) return;
        router.visit(route('entities.campaigns.edit', { campaign: model.id }));
    },
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Campaign, l'utiliser directement
    const model = targetEntity instanceof Campaign ? targetEntity : Campaign.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
        case 'quick-view':
            selectedEntity.value = model;
            modalView.value = 'full';
            modalOpen.value = true;
            break;

        case 'edit':
            router.visit(route('entities.campaigns.edit', { campaign: entityId }));
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('campaign');
            const url = resolveEntityRouteUrl('campaign', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            // TODO: Implémenter le téléchargement PDF
            break;
        case 'delete':
            // TODO: Implémenter la suppression avec confirmation
            break;
    }
};

// Handlers pour les actions du modal

const handleModalExpand = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    router.visit(route('entities.campaigns.show', { campaign: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('campaign');
    const url = resolveEntityRouteUrl('campaign', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'entité copié !");
    }
};

const handleModalDownloadPdf = (entity) => {
    // TODO: Implémenter le téléchargement PDF
};

const handleModalRefresh = () => {
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = (entity) => {
    // TODO: Implémenter la suppression avec confirmation
};

</script>

<template>
    <Head title="Liste des Campagnes" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Campagnes</h1>
                <p class="text-primary-200 mt-2">Gérez les campagnes de jeu</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une campagne
            </Btn>
        </div>
        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="campaigns"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('campaigns')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @action="handleTableAction"
                />
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="campaign"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('campaigns')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="campaign"
            :view="modalView"
            :open="modalOpen"
            :table-meta="tableMeta"
            @close="closeModal"
            @expand="handleModalExpand"
            @copy-link="handleModalCopyLink"
            @download-pdf="handleModalDownloadPdf"
            @refresh="handleModalRefresh"
            @delete="handleModalDelete"
        />
    </div>
</template>
