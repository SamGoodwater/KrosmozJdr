<script setup>
/**
 * SectionImageEdit Template
 * 
 * @description
 * Template de section pour éditer un média (image/PDF/documents/archives) en mode écriture.
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
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import FilePreview from '@/Pages/Atoms/data-display/FilePreview.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
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
const localSettings = ref({
  align: String(props.settings?.align || 'center'),
  size: String(props.settings?.size || 'md'),
  zoom: Number(props.settings?.zoom) || 100,
  lazyLoad: Boolean(props.settings?.lazyLoad),
  documentDisplayMode: String(props.settings?.documentDisplayMode || 'preview'),
});
const syncSettingsFromProps = ref(false);
const syncDataFromProps = ref(false);
const lastSavedDataSignature = ref('');
const lastSavedSettingsSignature = ref('');
const saveState = ref('idle'); // idle | saving | saved | error
let saveStateTimer = null;

const setSaveState = (state) => {
  saveState.value = state;
  if (saveStateTimer) {
    clearTimeout(saveStateTimer);
    saveStateTimer = null;
  }
  if (state === 'saved') {
    saveStateTimer = setTimeout(() => {
      saveState.value = 'idle';
    }, 1600);
  }
};

const alignOptions = [
  { value: 'left', label: 'Gauche' },
  { value: 'center', label: 'Centre' },
  { value: 'right', label: 'Droite' },
];

const sizeOptions = [
  { value: 'sm', label: 'Petit' },
  { value: 'md', label: 'Moyen' },
  { value: 'lg', label: 'Grand' },
  { value: 'xl', label: 'Très grand' },
  { value: 'full', label: 'Pleine largeur' },
];

const documentModeOptions = [
  { value: 'preview', label: 'Aperçu dans le navigateur' },
  { value: 'download', label: 'Téléchargement uniquement' },
];

// Synchroniser avec les props
watch(() => props.data, (newData) => {
  if (newData) {
    syncDataFromProps.value = true;
    localData.value = {
      src: newData.src || '',
      alt: newData.alt || '',
      caption: newData.caption || ''
    };
    lastSavedDataSignature.value = JSON.stringify(localData.value);
    syncDataFromProps.value = false;
  }
}, { deep: true });
watch(() => props.section?.files, (nextFiles) => {
  localFiles.value = Array.isArray(nextFiles) ? nextFiles : [];
}, { deep: true, immediate: true });

watch(
  () => props.settings,
  (nextSettings) => {
    syncSettingsFromProps.value = true;
    localSettings.value = {
      align: String(nextSettings?.align || 'center'),
      size: String(nextSettings?.size || 'md'),
      zoom: Number(nextSettings?.zoom) || 100,
      lazyLoad: Boolean(nextSettings?.lazyLoad),
      documentDisplayMode: String(nextSettings?.documentDisplayMode || 'preview'),
    };
    lastSavedSettingsSignature.value = JSON.stringify(localSettings.value);
    syncSettingsFromProps.value = false;
  },
  { deep: true, immediate: true }
);

// Auto-save avec debounce
watch(localData, (newVal) => {
  if (syncDataFromProps.value) return;
  const newData = {
    ...props.data,
    ...newVal
  };
  const normalized = {
    src: String(newData?.src || ''),
    alt: String(newData?.alt || ''),
    caption: String(newData?.caption || ''),
  };
  const signature = JSON.stringify(normalized);
  if (signature === lastSavedDataSignature.value) return;
  lastSavedDataSignature.value = signature;
  
  // Sauvegarder via le composable
  saveSection(props.section.id, { data: newData }, {
    onQueued: () => setSaveState('saving'),
    onSuccess: () => setSaveState('saved'),
    onError: () => setSaveState('error'),
  });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
}, { deep: true });

watch(
  localSettings,
  (newSettings) => {
    if (syncSettingsFromProps.value) return;
    const sectionId = props.section?.id;
    if (!sectionId) return;
    const zoom = Math.min(500, Math.max(10, Number(newSettings?.zoom) || 100));
    const normalized = {
      align: String(newSettings?.align || 'center'),
      size: String(newSettings?.size || 'md'),
      zoom,
      lazyLoad: Boolean(newSettings?.lazyLoad),
      documentDisplayMode: String(newSettings?.documentDisplayMode || 'preview'),
    };
    const signature = JSON.stringify(normalized);
    if (signature === lastSavedSettingsSignature.value) return;
    lastSavedSettingsSignature.value = signature;

    saveSection(sectionId, {
      settings: {
        ...props.settings,
        ...normalized,
      },
    }, {
      onQueued: () => setSaveState('saving'),
      onSuccess: () => setSaveState('saved'),
      onError: () => setSaveState('error'),
    });
  },
  { deep: true }
);

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
      lastSavedDataSignature.value = JSON.stringify({
        src: String(nextData?.src || ''),
        alt: String(nextData?.alt || ''),
        caption: String(nextData?.caption || ''),
      });
      saveSectionImmediate(props.section.id, { data: nextData }, {
        onSuccess: () => setSaveState('saved'),
        onError: () => setSaveState('error'),
      });
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
      lastSavedDataSignature.value = JSON.stringify({
        src: String(nextData?.src || ''),
        alt: String(nextData?.alt || ''),
        caption: String(nextData?.caption || ''),
      });
      saveSectionImmediate(props.section.id, { data: nextData }, {
        onSuccess: () => setSaveState('saved'),
        onError: () => setSaveState('error'),
      });
      emit('data-updated', nextData);
    }
  } catch (error) {
    uploadError.value = error?.response?.data?.message || 'Erreur lors de la suppression du fichier.';
  }
};
</script>

<template>
  <div class="section-image-edit space-y-4">
    <div class="flex justify-end">
      <InlineSaveStatus :state="saveState" />
    </div>
    <div class="space-y-3">
      <h5 class="font-semibold text-sm">Paramètres d'affichage</h5>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <SelectField
          v-model="localSettings.align"
          label="Alignement"
          :options="alignOptions"
        />
        <SelectField
          v-model="localSettings.size"
          label="Taille"
          :options="sizeOptions"
        />
      </div>
      <InputField
        v-model.number="localSettings.zoom"
        type="number"
        label="Zoom (%)"
        :min="10"
        :max="500"
        helper="Niveau de zoom (10 à 500)."
      />
      <ToggleField
        v-model="localSettings.lazyLoad"
        label="Chargement différé"
        helper="Charger le média uniquement quand il devient visible."
      />
      <SelectField
        v-model="localSettings.documentDisplayMode"
        label="Affichage des documents"
        :options="documentModeOptions"
        helper="Contrôle l'affichage des PDF et autres documents non-image."
      />
    </div>

    <div class="space-y-2">
      <label class="label">
        <span class="label-text">Fichier (image, PDF, Office, texte, archive)</span>
      </label>
      <input
        type="file"
        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z,.odt,.ods,.odp"
        class="file-input file-input-bordered w-full"
        :disabled="isUploading"
        @change="uploadFile"
      />
      <p class="text-xs text-base-content/60">
        Upload direct recommandé. Tu peux remplacer le fichier plus tard depuis cette même section.
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
          Le template prend en charge images, PDF, documents Office, textes et archives.
        </p>
      </div>
    </div>
  </div>
</template>

