<script setup>
/**
 * Monster Edit Page
 * 
 * @description
 * Page d'édition d'un monstre avec deux modes d'affichage (Grand et Compact)
 * 
 * @props {Object} monster - Données du monstre à éditer
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
    monster: {
        type: Object,
        required: true
    },
    availableScenarios: {
        type: Array,
        default: () => []
    },
    availableCampaigns: {
        type: Array,
        default: () => []
    },
    availableSpells: {
        type: Array,
        default: () => []
    }
});

// Mode d'affichage par défaut
const viewMode = ref('large');

// Configuration des champs pour les monstres
// Note: Les monstres ont une relation avec Creature pour le nom
const fieldsConfig = {
    size: { 
        type: 'number', 
        label: 'Taille', 
        required: false, 
        showInCompact: true 
    },
    is_boss: { 
        type: 'checkbox', 
        label: 'Boss', 
        required: false, 
        showInCompact: true 
    },
    boss_pa: { 
        type: 'number', 
        label: 'PA Boss', 
        required: false, 
        showInCompact: false 
    }
};

// Extraire les données du monstre (gérer la structure Resource)
const monster = computed(() => {
    const monsterData = props.monster || page.props.monster || {};
    if (monsterData.data && typeof monsterData.data === 'object' && monsterData.data.id) {
        return monsterData.data;
    }
    return monsterData;
});

// Nom du monstre depuis la relation creature
const monsterName = computed(() => {
    return monster.value?.creature?.name || 'Nouveau monstre';
});

setPageTitle(`Modifier le monstre : ${monsterName.value}`);
</script>

<template>
    <Head :title="`Modifier le monstre : ${monsterName}`" />
    
    <Container class="space-y-6">
        <div class="p-4 bg-base-200 rounded-lg">
            <p class="text-sm text-base-content/70">
                <strong>Note :</strong> Le nom du monstre est géré via la relation Creature. 
                Pour modifier le nom, éditez la créature associée.
            </p>
            <p v-if="monster?.creature" class="mt-2">
                <strong>Créature associée :</strong> {{ monster.creature.name }}
            </p>
        </div>
        
        <EntityEditForm
            :entity="monster"
            entity-type="monster"
            :view-mode="viewMode"
            :fields-config="fieldsConfig"
            :is-updating="true"
            @update:view-mode="viewMode = $event"
        />
        
        <!-- Gestion des scénarios du monstre -->
        <EntityRelationsManager
            :relations="monster?.scenarios || []"
            :available-items="availableScenarios"
            :entity-id="monster?.id"
            entity-type="monsters"
            relation-type="scenarios"
            relation-name="Scénarios du monstre"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'scénario',
                itemLabelPlural: 'scénarios'
            }"
        />
        
        <!-- Gestion des campagnes du monstre -->
        <EntityRelationsManager
            :relations="monster?.campaigns || []"
            :available-items="availableCampaigns"
            :entity-id="monster?.id"
            entity-type="monsters"
            relation-type="campaigns"
            relation-name="Campagnes du monstre"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'campagne',
                itemLabelPlural: 'campagnes'
            }"
        />
        
        <!-- Gestion des sorts d'invocation du monstre -->
        <EntityRelationsManager
            :relations="monster?.spellInvocations || []"
            :available-items="availableSpells"
            :entity-id="monster?.id"
            entity-type="monsters"
            relation-type="spellInvocations"
            relation-name="Sorts d'invocation du monstre"
            :config="{
                displayFields: ['name', 'description', 'level'],
                searchFields: ['name', 'description'],
                routeName: 'entities.monsters.updateSpellInvocations',
                itemLabel: 'sort',
                itemLabelPlural: 'sorts'
            }"
        />
    </Container>
</template>

