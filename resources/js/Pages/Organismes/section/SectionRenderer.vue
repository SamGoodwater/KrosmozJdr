<script setup>
/**
 * SectionRenderer Organism
 * 
 * @description
 * Composant organisme pour rendre dynamiquement une section selon son type.
 * - Gère le header réutilisable (SectionHeader)
 * - Bascule entre mode lecture et écriture
 * - Charge dynamiquement les templates read/edit
 * - Gère les actions : copier lien, basculer mode, paramètres
 * 
 * @props {Object} section - Données de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionRenderer :section="section" :user="user" />
 */
import { computed, ref, watch, shallowRef, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import SectionHeader from '@/Pages/Molecules/section/SectionHeader.vue';
import SectionParamsModal from './modals/SectionParamsModal.vue';
import { useSectionMode } from './composables/useSectionMode';
import { useSectionSave } from './composables/useSectionSave';
import { useSectionTemplates } from './composables/useSectionTemplates';
import { useSectionAPI } from './composables/useSectionAPI';
import { useSectionUI } from './composables/useSectionUI';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';

const props = defineProps({
    section: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value === 'object' && 'id' in value && ('template' in value || 'type' in value);
        }
    },
    user: {
        type: Object,
        default: null
    },
    autoEdit: {
        type: Boolean,
        default: false
    }
});

// Utiliser le composable UI unifié
const { 
    sectionModel, 
    canEdit, 
    canDelete,
    templateInfo,
    stateInfo,
    uiData 
} = useSectionUI(() => props.section);

// États
const isHovered = ref(false);
const paramsModalOpen = ref(false);
const templateComponent = shallowRef(null);
const isLoadingTemplate = ref(false);

// Composables
// Utiliser directement props.section.id car le validator garantit qu'il existe
// On peut aussi utiliser sectionModel.value?.id comme fallback
const sectionId = computed(() => {
  // Priorité à props.section.id (toujours disponible grâce au validator)
  return props.section?.id || sectionModel.value?.id;
});
const { isEditing, toggleEditMode, setEditMode } = useSectionMode(sectionId);
const { saveSectionImmediate } = useSectionSave();
const { getTemplateComponent } = useSectionTemplates();
const { updateSection } = useSectionAPI();
const { copyToClipboard } = useCopyToClipboard();

// Activer automatiquement le mode édition si autoEdit est true
watch(() => props.autoEdit, (shouldEdit) => {
    if (shouldEdit && sectionId.value && canEdit.value) {
        setEditMode(true);
    }
}, { immediate: true });

/**
 * Template de la section (utilise templateInfo du composable UI)
 */
const templateValue = computed(() => {
  return templateInfo.value.value;
});

/**
 * Données de la section
 */
const sectionData = computed(() => {
  return props.section.data || {};
});

/**
 * Paramètres de la section
 */
const sectionSettings = computed(() => {
  return props.section.settings || {};
});

/**
 * Charge le composant template selon le mode
 */
const loadTemplateComponent = async () => {
  if (!templateValue.value) return;
  
  isLoadingTemplate.value = true;
  try {
    const mode = isEditing.value ? 'edit' : 'read';
    const component = await getTemplateComponent(templateValue.value, mode);
    templateComponent.value = component;
  } catch (error) {
    console.error('Erreur lors du chargement du template:', error);
    templateComponent.value = null;
  } finally {
    isLoadingTemplate.value = false;
  }
};

// Charger le template initial
loadTemplateComponent();

// Recharger le template quand le mode change
watch(isEditing, (newValue, oldValue) => {
  // S'assurer que le mode a vraiment changé
  if (newValue !== oldValue) {
    loadTemplateComponent();
  }
}, { immediate: false, flush: 'sync' });

// Également écouter les changements de sectionId pour recharger si nécessaire
watch(sectionId, () => {
  loadTemplateComponent();
}, { immediate: false });

// Recharger le template si le type change
watch(templateValue, (newValue, oldValue) => {
  // S'assurer que le type a vraiment changé
  if (newValue !== oldValue) {
    loadTemplateComponent();
  }
}, { immediate: false });

/**
 * Gère le basculement du mode édition avec rechargement forcé
 */
const handleToggleEdit = async () => {
  toggleEditMode();
  // Attendre que Vue ait mis à jour la réactivité
  await nextTick();
  // Forcer le rechargement du template
  loadTemplateComponent();
};

/**
 * Gère la mise à jour du titre
 */
const handleTitleUpdate = (newTitle) => {
    const id = sectionId.value;
    
    if (!id) {
        console.error('SectionRenderer: Impossible de mettre à jour le titre, sectionId manquant', { 
            sectionId: id,
            sectionModel: sectionModel.value,
            propsSection: props.section,
            newTitle 
        });
        return;
    }
  
    saveSectionImmediate(id, {
        title: newTitle
    });
};

/**
 * Gère la copie du lien de la section
 */
const handleCopyLink = async () => {
  if (!sectionModel.value || !sectionModel.value.page) return;
  
  const pageSlug = sectionModel.value.page.slug || sectionModel.value.pageId;
  const sectionSlug = sectionModel.value.slug || sectionModel.value.id;
  const url = `${window.location.origin}${route('pages.show', pageSlug)}#${sectionSlug}`;
  
  await copyToClipboard(url, 'Lien de la section copié !');
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
 * 
 * @param {Object} updatedParams - Paramètres mis à jour (title, slug, order, is_visible, can_edit_role, state, settings)
 */
const handleParamsUpdated = async (updatedParams) => {
    const id = sectionId.value; // Utiliser le computed sectionId (avec fallback)
    
    if (!id) {
        console.error('SectionRenderer: Impossible de mettre à jour la section, ID manquant', { 
            sectionId: id,
            sectionModel: sectionModel.value,
            sectionModelId: sectionModel.value?.id,
            propsSection: props.section,
            propsSectionId: props.section?.id
        });
        return;
    }
    
    try {
        await updateSection(id, updatedParams, {
            onSuccess: () => {
                paramsModalOpen.value = false;
                router.reload({ only: ['page'] });
            }
        });
    } catch (errors) {
        console.error('Erreur lors de la mise à jour de la section:', errors);
    }
};

/**
 * Gère la mise à jour des données depuis le template
 */
const handleDataUpdate = (newData) => {
  // Les templates compatibles auto-save gèrent déjà la sauvegarde
  // Cette fonction est appelée pour informer le parent si nécessaire
};
</script>

<template>
    <div 
        class="section-renderer group relative" 
        :class="uiData.containerClass"
        :data-section-id="sectionModel?.id" 
        :data-section-template="templateValue"
        :data-section-state="stateInfo.value"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
    <!-- Header toujours visible -->
    <SectionHeader
      :title="section.title || sectionModel?.title"
      :isEditing="isEditing"
      :canEdit="canEdit"
      :isHovered="isHovered"
      @update:title="handleTitleUpdate"
      @toggle-edit="handleToggleEdit"
      @open-params="handleOpenParamsModal"
      @copy-link="handleCopyLink"
    />
    
    <!-- Contenu selon le mode -->
    <div v-if="isLoadingTemplate" class="section-loading">
      <span class="loading loading-spinner"></span>
      <p class="mt-2 text-sm text-base-content/70">Chargement...</p>
        </div>

        <component
      v-else-if="templateComponent"
            :is="templateComponent"
            :section="section"
      :data="sectionData"
      :settings="sectionSettings"
      :editing="isEditing"
      @data-updated="handleDataUpdate"
        />

    <!-- Erreur si le template n'existe pas -->
        <div v-else class="section-error alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
        <h3 class="font-bold">Template non trouvé</h3>
                <p class="text-sm">
          Le template "{{ templateValue }}" n'est pas disponible.
                    Veuillez contacter un administrateur.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal de paramètres -->
    <SectionParamsModal
        v-if="sectionModel"
        :open="paramsModalOpen"
        :section-template="templateValue"
        :section="sectionModel"
        @close="handleCloseParamsModal"
        @validated="handleParamsUpdated"
        @deleted="() => router.reload({ only: ['page'] })"
    />
</template>

<style scoped lang="scss">
.section-renderer {
    position: relative;
  margin-bottom: 2rem;
  
  &:last-child {
    margin-bottom: 0;
  }
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
