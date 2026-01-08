<script setup>
/**
 * Monster Index Page
 * 
 * @description
 * Page de liste des monstres avec tableau et modal
 * 
 * @props {Object} monsters - Collection paginée des monstres
 */
import { Head } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Monster } from "@/Models/Entity/Monster";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import { createMonstersTanStackTableConfig } from './monsters-tanstack-table-config';
import { adaptMonsterEntitiesTableResponse } from "@/Entities/monster/monster-adapter";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    monsters: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
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

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createMonstersTanStackTableConfig());
const serverUrl = computed(() => `${route('api.tables.monsters')}?format=entities&limit=5000&_t=${refreshToken.value}`);

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
const monsterDescriptors = computed(() => getMonsterFieldDescriptors({ capabilities: { updateAny: canModify.value } }));
const fieldsConfig = computed(() => createFieldsConfigFromDescriptors(monsterDescriptors.value, { meta: {}, capabilities: { updateAny: canModify.value } }));
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

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Monster.fromArray([raw])[0] || null;
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
    <Head title="Liste des Monstres" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Monstres</h1>
                <p class="text-primary-200 mt-2">Gérez les monstres du jeu</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un monstre
            </Btn>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[minmax(0,1fr)_380px]': selectedEntities.length >= 1 }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="monsters"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptMonsterEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <!-- Quick Edit Panel -->
            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="monsters"
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
            entity-type="monster"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
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
            @close="closeModal"
        />
    </div>
</template>
