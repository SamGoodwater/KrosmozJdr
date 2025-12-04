<script setup>
/**
 * SectionEntityTable Template
 * 
 * @description
 * Template de section pour afficher un tableau d'entités.
 * - Affiche une table d'entités avec filtres et colonnes personnalisables
 * - À implémenter selon les besoins spécifiques
 * 
 * @props {Object} params - Paramètres de la section
 * @props {String} params.entity - Type d'entité à afficher (requis)
 * @props {Object} params.filters - Filtres à appliquer (optionnel)
 * @props {Array} params.columns - Colonnes à afficher (optionnel)
 * @props {Object} section - Données complètes de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionEntityTable 
 *   :params="{ 
 *     entity: 'classes',
 *     filters: { is_visible: 'guest' },
 *     columns: ['name', 'description']
 *   }"
 *   :section="section"
 * />
 */
import { computed } from 'vue';

const props = defineProps({
    params: {
        type: Object,
        required: true,
        default: () => ({})
    },
    section: {
        type: Object,
        required: true
    },
    user: {
        type: Object,
        default: null
    }
});

/**
 * Type d'entité
 */
const entityType = computed(() => {
    return props.params.entity || null;
});

/**
 * Filtres
 */
const filters = computed(() => {
    return props.params.filters || {};
});

/**
 * Colonnes
 */
const columns = computed(() => {
    return props.params.columns || [];
});
</script>

<template>
    <div class="section-entity-table">
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle"></i>
            <div>
                <h3 class="font-bold">Tableau d'entités</h3>
                <p class="text-sm">
                    Type d'entité : <strong>{{ entityType || 'Non spécifié' }}</strong>
                </p>
                <p class="text-sm mt-1">
                    Cette fonctionnalité sera implémentée prochainement.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.section-entity-table {
    // Styles pour le tableau d'entités
}
</style>

