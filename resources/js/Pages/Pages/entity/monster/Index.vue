<script setup>
/**
 * Monster Index Page
 * 
 * @description
 * Page de liste des monstres avec tableau et modal
 * 
 * @props {Object} monsters - Collection paginée des monstres
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Monster } from "@/Models/Entity/Monster";
import { useEntityIndexQuickEditTable } from "@/Composables/entity/useEntityIndexQuickEditTable.js";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
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
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";
import {
    normalizeIndexTableFilters,
    useEntityIndexTableApiUrl,
} from "@/Composables/entity/useEntityIndexTableFilters";

const props = defineProps({
    monsters: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    creatures: {
        type: Array,
        default: () => []
    },
    monsterRaces: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Monstres');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('monsters'));
const canModify = computed(() => canUpdateAny('monsters'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();
const { refreshEntity } = useScrapping();

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('full');
const createModalOpen = ref(false);
const quickEditModalOpen = ref(false);
const quickEditEntity = ref(null);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);
const { tableQuickEditEnabled, onUpdateTableQuickEdit } = useEntityIndexQuickEditTable(Monster);

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: {
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
        creatures: props.creatures || [],
        monsterRaces: props.monsterRaces || [],
    };
    const descriptors = getMonsterFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});

const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));
const serverUrl = useEntityIndexTableApiUrl("api.tables.monsters", () => props.filters, refreshToken);

const filteredIds = computed(() => selectedIds.value || []);

// Sécurité UX: si l'utilisateur perd le droit de modifier, on coupe les modes d'édition.
watch(
    () => canModify.value,
    (allowed) => {
        if (allowed) return;
        selectedIds.value = [];
    },
    { immediate: true }
);

// Calcul des entités sélectionnées depuis les IDs et les rows
const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Monster.fromArray(raw);
});

// Fields config pour les formulaires (généré depuis les descriptors)
const monsterDescriptors = computed(() =>
    getMonsterFieldDescriptors({
        capabilities: { updateAny: canModify.value, createAny: canCreate.value },
        creatures: props.creatures || [],
        monsterRaces: props.monsterRaces || [],
    })
);
const fieldsConfig = computed(() =>
    createFieldsConfigFromDescriptors(monsterDescriptors.value, {
        capabilities: { updateAny: canModify.value },
        creatures: props.creatures || [],
        monsterRaces: props.monsterRaces || [],
    })
);
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(monsterDescriptors.value));

// Bulk edit
const handleBulkApplied = async (payload) => {
    const ok = await bulkPatchJson({ url: "/api/entities/monsters/bulk", payload });
    if (!ok) return;
    refreshToken.value++;
    selectedIds.value = [];
};

const clearSelection = () => {
    selectedIds.value = [];
};

const tableMeta = ref({});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const openModal = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'full';
    modalOpen.value = true;
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Monster.fromArray([raw])[0] || null;
    if (!model) return;
    openModal(model);
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
    ModelClass: Monster,
    routeShowName: "entities.monsters.show",
    routeShowParam: "monster",
    canModify: () => canModify.value,
    openFullModal: (model) => {
        selectedEntity.value = model;
        modalView.value = "full";
        modalOpen.value = true;
    },
    openEdit: (model) => {
        quickEditEntity.value = model;
        quickEditModalOpen.value = true;
    },
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Monster, l'utiliser directement
    const model = targetEntity instanceof Monster ? targetEntity : Monster.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.monsters.show', { monster: entityId }));
            break;

        case 'quick-view':
            openModal(model);
            break;

        case 'edit':
            router.visit(route('entities.monsters.edit', { monster: entityId }));
            break;

        case 'quick-edit':
            quickEditEntity.value = model;
            quickEditModalOpen.value = true;
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('monster');
            const url = resolveEntityRouteUrl('monster', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            // TODO: Implémenter le téléchargement PDF
            break;

        case 'refresh':
            await refreshEntity('monster', entityId, { forceUpdate: true });
            refreshToken.value++;
            break;

        case 'delete':
            // TODO: Implémenter la suppression avec confirmation
            break;
    }
};

// Handlers pour les actions du modal
const handleModalQuickEdit = (entity) => {
    quickEditEntity.value = entity;
    quickEditModalOpen.value = true;
    closeModal();
};

const handleModalExpand = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    router.visit(route('entities.monsters.show', { monster: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('monster');
    const url = resolveEntityRouteUrl('monster', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'entité copié !");
    }
};

const handleModalDownloadPdf = () => {
    // TODO: Implémenter le téléchargement PDF
};

const handleModalRefresh = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    await refreshEntity('monster', entityId, { forceUpdate: true });
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = () => {
    // TODO: Implémenter la suppression avec confirmation
};

const handleQuickEditSubmit = async (payload) => {
    if (payload) {
        const ok = await bulkPatchJson("/api/entities/monsters/bulk", payload);
        if (!ok) return;
    }
    refreshToken.value++;
    quickEditEntity.value = null;
    quickEditModalOpen.value = false;
};
</script>

<template>
    <Head title="Liste des Monstres" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Monstres</h1>
                <p class="text-primary-200 mt-2">Gérez les monstres du jeu</p>
            </div>
            <div class="flex gap-2">
                <Btn variant="ghost" @click="refreshToken++" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
                </Btn>
                <Btn variant="ghost" @click="router.visit(route('entities.monster-races.index'))">
                    <i class="fa-solid fa-users mr-2"></i>
                    Races de monstres
                </Btn>
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un monstre
                </Btn>
            </div>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div
            class="grid grid-cols-1 gap-4"
            :class="{
                'xl:grid-cols-[minmax(0,1fr)_380px]':
                    canModify && selectedEntities.length >= 1 && tableQuickEditEnabled,
            }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="monsters"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :initial-filter-values="indexTableFilters"
                    :response-adapter="getEntityResponseAdapter('monsters')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @update:quick-edit-enabled="onUpdateTableQuickEdit"
                    @action="handleTableAction"
                />
            </div>

            <!-- Quick Edit Panel -->
            <div v-if="canModify && selectedEntities.length >= 1 && tableQuickEditEnabled" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="monsters"
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    mode="client"
                    :filtered-ids="filteredIds"
                    :extra-ctx="{ creatures: props.creatures || [], monsterRaces: props.monsterRaces || [] }"
                    @applied="handleBulkApplied"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="monster"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('monsters')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="monster"
            :view="modalView"
            :open="modalOpen"
            :table-meta="tableMeta"
            @close="closeModal"
            @quick-edit="handleModalQuickEdit"
            @expand="handleModalExpand"
            @copy-link="handleModalCopyLink"
            @download-pdf="handleModalDownloadPdf"
            @refresh="handleModalRefresh"
            @delete="handleModalDelete"
        />

        <!-- Modal d'édition rapide -->
        <EntityQuickEditModal
            v-if="quickEditEntity"
            :entity="quickEditEntity"
            entity-type="monster"
            :fields-config="fieldsConfig"
            :open="quickEditModalOpen"
            @close="quickEditModalOpen = false"
            @submit="handleQuickEditSubmit"
        />
    </div>
</template>
