<script setup>
/**
 * EntityViewLarge Molecule
 * 
 * @description
 * Vue grande d'une entité avec tout le contenu affiché
 * Utilisée dans les grandes modals ou directement dans le main
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 */
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

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

// Fonction pour obtenir l'icône selon le type d'entité
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
    <div class="space-y-6">
        <!-- En-tête avec image et nom -->
        <div class="flex gap-4 items-start">
            <div v-if="entity.image" class="flex-shrink-0">
                <Image :source="entity.image" :alt="entity.name || 'Image'" size="lg" rounded="lg" />
            </div>
            <div v-else class="flex-shrink-0">
                <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="xl" />
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-primary-100">{{ entity.name || entity.title }}</h2>
                <p v-if="entity.description" class="text-primary-300 mt-2">{{ entity.description }}</p>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template v-for="(value, key) in entity" :key="key">
                <div v-if="!['id', 'name', 'title', 'description', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined">
                    <div class="flex flex-col">
                        <span class="text-xs text-primary-400 uppercase">{{ key }}</span>
                        <span class="text-primary-100">
                            <Badge v-if="typeof value === 'boolean'" :color="value ? 'success' : 'error'" size="sm">
                                {{ value ? 'Oui' : 'Non' }}
                            </Badge>
                            <span v-else-if="Array.isArray(value)">{{ value.length }} élément(s)</span>
                            <span v-else-if="typeof value === 'object'">{{ value.name || value.title || JSON.stringify(value) }}</span>
                            <span v-else>{{ value }}</span>
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

