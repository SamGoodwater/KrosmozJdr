<script setup>
/**
 * Page Index Component
 * 
 * @description
 * Page de liste des pages dynamiques avec modal de création
 * 
 * @props {Object} pages - Collection paginée des pages
 * @props {Array} allPages - Liste de toutes les pages (pour le select parent_id dans le modal)
 */
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import CreatePageModal from '@/Pages/Organismes/section/CreatePageModal.vue';
import EditPageModal from '@/Pages/Organismes/section/EditPageModal.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

const props = defineProps({
    pages: {
        type: Object,
        required: true
    },
    allPages: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des pages');

const page = usePage();
const modalOpen = ref(false);
const editModalOpen = ref(false);
const selectedPage = ref(null);

// Vérifier si l'utilisateur peut créer des pages (admin ou super_admin selon PagePolicy)
const canCreate = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return false;
    // Les admins et super_admin peuvent créer des pages
    return user.role === 4 || user.role === 5; // 4 = admin, 5 = super_admin
});

const handleCreate = () => {
    modalOpen.value = true;
};

const handleCloseModal = () => {
    modalOpen.value = false;
};

const handleEdit = (pageItem) => {
    selectedPage.value = pageItem;
    editModalOpen.value = true;
};

const handleCloseEditModal = () => {
    editModalOpen.value = false;
    selectedPage.value = null;
};

const handlePageDeleted = () => {
    editModalOpen.value = false;
    selectedPage.value = null;
};

const handleView = (pageSlug) => {
    router.visit(route('pages.show', pageSlug));
};

const handleDelete = (pageId, pageTitle) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la page "${pageTitle}" ?`)) {
        router.delete(route('pages.delete', pageId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['pages'] });
            }
        });
    }
};

// Formatage de l'état
const formatState = (state) => {
    const states = {
        'draft': { label: 'Brouillon', color: 'badge-ghost' },
        'preview': { label: 'Prévisualisation', color: 'badge-info' },
        'published': { label: 'Publié', color: 'badge-success' },
        'archived': { label: 'Archivé', color: 'badge-warning' }
    };
    return states[state] || { label: state, color: 'badge-ghost' };
};

// Formatage de la visibilité
const formatVisibility = (visibility) => {
    const visibilities = {
        'guest': 'Invité',
        'user': 'Utilisateur',
        'game_master': 'Maître de jeu',
        'admin': 'Administrateur'
    };
    return visibilities[visibility] || visibility;
};
</script>

<template>
    <Head title="Liste des pages" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des pages</h1>
                <p class="text-primary-200 mt-2">Gérez les pages dynamiques du site</p>
            </div>
            <Btn 
                v-if="canCreate"
                @click="handleCreate" 
                variant="primary"
            >
                <Icon source="fa-solid fa-plus" class="mr-2" />
                Créer une page
            </Btn>
        </div>

        <!-- Tableau des pages -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div v-if="pages.data && pages.data.length > 0" class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Slug</th>
                                <th>État</th>
                                <th>Visibilité</th>
                                <th>Dans le menu</th>
                                <th>Ordre</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pageItem in pages.data" :key="pageItem.id">
                                <td>{{ pageItem.id }}</td>
                                <td>
                                    <Route 
                                        :href="route('pages.show', pageItem.slug)"
                                        class="link link-primary"
                                    >
                                        {{ pageItem.title }}
                                    </Route>
                                </td>
                                <td>
                                    <code class="text-xs bg-base-200 px-2 py-1 rounded">{{ pageItem.slug }}</code>
                                </td>
                                <td>
                                    <span 
                                        :class="['badge', formatState(pageItem.state).color]"
                                    >
                                        {{ formatState(pageItem.state).label }}
                                    </span>
                                </td>
                                <td>{{ formatVisibility(pageItem.is_visible) }}</td>
                                <td>
                                    <span v-if="pageItem.in_menu" class="badge badge-success">Oui</span>
                                    <span v-else class="badge badge-ghost">Non</span>
                                </td>
                                <td>{{ pageItem.menu_order }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <Btn
                                            size="xs"
                                            variant="ghost"
                                            @click="handleView(pageItem.slug)"
                                            title="Voir la page"
                                        >
                                            <Icon source="fa-solid fa-eye" />
                                        </Btn>
                                        <Btn
                                            v-if="pageItem.can?.update"
                                            size="xs"
                                            variant="info"
                                            @click="handleEdit(pageItem)"
                                            title="Modifier"
                                        >
                                            <Icon source="fa-solid fa-edit" />
                                        </Btn>
                                        <Btn
                                            v-if="pageItem.can?.delete"
                                            size="xs"
                                            variant="error"
                                            @click="handleDelete(pageItem.id, pageItem.title)"
                                            title="Supprimer"
                                        >
                                            <Icon source="fa-solid fa-trash-can" />
                                        </Btn>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-8">
                    <p class="text-gray-500">Aucune page trouvée.</p>
                    <Btn 
                        v-if="canCreate"
                        @click="handleCreate" 
                        variant="primary"
                        class="mt-4"
                    >
                        <Icon source="fa-solid fa-plus" class="mr-2" />
                        Créer la première page
                    </Btn>
                </div>

                <!-- Pagination -->
                <div v-if="pages.links && pages.links.length > 3" class="flex justify-center mt-4">
                    <div class="join">
                        <template v-for="(link, index) in pages.links" :key="index">
                            <button
                                v-if="link.url"
                                :class="['join-item btn', link.active ? 'btn-active' : 'btn-ghost']"
                                v-html="link.label"
                                @click="router.visit(link.url)"
                            ></button>
                            <span
                                v-else
                                :class="['join-item btn btn-disabled']"
                                v-html="link.label"
                            ></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de création -->
        <CreatePageModal
            :open="modalOpen"
            :pages="allPages"
            @close="handleCloseModal"
        />

        <!-- Modal d'édition -->
        <EditPageModal
            v-if="selectedPage"
            :open="editModalOpen"
            :page="selectedPage"
            :pages="allPages"
            @close="handleCloseEditModal"
            @deleted="handlePageDeleted"
        />
    </Container>
</template>

<style scoped lang="scss">
// Styles spécifiques si nécessaire
</style>
