<script setup>
/**
 * SectionImageEdit Template
 * 
 * @description
 * Template de section pour éditer un média (image/PDF) en mode écriture.
 * - URL manuelle (fallback)
 * - Upload image/PDF via Media Library de la section
 * - Auto-save du contenu avec debounce
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 * 
 * @emits data-updated - Émis quand les données sont mises à jour
 */
import { ref, watch } from 'vue';
import axios from 'axios';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import FilePreview from '@/Pages/Atoms/data-display/FilePreview.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['data-updated']);

const { saveSection, saveSectionImmediate } = useSectionSave();

const localData = ref({
  src: props.data?.src || '',
  alt: props.data?.alt || '',
  caption: props.data?.caption || ''
});
const localFiles = ref(Array.isArray(props.section?.files) ? props.section.files : []);
const isUploading = ref(false);
const uploadError = ref('');

// Synchroniser avec les props
watch(() => props.data, (newData) => {
  if (newData) {
    localData.value = {
      src: newData.src || '',
      alt: newData.alt || '',
      caption: newData.caption || ''
    };
  }
}, { deep: true });
watch(() => props.section?.files, (nextFiles) => {
  localFiles.value = Array.isArray(nextFiles) ? nextFiles : [];
}, { deep: true, immediate: true });

// Auto-save avec debounce
watch(localData, (newVal) => {
  const newData = {
    ...props.data,
    ...newVal
  };
  
  // Sauvegarder via le composable
  saveSection(props.section.id, { data: newData });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
}, { deep: true });

const firstFile = () => {
  return localFiles.value?.[0] || null;
};

const inferTypeFromUrl = (url = '') => {
  const normalized = String(url).toLowerCase();
  if (/\.(png|jpe?g|gif|webp|avif|bmp|svg)(\?|$)/.test(normalized)) return 'image';
  if (/\.pdf(\?|$)/.test(normalized)) return 'file';
  return 'unknown';
};

const currentPreview = () => {
  const file = firstFile();
  if (file?.url) {
    return {
      url: file.url,
      type: inferTypeFromUrl(file.url),
      name: file.title || 'Fichier',
      id: file.id,
    };
  }
  if (localData.value.src) {
    return {
      url: localData.value.src,
      type: inferTypeFromUrl(localData.value.src),
      name: localData.value.alt || 'Média',
      id: null,
    };
  }
  return null;
};

const uploadFile = async (event) => {
  const file = event?.target?.files?.[0];
  if (!file || !props.section?.id) return;

  uploadError.value = '';
  isUploading.value = true;
  try {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('title', file.name);

    const response = await axios.post(route('sections.files.store', { section: props.section.id }), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    const uploaded = response?.data?.file || null;
    if (uploaded) {
      localFiles.value = [uploaded];

      // Garder src/alt synchronisés pour compatibilité lecture.
      const nextData = {
        ...localData.value,
        src: uploaded.url || localData.value.src,
        alt: localData.value.alt || uploaded.title || file.name,
      };
      localData.value = nextData;
      saveSectionImmediate(props.section.id, { data: nextData });
      emit('data-updated', nextData);
    }
  } catch (error) {
    uploadError.value = error?.response?.data?.message || 'Erreur lors de l\'upload du fichier.';
  } finally {
    isUploading.value = false;
    if (event?.target) event.target.value = '';
  }
};

const deleteCurrentFile = async () => {
  const file = firstFile();
  if (!file?.id || !props.section?.id) return;

  uploadError.value = '';
  try {
    await axios.delete(route('sections.files.delete', { section: props.section.id, medium: file.id }));
    localFiles.value = [];

    if (localData.value.src === file.url) {
      const nextData = { ...localData.value, src: '' };
      localData.value = nextData;
      saveSectionImmediate(props.section.id, { data: nextData });
      emit('data-updated', nextData);
    }
  } catch (error) {
    uploadError.value = error?.response?.data?.message || 'Erreur lors de la suppression du fichier.';
  }
};
</script>

<template>
  <div class="section-image-edit space-y-4">
    <div class="space-y-2">
      <label class="label">
        <span class="label-text">Fichier (image ou PDF)</span>
      </label>
      <input
        type="file"
        accept="image/*,.pdf,application/pdf"
        class="file-input file-input-bordered w-full"
        :disabled="isUploading"
        @change="uploadFile"
      />
      <p class="text-xs text-base-content/60">
        Upload direct recommandé. L'URL manuelle ci-dessous reste disponible en fallback.
      </p>
      <p v-if="uploadError" class="text-error text-sm">{{ uploadError }}</p>
    </div>

    <div v-if="currentPreview()" class="space-y-2">
      <FilePreview
        :url="currentPreview().url"
        :type="currentPreview().type"
        :name="currentPreview().name"
        :can-delete="false"
      />
      <Btn
        v-if="currentPreview().id"
        color="error"
        variant="outline"
        size="sm"
        :disabled="isUploading"
        @click="deleteCurrentFile"
      >
        <Icon source="fa-trash-can" pack="solid" class="mr-2" />
        Supprimer le fichier
      </Btn>
    </div>

    <InputField
      v-model="localData.src"
      label="URL du média"
      type="url"
      placeholder="https://example.com/image.jpg ou https://example.com/document.pdf"
      helper="Utilisé si aucun fichier uploadé n'est présent."
    />
    
    <InputField
      v-model="localData.alt"
      label="Texte alternatif / Titre"
      type="text"
      placeholder="Description du média"
      helper="Texte affiché pour l'accessibilité et les aperçus."
    />
    
    <InputField
      v-model="localData.caption"
      label="Légende"
      type="text"
      placeholder="Légende de l'image"
      helper="Légende affichée sous l'image (optionnel)"
    />
    
    <div class="alert alert-info" v-if="isUploading">
      <i class="fa-solid fa-spinner fa-spin"></i>
      <div>
        <p class="text-sm">Upload en cours...</p>
      </div>
    </div>

    <div class="alert alert-info" v-else>
      <i class="fa-solid fa-info-circle"></i>
      <div>
        <p class="text-sm">
          Le template prend en charge les images et les PDF.
        </p>
      </div>
    </div>
  </div>
</template>

