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
import { computed, ref, watch } from 'vue';
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

// Section à ouvrir en mode édition après création
const sectionToEdit = ref(null);
const pendingSectionTemplate = ref(null); // Template de la section en attente

// Utiliser le modèle Page pour normaliser l'accès aux données
const pageModel = computed(() => {
    if (!props.page) return null;
    return new Page(props.page);
});

// Sections disponibles
const sections = computed(() => {
    return pageModel.value?.sections || props.page?.sections || [];
});

// Watcher pour détecter quand une nouvelle section est ajoutée
// On surveille props.page directement car c'est ce qui change après la redirection Inertia
watch(() => props.page?.sections, (newSections, oldSections) => {
    // Si on attend une section avec un template spécifique
    if (pendingSectionTemplate.value && newSections && Array.isArray(newSections)) {
        const oldLength = oldSections?.length || 0;
        const newLength = newSections.length;
        
        // Si le nombre de sections a augmenté OU si on n'avait pas de sections avant
        if (newLength > oldLength || (oldLength === 0 && newLength > 0)) {
            // Trouver la nouvelle section avec le bon template
            const newSection = newSections
                .filter(s => s.template === pendingSectionTemplate.value)
                .sort((a, b) => {
                    // Trier par ID décroissant (le plus récent en premier)
                    return (b.id || 0) - (a.id || 0);
                })[0];
            
            if (newSection?.id) {
                sectionToEdit.value = newSection.id;
                pendingSectionTemplate.value = null; // Réinitialiser
                
                // Réinitialiser après un court délai pour permettre le rendu
                setTimeout(() => {
                    sectionToEdit.value = null;
                }, 1000);
            } else {
                console.warn('PageRenderer - No section found with template:', pendingSectionTemplate.value);
            }
        }
    }
}, { deep: true, immediate: false });

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
    
    // Le backend redirige déjà vers la page, donc les données seront rechargées
    // Si on a un sectionId, on l'utilise directement
    if (data?.openEdit && data?.sectionId) {
        sectionToEdit.value = data.sectionId;
        
        // Réinitialiser après un court délai pour permettre le rendu
        setTimeout(() => {
            sectionToEdit.value = null;
        }, 1000);
    } else if (data?.openEdit && data?.template) {
        // Si pas d'ID mais un template, on attend que les sections soient mises à jour
        // Le watcher sur `props.page.sections` détectera la nouvelle section
        pendingSectionTemplate.value = data.template;
        
        // Vérifier immédiatement si les sections sont déjà disponibles
        const currentSections = sections.value;
        
        if (currentSections.length > 0) {
            const newSection = currentSections
                .filter(s => s.template === data.template)
                .sort((a, b) => {
                    return (b.id || 0) - (a.id || 0);
                })[0];
            
            if (newSection?.id) {
                sectionToEdit.value = newSection.id;
                pendingSectionTemplate.value = null;
                
                setTimeout(() => {
                    sectionToEdit.value = null;
                }, 1000);
            }
        }
        
        // Fallback : vérifier périodiquement si les sections sont disponibles
        // (au cas où le watcher ne se déclenche pas)
        let attempts = 0;
        const maxAttempts = 20; // 2 secondes max (20 * 100ms)
        const checkInterval = setInterval(() => {
            attempts++;
            const currentSections = sections.value;
            
            if (currentSections.length > 0) {
                const newSection = currentSections
                    .filter(s => s.template === data.template)
                    .sort((a, b) => {
                        return (b.id || 0) - (a.id || 0);
                    })[0];
                
                if (newSection?.id) {
                    sectionToEdit.value = newSection.id;
                    pendingSectionTemplate.value = null;
                    clearInterval(checkInterval);
                    
                    setTimeout(() => {
                        sectionToEdit.value = null;
                    }, 1000);
                } else if (attempts >= maxAttempts) {
                    pendingSectionTemplate.value = null;
                    clearInterval(checkInterval);
                }
            } else if (attempts >= maxAttempts) {
                pendingSectionTemplate.value = null;
                clearInterval(checkInterval);
            }
        }, 100);
    }
};

/**
 * Sections triées par ordre
 */
const sortedSections = computed(() => {
    // Extraire les sections depuis props.page ou pageModel
    // IMPORTANT: Utiliser props.page.sections directement car pageModel.sections peut ne pas avoir les permissions can
    const sections = props.page?.sections || pageModel.value?.sections || [];
    
    if (!Array.isArray(sections) || sections.length === 0) {
        return [];
    }
    
    return [...sections].sort((a, b) => {
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
                :auto-edit="sectionToEdit === section.id"
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

