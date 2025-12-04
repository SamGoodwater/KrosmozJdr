<script setup>
/**
 * PageRenderer Organism
 * 
 * @description
 * Composant organisme pour afficher une page dynamique avec ses sections.
 * - Affiche le titre de la page
 * - Rend toutes les sections affichables via SectionRenderer
 * - Gère l'ordre des sections
 * - Respecte la visibilité et l'état des sections
 * - Bouton discret pour modifier la page (si droits)
 * 
 * @props {Object} page - Données de la page (avec sections)
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * @props {Array} pages - Liste des pages disponibles (pour le modal d'édition)
 * 
 * @example
 * <PageRenderer :page="page" :user="user" :pages="pages" />
 */
import { computed, ref } from 'vue';
import SectionRenderer from './SectionRenderer.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EditPageModal from './EditPageModal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { Page } from '@/Models';

const props = defineProps({
    page: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value === 'object' && 'id' in value && 'title' in value;
        }
    },
    user: {
        type: Object,
        default: null
    },
    pages: {
        type: Array,
        default: () => []
    }
});

// Modal d'édition
const editModalOpen = ref(false);

// Utiliser le modèle Page pour normaliser l'accès aux données
const pageModel = computed(() => {
    if (!props.page) return null;
    return new Page(props.page);
});

// Vérifier si l'utilisateur peut modifier la page
const canEdit = computed(() => {
    if (!pageModel.value) return false;
    return pageModel.value.canUpdate;
});

const handleOpenEditModal = () => {
    editModalOpen.value = true;
};

const handleCloseEditModal = () => {
    editModalOpen.value = false;
};

const handlePageDeleted = () => {
    // Rediriger vers la liste des pages après suppression
    window.location.href = route('pages.index');
};

/**
 * Sections triées par ordre
 */
const sortedSections = computed(() => {
    if (!props.page.sections || !Array.isArray(props.page.sections)) {
        return [];
    }
    
    return [...props.page.sections].sort((a, b) => {
        return (a.order || 0) - (b.order || 0);
    });
});
</script>

<template>
    <Container class="page-renderer">
        <!-- Titre de la page -->
        <header class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-4xl font-bold text-primary">
                    {{ pageModel?.title || 'Page' }}
                </h1>
                <!-- Icône discrète pour modifier à côté du titre -->
                <!-- Forcer l'affichage temporairement pour debug -->
                <button
                    @click="handleOpenEditModal"
                    class="opacity-40 hover:opacity-100 transition-opacity p-2 rounded hover:bg-base-200 inline-flex items-center justify-center text-base-content"
                    title="Modifier la page"
                    type="button"
                    style="min-width: 32px; min-height: 32px;"
                >
                    <i class="fa-solid fa-edit text-sm"></i>
                </button>
            </div>
            <div v-if="pageModel?.createdByUser" class="text-sm text-base-content/70">
                Par {{ pageModel.createdByUser?.name || pageModel.createdByUser?.email }}
            </div>
        </header>

        <!-- Sections -->
        <div v-if="sortedSections.length > 0" class="sections space-y-8">
            <SectionRenderer
                v-for="section in sortedSections"
                :key="section.id"
                :section="section"
                :user="user"
            />
        </div>

        <!-- Message si aucune section -->
        <div v-else class="text-center py-12 text-base-content/50">
            <p>Aucune section disponible pour cette page.</p>
        </div>

        <!-- Modal d'édition -->
        <!-- Toujours monter le modal, même si pageData.id n'existe pas encore -->
        <EditPageModal
            :open="editModalOpen"
            :page="props.page"
            :pages="pages"
            @close="handleCloseEditModal"
            @deleted="handlePageDeleted"
        />
    </Container>
</template>

<style scoped lang="scss">
.page-renderer {
    max-width: 4xl;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.sections {
    > * {
        // Espacement entre les sections
        margin-bottom: 2rem;
        
        &:last-child {
            margin-bottom: 0;
        }
    }
}
</style>

