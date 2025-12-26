<script setup>
/**
 * Item Index Page
 * 
 * @description
 * Page de liste des items avec tableau et modal
 * 
 * @props {Object} items - Collection paginée des items
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useEntityComparison } from "@/Composables/utils/useEntityComparison";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Item } from "@/Models/Entity/Item";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { createItemsTanStackTableConfig } from './items-tanstack-table-config';

const props = defineProps({
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

// Notifications
const notificationStore = useNotificationStore();

// Permissions
const { canCreate: canCreatePermission, canUpdateAny, canManageAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('items'));
const canModify = computed(() => canUpdateAny('items'));
const canManage = computed(() => canManageAny('items'));

// Table v2 state (client-first)
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const serverUrl = computed(() => `${route('api.tables.items')}?limit=5000&_t=${refreshToken.value}`);

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    const idSet = new Set(selectedIds.value);
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(r?.id))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Item.fromArray(raw);
});

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

// UX: en édition rapide simple, on force un seul élément sélectionné.
watch(
    () => selectedIds.value,
    (ids) => {
        if (!quickEditMode.value) return;
        if (multiEditMode.value) return;
        if (!Array.isArray(ids) || ids.length <= 1) return;
        selectedIds.value = [ids[ids.length - 1]];
    },
    { deep: true },
);

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
// Mode édition rapide
const quickEditMode = ref(false);
const quickEditViewMode = ref('compact');

const multiEditMode = ref(false);

// Sécurité UX: si l'utilisateur perd le droit de modifier, on coupe les modes d'édition.
watch(
    () => canModify.value,
    (allowed) => {
        if (allowed) return;
        quickEditMode.value = false;
        multiEditMode.value = false;
        selectedIds.value = [];
    },
    { immediate: true }
);

const tableConfig = computed(() => {
    // Sélection active uniquement en mode édition rapide (et sera aussi gated par updateAny via wrapper).
    return createItemsTanStackTableConfig({ selectionEnabled: canModify.value && quickEditMode.value });
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

// Configuration des champs pour les items (identique à Edit.vue)
const fieldsConfig = {
    name: { 
        type: 'text', 
        label: 'Nom', 
        required: true, 
        showInCompact: true 
    },
    description: { 
        type: 'textarea', 
        label: 'Description', 
        required: false, 
        showInCompact: false 
    },
    level: { 
        type: 'number', 
        label: 'Niveau', 
        required: false, 
        showInCompact: true 
    },
    rarity: { 
        type: 'select', 
        label: 'Rareté', 
        required: false, 
        showInCompact: true,
        options: [
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
        ]
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Comparaison des entités sélectionnées pour l'édition multiple
const comparison = computed(() => {
    if (multiEditMode.value && selectedEntities.value.length > 0) {
        return useEntityComparison(selectedEntities.value, fieldsConfig);
    }
    return {
        commonValues: {},
        differentFields: [],
        hasDifferences: false
    };
});

const handleQuickEditSubmit = () => {
    // Recharger les données après sauvegarde
    refreshToken.value++;
    // Réinitialiser la sélection après sauvegarde
    selectedIds.value = [];
};

const handleQuickEditCancel = () => {
    selectedIds.value = [];
    if (multiEditMode.value) {
        multiEditMode.value = false;
    }
};

// Handlers pour les actions du menu
const handleQuickView = (entity) => {
    handleView(entity);
};

const handleQuickEditAction = (entity) => {
    if (!quickEditMode.value) {
        quickEditMode.value = true;
    }
    selectedIds.value = [entity.id];
    multiEditMode.value = false; // Désactiver le mode multiple si on passe en édition simple
};

const handleRefresh = (entity) => {
    refreshToken.value++;
};

const handleRefreshAll = () => {
    refreshToken.value++;
};

const handleDownloadPdf = async (entity) => {
    // Le téléchargement est géré directement par EntityActionsMenu via useDownloadPdf
    // Cette méthode peut être utilisée pour des actions supplémentaires si nécessaire
    console.log('Téléchargement PDF pour:', entity);
};
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
                <!-- Toggle édition rapide -->
                <div class="flex items-center gap-2">
                    <ToggleField
                        v-if="canModify"
                        v-model="quickEditMode"
                        label="Édition rapide"
                    />
                    <ToggleField
                        v-if="canModify && quickEditMode"
                        v-model="multiEditMode"
                        label="Sélection multiple"
                    />
                </div>
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un objet
                </Btn>
            </div>
        </div>

        <!-- Vue en 2 colonnes si édition rapide activée -->
        <div v-if="quickEditMode" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Colonne gauche : Tableau -->
            <div>
                <EntityTanStackTable
                    entity-type="items"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <!-- Colonne droite : Formulaire d'édition -->
            <div class="lg:sticky lg:top-4 lg:h-fit">
                <!-- Édition multiple -->
                <div v-if="multiEditMode && selectedEntities.length > 0" class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">
                            Édition multiple
                            <span class="badge badge-primary">{{ selectedEntities.length }}</span>
                        </h2>
                        <div v-if="comparison.hasDifferences" class="alert alert-warning mb-4">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div>
                                <p class="text-sm">
                                    Certains champs ont des valeurs différentes entre les entités sélectionnées.
                                    Les champs vides seront ignorés lors de la sauvegarde.
                                </p>
                            </div>
                        </div>
                        <EntityEditForm
                            :entity="comparison.commonValues"
                            entity-type="item"
                            :view-mode="quickEditViewMode"
                            :fields-config="fieldsConfig"
                            :is-updating="true"
                            @submit="handleMultiEditSubmit"
                            @cancel="handleQuickEditCancel"
                            @update:view-mode="quickEditViewMode = $event"
                        />
                    </div>
                </div>
                <!-- Édition simple -->
                <div v-else-if="!multiEditMode && quickEditEntity" class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Édition rapide</h2>
                        <EntityEditForm
                            :entity="quickEditEntity"
                            entity-type="item"
                            :view-mode="quickEditViewMode"
                            :fields-config="fieldsConfig"
                            :is-updating="true"
                            @submit="handleQuickEditSubmit"
                            @cancel="handleQuickEditCancel"
                            @update:view-mode="quickEditViewMode = $event"
                        />
                    </div>
                </div>
                <div v-else class="card bg-base-200 shadow-xl">
                    <div class="card-body text-center">
                        <p class="text-base-content/70">
                            <span v-if="multiEditMode">
                                Sélectionnez un ou plusieurs objets dans le tableau pour les éditer
                            </span>
                            <span v-else>
                                Sélectionnez un objet dans le tableau pour l'éditer rapidement
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue normale (tableau seul) si édition rapide désactivée -->
        <EntityTanStackTable
            v-else
            entity-type="items"
            :config="tableConfig"
            :server-url="serverUrl"
            v-model:selected-ids="selectedIds"
            @loaded="handleTableLoaded"
            @row-dblclick="handleRowDoubleClick"
        />

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="item"
            :fields-config="fieldsConfig"
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

