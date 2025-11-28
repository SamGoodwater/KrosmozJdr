<script setup>
/**
 * EntityViewMinimal Molecule
 * 
 * @description
 * Vue minimale d'une entité avec seulement les infos importantes
 * Affichées sous forme d'icônes avec tooltips
 * Peut s'agrandir au hover pour afficher plus de choses
 * Utilisée dans des grilles, petites modals ou hovers
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {Array} importantFields - Liste des champs importants à afficher
 */
import { ref } from 'vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    importantFields: {
        type: Array,
        default: () => ['level', 'rarity', 'usable', 'is_visible']
    }
});

const isHovered = ref(false);

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

const getFieldIcon = (field) => {
    const icons = {
        level: 'fa-solid fa-level-up-alt',
        rarity: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        price: 'fa-solid fa-coins',
        life: 'fa-solid fa-heart',
        pa: 'fa-solid fa-bolt',
        po: 'fa-solid fa-crosshairs'
    };
    return icons[field] || 'fa-solid fa-info-circle';
};
</script>

<template>
    <div 
        class="relative p-3 rounded-lg border border-base-300 transition-all duration-200"
        :class="{ 'bg-base-200 shadow-md scale-105': isHovered }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false">
        
        <!-- Nom et icône principale -->
        <div class="flex items-center gap-2 mb-2">
            <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="md" />
            <span class="font-semibold text-primary-100 text-sm">{{ entity.name || entity.title }}</span>
        </div>

        <!-- Infos importantes en icônes -->
        <div class="flex gap-2 flex-wrap">
            <template v-for="field in importantFields" :key="field">
                <Tooltip 
                    v-if="entity[field] !== null && entity[field] !== undefined"
                    :content="`${field}: ${entity[field]}`"
                    placement="top">
                    <div class="flex items-center gap-1">
                        <Icon :source="getFieldIcon(field)" :alt="field" size="xs" />
                        <span class="text-xs text-primary-300">{{ entity[field] }}</span>
                    </div>
                </Tooltip>
            </template>
        </div>

        <!-- Contenu supplémentaire au hover -->
        <div v-if="isHovered" class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300">
            <template v-for="(value, key) in entity" :key="key">
                <div v-if="!importantFields.includes(key) && !['id', 'name', 'title', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined">
                    <span class="font-semibold">{{ key }}:</span> 
                    <span v-if="Array.isArray(value)">{{ value.length }} élément(s)</span>
                    <span v-else-if="typeof value === 'object'">{{ value.name || value.title || 'Objet' }}</span>
                    <span v-else>{{ value }}</span>
                </div>
            </template>
        </div>
    </div>
</template>

