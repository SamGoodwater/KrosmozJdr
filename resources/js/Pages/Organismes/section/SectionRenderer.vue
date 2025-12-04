<script setup>
/**
 * SectionRenderer Organism
 * 
 * @description
 * Composant organisme pour rendre dynamiquement une section selon son type.
 * - Route vers le bon template de section selon le type
 * - Passe les params de la section au template
 * - Gère les erreurs si le type n'existe pas
 * 
 * @props {Object} section - Données de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionRenderer :section="section" :user="user" />
 */
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import SectionText from './templates/SectionText.vue';
import SectionImage from './templates/SectionImage.vue';
import SectionGallery from './templates/SectionGallery.vue';
import SectionVideo from './templates/SectionVideo.vue';
import SectionEntityTable from './templates/SectionEntityTable.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { Section } from '@/Models';
import SectionParamsModal from './SectionParamsModal.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';

const props = defineProps({
    section: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value === 'object' && 'id' in value && 'type' in value;
        }
    },
    user: {
        type: Object,
        default: null
    }
});

/**
 * Composant template à charger selon le type
 */
const templateComponent = computed(() => {
    const type = props.section.type;
    
    // Mapping des types vers les composants
    const typeMap = {
        'text': SectionText,
        'image': SectionImage,
        'gallery': SectionGallery,
        'video': SectionVideo,
        'entity_table': SectionEntityTable
    };
    
    return typeMap[type] || null;
});

/**
 * Params de la section (avec valeurs par défaut)
 */
const sectionParams = computed(() => {
    return props.section.params || {};
});

/**
 * Modèle Section normalisé
 */
const sectionModel = computed(() => {
    if (!props.section) return null;
    return new Section(props.section);
});

/**
 * Vérifie si l'utilisateur peut modifier la section
 */
const canEdit = computed(() => {
    if (!sectionModel.value) return false;
    return sectionModel.value.canUpdate;
});

// États pour les modals et hover
const isHovered = ref(false);
const paramsModalOpen = ref(false);

/**
 * Gère l'ouverture du modal d'édition
 */
const handleEdit = () => {
    if (!sectionModel.value) return;
    router.visit(route('sections.edit', sectionModel.value.id));
};

/**
 * Gère l'ouverture du modal de paramètres
 */
const handleOpenParamsModal = () => {
    paramsModalOpen.value = true;
};

/**
 * Gère la fermeture du modal de paramètres
 */
const handleCloseParamsModal = () => {
    paramsModalOpen.value = false;
};

/**
 * Gère la mise à jour des paramètres
 */
const handleParamsUpdated = (updatedParams) => {
    if (!sectionModel.value) return;
    
    // Mettre à jour la section via API
    router.patch(route('sections.update', sectionModel.value.id), {
        params: updatedParams
    }, {
        preserveScroll: true,
        onSuccess: () => {
            paramsModalOpen.value = false;
            // Recharger la page pour afficher les changements
            router.reload({ only: ['page'] });
        },
        onError: (errors) => {
            console.error('Erreur lors de la mise à jour de la section:', errors);
        }
    });
};

/**
 * Gère la copie du lien de la section
 */
const { copyToClipboard } = useCopyToClipboard();

const handleCopyLink = async () => {
    if (!sectionModel.value || !sectionModel.value.page) return;
    
    const pageSlug = sectionModel.value.page.slug || sectionModel.value.pageId;
    const sectionId = sectionModel.value.id;
    const url = `${window.location.origin}${route('pages.show', pageSlug)}#section-${sectionId}`;
    
    await copyToClipboard(url, 'Lien de la section copié !');
};
</script>

<template>
    <div 
        class="section-renderer group relative" 
        :data-section-id="section.id" 
        :data-section-type="section.type"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Icônes d'action (visibles au hover) -->
        <div 
            v-if="canEdit && isHovered" 
            class="absolute top-2 right-2 flex gap-2 z-10"
        >
            <!-- Icône de modification -->
            <button
                @click="handleEdit"
                class="p-2 rounded-lg bg-base-100/90 backdrop-blur-sm border border-base-300 hover:bg-base-200 transition-all shadow-lg"
                title="Modifier la section"
                type="button"
            >
                <Icon source="fa-solid fa-edit" size="sm" />
            </button>
            
            <!-- Icône de paramétrage -->
            <button
                @click="handleOpenParamsModal"
                class="p-2 rounded-lg bg-base-100/90 backdrop-blur-sm border border-base-300 hover:bg-base-200 transition-all shadow-lg"
                title="Paramètres de la section"
                type="button"
            >
                <Icon source="fa-solid fa-gear" size="sm" />
            </button>
        </div>

        <!-- Icône de copie de lien (en haut à gauche, visible au hover) -->
        <button
            v-if="isHovered"
            @click="handleCopyLink"
            class="absolute top-2 left-2 p-2 rounded-lg bg-base-100/90 backdrop-blur-sm border border-base-300 hover:bg-base-200 transition-all shadow-lg z-10"
            title="Copier le lien de la section"
            type="button"
        >
            <Icon source="fa-solid fa-link" size="sm" />
        </button>

        <!-- Rendu dynamique du template -->
        <component
            v-if="templateComponent"
            :is="templateComponent"
            :params="sectionParams"
            :section="section"
            :user="user"
        />

        <!-- Erreur si le type n'existe pas -->
        <div v-else class="section-error alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <h3 class="font-bold">Type de section inconnu</h3>
                <p class="text-sm">
                    Le type "{{ section.type }}" n'est pas reconnu.
                    Veuillez contacter un administrateur.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal de paramètres -->
    <SectionParamsModal
        v-if="sectionModel"
        :open="paramsModalOpen"
        :section-type="sectionModel.type"
        :initial-params="sectionModel.params"
        @close="handleCloseParamsModal"
        @validated="handleParamsUpdated"
    />
</template>

<style scoped lang="scss">
.section-renderer {
    position: relative;
}

.section-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    min-height: 100px;
}

.section-error {
    margin: 1rem 0;
}
</style>

