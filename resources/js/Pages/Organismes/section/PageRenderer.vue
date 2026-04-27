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
import { computed, ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import SectionRenderer from './SectionRenderer.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EditPageModal from './modals/EditPageModal.vue';
import CreateSectionModal from './modals/CreateSectionModal.vue';
import RulesPagePlan from './RulesPagePlan.vue';
import RulesBreadcrumbSticky from './RulesBreadcrumbSticky.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { Page } from '@/Models';
import { useDynamicMenu } from '@/Composables/layout/useDynamicMenu';

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
const activeSectionId = ref(null);
let sectionObserver = null;

// Utiliser le modèle Page pour normaliser l'accès aux données
const pageModel = computed(() => {
    if (!props.page) return null;
    return new Page(props.page);
});
const { menuItems } = useDynamicMenu();

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

const sectionHash = (section) => {
    if (!section) return '';
    const slug = String(section.slug || '').trim();
    if (slug) return `#ssec-${slug}`;
    const sid = section.id;
    return sid ? `#section-${sid}` : '';
};

const parseL4Headings = (section) => {
    const html = String(section?.data?.content || '').trim();
    if (!html || typeof DOMParser === 'undefined') return [];
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const used = new Set();
        return Array.from(doc.body.querySelectorAll('h4, h5, h6'))
            .map((el, idx) => {
                const text = String(el.textContent || '').trim();
                if (!text) return null;
                const base = text
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
                const local = base || `heading-${idx + 1}`;
                const prefix = String(section?.slug || section?.id || '').trim();
                const anchorPrefix = prefix ? `ssec-${prefix}` : `section-${String(section?.id || '')}`;
                let id = `${anchorPrefix}-${local}`;
                let n = 2;
                while (used.has(id)) {
                    id = `${anchorPrefix}-${local}-${n}`;
                    n += 1;
                }
                used.add(id);
                return {
                    id,
                    text,
                    hash: `#${id}`,
                };
            })
            .filter(Boolean);
    } catch {
        return [];
    }
};

const planSections = computed(() =>
    sortedSections.value.map((section, index) => ({
        id: section.id ?? `section-${index + 1}`,
        title: String(section?.title || '').trim() || `Section ${index + 1}`,
        hash: sectionHash(section),
        l4Headings: parseL4Headings(section),
    })),
);

const activeSectionTitle = computed(() => {
    const id = activeSectionId.value;
    if (!id) return planSections.value[0]?.title || '';
    return planSections.value.find((section) => section.id === id)?.title || planSections.value[0]?.title || '';
});

const flattenedMenuPages = computed(() => {
    const tree = Array.isArray(menuItems.value) ? menuItems.value : [];
    const currentUrl = String(pageModel.value?.url || '').trim();
    if (!tree.length || !currentUrl) return [];

    const normalize = (url) => String(url || '').replace(/\/+$/, '');
    const current = normalize(currentUrl);

    let siblingBranch = null;

    const visit = (items = []) => {
        if (!Array.isArray(items) || !items.length || siblingBranch) return;
        for (const item of items) {
            const children = Array.isArray(item?.children) ? item.children : [];
            if (children.length) {
                const matchedChild = children.some((child) => normalize(child?.url) === current);
                if (matchedChild) {
                    siblingBranch = children;
                    return;
                }
                visit(children);
                if (siblingBranch) return;
            }
        }
    };

    visit(tree);

    // Fallback: si page courante non trouvée comme enfant, garder la liste actuelle minimale.
    const branch = siblingBranch || [];
    return branch
        .filter((item) => item?.title && item?.url)
        .map((item) => ({ title: item.title, url: item.url }));
});

const l1Pages = computed(() => {
    const tree = Array.isArray(menuItems.value) ? menuItems.value : [];
    const currentUrl = String(pageModel.value?.url || '').trim();
    if (!tree.length || !currentUrl) return [];

    const normalize = (url) => String(url || '').replace(/\/+$/, '');
    const current = normalize(currentUrl);
    let groupChildren = [];

    const findGroupForCurrent = (groups = []) => {
        for (const group of groups) {
            const children = Array.isArray(group?.children) ? group.children : [];
            if (!children.length) continue;
            const hasCurrentInside = children.some((parentItem) => {
                if (normalize(parentItem?.url) === current) return true;
                const nested = Array.isArray(parentItem?.children) ? parentItem.children : [];
                return nested.some((child) => normalize(child?.url) === current);
            });
            if (hasCurrentInside) {
                groupChildren = children;
                return true;
            }
        }
        return false;
    };

    findGroupForCurrent(tree);

    return (groupChildren || [])
        .filter((item) => item?.title && item?.url)
        .map((item) => ({ title: item.title, url: item.url }));
});

const l1Title = computed(() => {
    const parentTitle = String(pageModel.value?.parent?.title || '').trim();
    if (parentTitle) return parentTitle;
    const menuGroup = String(pageModel.value?.menuGroup || '').trim();
    if (menuGroup) return menuGroup;
    return 'Règles';
});

const showRulesBreadcrumb = computed(() => pageModel.value?.showRulesBreadcrumb !== false);

const navigateToSection = (section) => {
    if (!section) return;
    const hash = section.hash || '';
    if (!hash) return;
    if (typeof window !== 'undefined') {
        window.location.hash = hash;
        const targetId = hash.replace(/^#/, '');
        requestAnimationFrame(() => {
            const el = document.getElementById(targetId);
            if (el && typeof el.scrollIntoView === 'function') {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
};

const navigateToPage = (item) => {
    const url = String(item?.url || '').trim();
    if (!url) return;
    router.visit(url, {
        preserveScroll: true,
    });
};

/**
 * Ancres `#section-{id}` (id numérique) ou `#ssec-{slug}` (slug section, règles / liens stables) : scroll une fois le DOM prêt.
 */
function scrollToSectionFromHash() {
    const raw = (typeof window !== 'undefined' ? window.location.hash : '').replace(/^#/, '');
    if (!raw) return;
    const isNumericSection = raw.startsWith('section-') && raw !== 'section-';
    const isSlugSection = raw.startsWith('ssec-') && raw.length > 'ssec-'.length;
    if (!isNumericSection && !isSlugSection) return;
    nextTick(() => {
        requestAnimationFrame(() => {
            const el = document.getElementById(raw);
            if (el && typeof el.scrollIntoView === 'function') {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

function onInertiaFinish() {
    scrollToSectionFromHash();
}

const setupSectionObserver = () => {
    if (typeof window === 'undefined' || typeof IntersectionObserver === 'undefined') return;
    if (sectionObserver) {
        sectionObserver.disconnect();
        sectionObserver = null;
    }
    const nodes = Array.from(document.querySelectorAll('[data-section-id]'));
    if (!nodes.length) return;

    sectionObserver = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio);
            if (!visible.length) return;
            const top = visible[0].target;
            const rawId = Number(top?.getAttribute('data-section-id'));
            if (!Number.isNaN(rawId) && rawId > 0) {
                activeSectionId.value = rawId;
            }
        },
        {
            root: null,
            threshold: [0.2, 0.45, 0.7],
            rootMargin: '-18% 0px -58% 0px',
        },
    );

    nodes.forEach((node) => sectionObserver.observe(node));
    const firstId = Number(nodes[0]?.getAttribute('data-section-id'));
    if (!Number.isNaN(firstId) && firstId > 0) {
        activeSectionId.value = firstId;
    }
};

onMounted(() => {
    scrollToSectionFromHash();
    setupSectionObserver();
    if (typeof document !== 'undefined') {
        document.addEventListener('inertia:finish', onInertiaFinish);
        window.addEventListener('hashchange', scrollToSectionFromHash);
    }
});

onBeforeUnmount(() => {
    if (sectionObserver) {
        sectionObserver.disconnect();
        sectionObserver = null;
    }
    if (typeof document !== 'undefined') {
        document.removeEventListener('inertia:finish', onInertiaFinish);
        window.removeEventListener('hashchange', scrollToSectionFromHash);
    }
});

watch(
    () => sortedSections.value.map((s) => s?.id).join(','),
    () => {
        scrollToSectionFromHash();
        nextTick(() => setupSectionObserver());
    },
);
</script>

<template>
    <main class="page-show-main">
        <Container
            class="page-renderer"
            :class="pageModel?.pageCssClasses"
            :allow-overflow="true"
        >
            <div class="rules-top-nav sticky top-2 z-40 mb-5 overflow-visible">
                <div class="rules-top-nav__surface flex flex-wrap items-center justify-between gap-2 md:gap-3">
                    <RulesBreadcrumbSticky
                        v-if="showRulesBreadcrumb"
                        class="min-w-0 flex-1"
                        :l1-title="l1Title"
                        :l1-pages="l1Pages"
                        :page-title="pageModel?.title || props.page?.title || 'Page'"
                        :active-section-title="activeSectionTitle"
                        :pages="flattenedMenuPages"
                        :sections="planSections"
                        @navigate:page="navigateToPage"
                        @navigate:section="navigateToSection"
                    />

                    <div class="flex items-center">
                        <Btn
                            v-if="canEdit"
                            @click="handleOpenEditModal"
                            variant="ghost"
                            size="xs"
                            title="Modifier les options de la page"
                            class="shrink-0"
                        >
                            <Icon source="fa-edit" pack="solid" alt="Modifier la page" size="xs" />
                        </Btn>
                    </div>
                </div>
            </div>

            <RulesPagePlan
                class="mb-8"
                :l1-title="l1Title"
                :page-title="pageModel?.title || props.page?.title || 'Page'"
                :sections="planSections"
            />

            <!-- Sections -->
            <div v-if="sortedSections.length > 0" class="sections space-y-8 md:space-y-10">
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
    max-width: 4xl;
    margin: 0 auto;
    padding: 0;
    overflow: visible;
}

.rules-top-nav__surface {
    border-radius: var(--radius-box);
    backdrop-filter: blur(18px) saturate(1.08);
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--color-slate-900) 10%, transparent) 0%,
        color-mix(in srgb, var(--color-slate-900) 5%, transparent) 100%
    );
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

