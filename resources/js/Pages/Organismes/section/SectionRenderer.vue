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
import { computed, ref, watch, shallowRef } from 'vue';
import { router } from '@inertiajs/vue3';
import SectionHeader from '@/Pages/Molecules/section/SectionHeader.vue';
import SectionParamsModal from './modals/SectionParamsModal.vue';
import { Section } from '@/Models';
import { useSectionMode } from './composables/useSectionMode';
import { useSectionSave } from './composables/useSectionSave';
import { useSectionTemplates } from './composables/useSectionTemplates';
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
    }
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

// États
const isHovered = ref(false);
const paramsModalOpen = ref(false);
const templateComponent = shallowRef(null);
const isLoadingTemplate = ref(false);

// Composables
const sectionId = computed(() => sectionModel.value?.id);
const { isEditing, toggleEditMode } = useSectionMode(sectionId);
const { saveSectionImmediate } = useSectionSave();
const { getTemplateComponent } = useSectionTemplates();
const { copyToClipboard } = useCopyToClipboard();

/**
 * Template de la section
 */
const templateValue = computed(() => {
  return props.section.template || props.section.type || 'text';
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
watch(isEditing, () => {
  loadTemplateComponent();
});

// Recharger le template si le type change
watch(templateValue, () => {
  loadTemplateComponent();
});

/**
 * Gère la mise à jour du titre
 */
const handleTitleUpdate = (newTitle) => {
    if (!sectionModel.value) return;
  
  saveSectionImmediate(sectionModel.value.id, {
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
 */
const handleParamsUpdated = (updatedParams) => {
    if (!sectionModel.value) return;
    
  // Mettre à jour la section
    router.patch(route('sections.update', sectionModel.value.id), {
    ...updatedParams
    }, {
        preserveScroll: true,
        onSuccess: () => {
            paramsModalOpen.value = false;
            router.reload({ only: ['page'] });
        },
        onError: (errors) => {
            console.error('Erreur lors de la mise à jour de la section:', errors);
        }
    });
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
        :data-section-id="section.id" 
    :data-section-template="templateValue"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
    <!-- Header toujours visible -->
    <SectionHeader
      :title="section.title || sectionModel?.title"
      :is-editing="isEditing"
      :can-edit="canEdit"
      :is-hovered="isHovered"
      @update:title="handleTitleUpdate"
      @toggle-edit="toggleEditMode"
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
    :initial-settings="sectionSettings"
    :initial-data="sectionData"
    :section-id="sectionModel.id"
    :section-title="sectionModel.title || section.title"
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
