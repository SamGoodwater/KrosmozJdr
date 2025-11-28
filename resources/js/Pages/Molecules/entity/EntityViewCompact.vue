<script setup>
/**
 * EntityViewCompact Molecule
 * 
 * @description
 * Vue compacte d'une entité avec toutes les infos mais dans un format condensé
 * Utilise tooltips et scroll pour les grands textes
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 */
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
    }
});

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

const truncate = (text, maxLength = 50) => {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};
</script>

<template>
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête compact -->
        <div class="flex gap-2 items-center">
            <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="md" />
            <h3 class="text-lg font-semibold text-primary-100">{{ entity.name || entity.title }}</h3>
        </div>

        <!-- Informations en liste compacte -->
        <div class="space-y-2 text-sm">
            <template v-for="(value, key) in entity" :key="key">
                <div v-if="!['id', 'name', 'title', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined"
                     class="flex items-center justify-between gap-2">
                    <span class="text-primary-400 text-xs">{{ key }}:</span>
                    <div class="flex-1 text-right">
                        <Tooltip v-if="typeof value === 'string' && value.length > 30" 
                                 :content="value" placement="left">
                            <span class="text-primary-200 truncate block max-w-xs">
                                {{ truncate(value, 30) }}
                            </span>
                        </Tooltip>
                        <Badge v-else-if="typeof value === 'boolean'" 
                               :color="value ? 'success' : 'error'" size="xs">
                            {{ value ? 'Oui' : 'Non' }}
                        </Badge>
                        <span v-else-if="Array.isArray(value)" class="text-primary-200">{{ value.length }} élément(s)</span>
                        <span v-else-if="typeof value === 'object'" class="text-primary-200">{{ value.name || value.title || 'Objet' }}</span>
                        <span v-else class="text-primary-200">{{ value }}</span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

