<script setup>
/**
 * Npc Edit Page
 * 
 * @description
 * Page d'édition d'un PNJ avec gestion des relations
 */
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    npc: {
        type: Object,
        required: true
    },
    availablePanoplies: {
        type: Array,
        default: () => []
    },
    availableScenarios: {
        type: Array,
        default: () => []
    },
    availableCampaigns: {
        type: Array,
        default: () => []
    }
});

const viewMode = ref('large');

const fieldsConfig = {
    story: { 
        type: 'textarea', 
        label: 'Histoire', 
        required: false, 
        showInCompact: false 
    },
    historical: { 
        type: 'textarea', 
        label: 'Historique', 
        required: false, 
        showInCompact: false 
    },
    age: { 
        type: 'text', 
        label: 'Âge', 
        required: false, 
        showInCompact: true 
    },
    size: { 
        type: 'text', 
        label: 'Taille', 
        required: false, 
        showInCompact: true 
    }
};

const npc = computed(() => {
    const npcData = props.npc || page.props.npc || {};
    if (npcData.data && typeof npcData.data === 'object' && npcData.data.id) {
        return npcData.data;
    }
    return npcData;
});

// Nom du PNJ depuis la relation creature
const npcName = computed(() => {
    return npc.value?.creature?.name || 'Nouveau PNJ';
});

setPageTitle(`Modifier le PNJ : ${npcName.value}`);
</script>

<template>
    <Head :title="`Modifier le PNJ : ${npcName}`" />
    
    <Container class="space-y-6">
        <div class="p-4 bg-base-200 rounded-lg">
            <p class="text-sm text-base-content/70">
                <strong>Note :</strong> Le nom du PNJ est géré via la relation Creature. 
                Pour modifier le nom, éditez la créature associée.
            </p>
            <p v-if="npc?.creature" class="mt-2">
                <strong>Créature associée :</strong> {{ npc.creature.name }}
            </p>
        </div>
        
        <EntityEditForm
            :entity="npc"
            entity-type="npc"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des panoplies du PNJ -->
        <EntityRelationsManager
            :relations="npc?.panoplies || []"
            :available-items="availablePanoplies"
            :entity-id="npc?.id"
            entity-type="npcs"
            relation-type="panoplies"
            relation-name="Panoplies du PNJ"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'panoplie',
                itemLabelPlural: 'panoplies'
            }"
        />
        
        <!-- Gestion des scénarios du PNJ -->
        <EntityRelationsManager
            :relations="npc?.scenarios || []"
            :available-items="availableScenarios"
            :entity-id="npc?.id"
            entity-type="npcs"
            relation-type="scenarios"
            relation-name="Scénarios du PNJ"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'scénario',
                itemLabelPlural: 'scénarios'
            }"
        />
        
        <!-- Gestion des campagnes du PNJ -->
        <EntityRelationsManager
            :relations="npc?.campaigns || []"
            :available-items="availableCampaigns"
            :entity-id="npc?.id"
            entity-type="npcs"
            relation-type="campaigns"
            relation-name="Campagnes du PNJ"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'campagne',
                itemLabelPlural: 'campagnes'
            }"
        />
    </Container>
</template>

