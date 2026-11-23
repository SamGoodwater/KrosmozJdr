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
 * - Fil d'Ariane et plan des sections (PageTopNav)
 * 
 * @props {Object} page - Données de la page (avec sections)
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * @props {Array} pages - Liste des pages disponibles (pour le modal d'édition)
 * 
 * @example
 * <PageRenderer :page="page" :user="user" :pages="pages" />
 */
import { computed, ref, watch } from 'vue';
import SectionLazyGate from './SectionLazyGate.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EditPageModal from './modals/EditPageModal.vue';
import CreateSectionModal from './modals/CreateSectionModal.vue';
import PageMenuChildrenIndex from './PageMenuChildrenIndex.vue';
import PageTopNav from './PageTopNav.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { Page } from '@/Models';
import {
    adjacentCellClass,
    adjacentCellStyle,
    groupSectionsIntoRows,
    pageSectionsUseFullWidth,
} from '@/Utils/section/sectionLayout';
import AdjacentSectionsRow from '@/Pages/Molecules/section/AdjacentSectionsRow.vue';
import { resolveSectionIdFromAnchor, usePageSectionChrome } from '@/Composables/section/usePageSectionChrome';

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
    },
    menuChildIndex: {
        type: Array,
        default: () => [],
    },
    /** Le parent (fiche classe / spécialisation) affiche déjà le bandeau. */
    hideTopNav: {
        type: Boolean,
        default: false,
    },
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

/** Toutes les sections prennent la largeur de la plus large (un tableau = la colonne). */
const sectionsShareFullWidth = computed(() => pageSectionsUseFullWidth(sortedSections.value));

/** Rangées : sections consécutives au même `layoutRowId` côte à côte. */
const sectionRows = computed(() => groupSectionsIntoRows(sortedSections.value));

/**
 * Index plat d’une section dans sortedSections (eager mount).
 * @param {object} section
 * @returns {number}
 */
function sectionFlatIndex(section) {
    return sortedSections.value.findIndex((s) => s?.id === section?.id);
}

/** IDs de sections à monter immédiatement (ancre URL, plan, édition). */
const forceMountSectionIds = ref(new Set());

/**
 * Résout l’id section depuis une ancre `#section-{id}` ou `#ssec-{slug}…`.
 *
 * @param {string} targetId
 * @returns {number|null}
 */
function sectionIdFromHash(targetId) {
    return resolveSectionIdFromAnchor(targetId, sortedSections.value);
}

function collectHashEagerSectionIds() {
    if (typeof window === "undefined") {
        return new Set();
    }
    const raw = window.location.hash.replace(/^#/, "").trim();
    if (!raw) {
        return new Set();
    }
    const id = sectionIdFromHash(raw);
    return id ? new Set([id]) : new Set();
}

/**
 * Premières sections + ancre + édition auto : montage eager (le reste via IntersectionObserver).
 *
 * @param {Object} section
 * @param {number} index
 * @returns {boolean}
 */
function isSectionEager(section, index) {
    const id = Number(section?.id);
    if (Number.isNaN(id) || id <= 0) {
        return index < 2;
    }
    if (index < 2) {
        return true;
    }
    if (sectionToEdit.value === id) {
        return true;
    }
    if (forceMountSectionIds.value.has(id)) {
        return true;
    }
    return collectHashEagerSectionIds().has(id);
}

function requestSectionMount(sectionOrId) {
    const id =
        typeof sectionOrId === "object"
            ? Number(sectionOrId?.id)
            : Number(sectionOrId);
    if (Number.isNaN(id) || id <= 0) {
        return;
    }
    const next = new Set(forceMountSectionIds.value);
    next.add(id);
    forceMountSectionIds.value = next;
}

const {
    planSections,
    activeSectionTitle,
    pageTitle,
    flattenedMenuPages,
    l1Pages,
    l1Title,
    parentPageBreadcrumb,
    currentPageBreadcrumb,
    showBreadcrumb,
    navigateToSection,
    navigateToPage,
} = usePageSectionChrome(pageModel, sortedSections, {
    prepareSection: requestSectionMount,
    enabled: computed(() => !props.hideTopNav),
});

const menuChildIndexItems = computed(() =>
    Array.isArray(props.menuChildIndex) ? props.menuChildIndex : [],
);


watch(sectionToEdit, (id) => {
    if (id) {
        requestSectionMount(id);
    }
});
</script>

<template>
    <main class="page-show-main">
        <Container
            fluid
            class="page-renderer w-full min-w-0"
            :class="pageModel?.pageCssClasses"
            :allow-overflow="true"
        >
            <PageTopNav
                v-if="!hideTopNav"
                :show-breadcrumb="showBreadcrumb"
                :l1-title="l1Title"
                :l1-pages="l1Pages"
                :page-title="pageTitle"
                :page-url="currentPageBreadcrumb?.url || ''"
                :parent-page="parentPageBreadcrumb"
                :active-section-title="activeSectionTitle"
                :pages="flattenedMenuPages"
                :sections="planSections"
                @navigate:page="navigateToPage"
                @navigate:section="navigateToSection"
            />

            <PageMenuChildrenIndex
                v-if="menuChildIndexItems.length > 0"
                :items="menuChildIndexItems"
            />

            <!-- Sections (rangées éventuelles côte à côte) -->
            <div v-if="sectionRows.length > 0" class="sections">
            <template v-for="row in sectionRows" :key="row.key">
                <AdjacentSectionsRow
                    v-if="row.items.length > 1"
                    class="sections__adjacent-row w-full min-w-0"
                >
                    <div
                        v-for="(item, itemIdx) in row.items"
                        :key="item.section.id ?? `${row.key}-${itemIdx}`"
                        :class="adjacentCellClass(item.layoutWidth)"
                        :style="adjacentCellStyle(item.layoutWidth)"
                    >
                        <SectionLazyGate
                            :section="item.section"
                            :user="user"
                            :auto-edit="sectionToEdit === item.section.id"
                            :eager="isSectionEager(item.section, sectionFlatIndex(item.section))"
                            :force-full-width="sectionsShareFullWidth"
                            :in-adjacent-row="true"
                        />
                    </div>
                </AdjacentSectionsRow>
                <SectionLazyGate
                    v-else
                    :section="row.items[0].section"
                    :user="user"
                    :auto-edit="sectionToEdit === row.items[0].section.id"
                    :eager="isSectionEager(row.items[0].section, sectionFlatIndex(row.items[0].section))"
                    :force-full-width="sectionsShareFullWidth"
                />
            </template>
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
    </main>
</template>

<style scoped lang="scss">
.page-show-main {
    min-height: 40vh;
    padding: 1.5rem 1rem 2.5rem;
    @media (min-width: 768px) {
        padding: 2rem 1.5rem 3rem;
    }
    background: linear-gradient(
        185deg,
        color-mix(in oklch, hsl(var(--b3)) 35%, transparent) 0%,
        transparent 42%
    );
}

.page-renderer {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: 0 auto;
    padding: 0;
    overflow: visible;
}

.sections {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    max-width: 100%;
    min-width: 0;

    > * {
        min-width: 0;
        max-width: 100%;
        margin-bottom: 2rem;

        &:last-child {
            margin-bottom: 0;
        }
    }

    :deep(.sections__adjacent-row > *) {
        display: flex;
        flex-direction: column;
    }

    :deep(.sections__adjacent-row .section-renderer-surface),
    :deep(.sections__adjacent-row .section-lazy-gate) {
        margin-bottom: 0;
        height: 100%;
    }
}
</style>

