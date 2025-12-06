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
import { router } from '@inertiajs/vue3';
import SectionRenderer from './SectionRenderer.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EditPageModal from './modals/EditPageModal.vue';
import CreateSectionModal from './modals/CreateSectionModal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { Page } from '@/Models';

const props = defineProps({
    page: {
        type: Object,
        required: true,
        validator: (value) => {
            if (!value || typeof value !== 'object') return false;
            // Accepter les objets Inertia (peuvent avoir des ComputedRefImpl)
            // Accepter aussi les objets directs avec id ou title
            try {
                const pageData = value?.data || value;
                // Si c'est un ComputedRef, on accepte (sera résolu dans le computed)
                if (pageData && typeof pageData === 'object') {
                    return true;
                }
                return false;
            } catch {
                // Si l'accès échoue, on accepte quand même (peut être un ComputedRef)
                return true;
            }
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

// Modals
const editModalOpen = ref(false);
const createSectionModalOpen = ref(false);

// Utiliser le modèle Page pour normaliser l'accès aux données
const pageModel = computed(() => {
    if (!props.page) return null;
    return new Page(props.page);
});

// Vérifier si l'utilisateur peut modifier la page
const canEdit = computed(() => {
    if (!props.page) return false;
    
    // Vérifier directement depuis props.page (plus fiable)
    // Le PageResource inclut 'can' => ['update' => ...]
    // Mais les données peuvent être dans props.page.data si c'est une Resource
    const pageData = props.page?.data || props.page;
    let canUpdate = pageData?.can?.update || props.page?.can?.update || false;
    
    // Fallback sur le modèle si nécessaire
    if (!canUpdate && pageModel.value) {
        canUpdate = pageModel.value.canUpdate || false;
    }
    
    // Fallback final : vérifier le rôle utilisateur directement
    // Si l'utilisateur est admin ou super_admin, il peut toujours modifier
    if (!canUpdate && props.user) {
        const userRole = props.user.role || props.user.role_name;
        // Rôles admin : 4 = admin, 5 = super_admin
        const adminRoles = [4, 5, 'admin', 'super_admin'];
        if (adminRoles.includes(userRole)) {
            canUpdate = true;
        }
    }
    
    // Debug en développement
    if (import.meta.env.DEV) {
        console.log('PageRenderer - canEdit:', {
            canUpdate,
            pageData: pageData?.can,
            propsPageCan: props.page?.can,
            userRole: props.user?.role,
            pageModelCan: pageModel.value?.canUpdate
        });
    }
    
    return canUpdate;
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

const handleOpenCreateSectionModal = () => {
    createSectionModalOpen.value = true;
};

const handleCloseCreateSectionModal = () => {
    createSectionModalOpen.value = false;
};

const handleSectionCreated = (data) => {
    createSectionModalOpen.value = false;
    
    // Si la section doit être ouverte en mode édition, on recharge la page
    // Sinon, on recharge simplement pour afficher la nouvelle section
    if (data?.openEdit) {
        // Recharger la page pour afficher la section en mode édition
        router.reload({ only: ['page'] });
    } else {
        // Recharger la page pour afficher la nouvelle section
        router.reload({ only: ['page'] });
    }
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
                    {{ pageModel?.title || props.page?.title || 'Page' }}
                </h1>
                <!-- Bouton pour modifier la page à côté du titre (seulement si droits d'écriture) -->
                <Btn
                    v-if="canEdit"
                    @click="handleOpenEditModal"
                    variant="ghost"
                    size="sm"
                    title="Modifier les options de la page"
                    class="ml-2"
                >
                    <Icon source="fa-edit" pack="solid" alt="Modifier la page" size="sm" />
                </Btn>
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
            <Btn 
                v-if="canEdit" 
                @click="handleOpenCreateSectionModal" 
                color="primary"
                class="mt-4"
            >
                <Icon source="fa-plus" pack="solid" alt="Ajouter" class="mr-2" />
                Ajouter une section
            </Btn>
        </div>

        <!-- Bouton d'ajout de section (visible si sections existent, en mode glass, carré, à droite) -->
        <div v-if="sortedSections.length > 0 && canEdit" class="flex justify-end mt-8">
            <Btn
                @click="handleOpenCreateSectionModal"
                variant="glass"
                size="lg"
                square
                title="Ajouter une section"
            >
                <Icon source="fa-plus" pack="solid" alt="Ajouter une section" />
            </Btn>
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

        <!-- Modal de création de section -->
        <CreateSectionModal
            v-if="pageModel?.id"
            :open="createSectionModalOpen"
            :page-id="pageModel.id"
            @close="handleCloseCreateSectionModal"
            @created="handleSectionCreated"
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

