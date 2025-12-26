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
import { Panoply } from "@/Models/Entity/Panoply";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { createPanopliesTanStackTableConfig } from './panoplies-tanstack-table-config';

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
const { canCreate: canCreatePermission } = usePermissions();
const canCreate = computed(() => canCreatePermission('panoplies'));

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
const serverUrl = computed(() => `${route('api.tables.panoplies')}?limit=5000&_t=${refreshToken.value}`);

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
    router.visit(route('entities.panoplies.create'));
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Panoplies" />
    
    <Container class="space-y-6 pb-8">
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

        <EntityTanStackTable
            entity-type="panoplies"
            :config="tableConfig"
            :server-url="serverUrl"
            v-model:selected-ids="selectedIds"
            @loaded="handleTableLoaded"
            @row-dblclick="handleRowDoubleClick"
        />

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
    </Container>
</template>
