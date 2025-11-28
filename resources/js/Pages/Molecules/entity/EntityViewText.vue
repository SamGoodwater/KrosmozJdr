<script setup>
/**
 * EntityViewText Molecule
 * 
 * @description
 * Vue texte d'une entité : juste le nom avec une icône/image à la même taille
 * Au hover, affiche la vue minimale
 * Utilisée dans les listes simples
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 */
import { ref } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import EntityViewMinimal from './EntityViewMinimal.vue';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    }
});

const showHover = ref(false);

const getEntityIcon = (type) => {
    const icons = {
        attribute: 'fa-solid fa-list',
        campaign: 'fa-solid fa-book',
        capability: 'fa-solid fa-star',
        classe: 'fa-solid fa-user',
        consumable: 'fa-solid fa-flask',
        creature: 'fa-solid fa-paw',
        item: 'fa-solid fa-box',
        monster: 'fa-solid fa-dragon',
        npc: 'fa-solid fa-user-tie',
        panoply: 'fa-solid fa-layer-group',
        resource: 'fa-solid fa-gem',
        scenario: 'fa-solid fa-scroll',
        shop: 'fa-solid fa-store',
        specialization: 'fa-solid fa-graduation-cap',
        spell: 'fa-solid fa-wand-magic-sparkles'
    };
    return icons[type] || 'fa-solid fa-circle';
};
</script>

<template>
    <div 
        class="relative inline-flex items-center gap-2 cursor-pointer"
        @mouseenter="showHover = true"
        @mouseleave="showHover = false">
        
        <!-- Vue texte -->
        <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="sm" />
        <span class="text-primary-100">{{ entity.name || entity.title }}</span>

        <!-- Tooltip avec vue minimale au hover -->
        <div 
            v-if="showHover"
            class="absolute left-0 top-full mt-2 z-50 w-64"
            @mouseenter="showHover = true"
            @mouseleave="showHover = false">
            <EntityViewMinimal 
                :entity="entity" 
                :entity-type="entityType"
                class="shadow-lg" />
        </div>
    </div>
</template>

