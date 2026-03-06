<script setup>
/**
 * SectionLegalMarkdownEdit Template
 *
 * @description
 * Editeur simple pour configurer l'URL du markdown legal et un titre optionnel.
 */
import { ref, watch } from 'vue';
import axios from 'axios';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['data-updated']);
const { saveSection } = useSectionSave();
const syncFromProps = ref(false);
const lastSavedSignature = ref('');
const saveState = ref('idle'); // idle | saving | saved | error
const isUploading = ref(false);
const uploadError = ref('');
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

const localData = ref({
  sourceUrl: props.data?.sourceUrl || '/storage/legal/cgu.md',
  title: props.data?.title || '',
});

watch(() => props.data, (newData) => {
  if (!newData) return;
  syncFromProps.value = true;
  localData.value = {
    sourceUrl: newData.sourceUrl || '/storage/legal/cgu.md',
    title: newData.title || '',
  };
  lastSavedSignature.value = JSON.stringify({
    sourceUrl: localData.value.sourceUrl,
    title: localData.value.title,
  });
  syncFromProps.value = false;
}, { deep: true });

watch(localData, (newVal) => {
  if (syncFromProps.value) return;
  const newData = {
    ...props.data,
    ...newVal,
  };
  const signature = JSON.stringify({
    sourceUrl: String(newData?.sourceUrl || '/storage/legal/cgu.md'),
    title: String(newData?.title || ''),
  });
  if (signature === lastSavedSignature.value) return;
  lastSavedSignature.value = signature;

  saveSection(props.section.id, { data: newData }, {
    onQueued: () => setSaveState('saving'),
    onSuccess: () => setSaveState('saved'),
    onError: () => setSaveState('error'),
  });
  emit('data-updated', newData);
}, { deep: true });

/**
 * Upload un fichier markdown/texte et mappe automatiquement l'URL dans sourceUrl.
 *
 * @param {Event} event
 * @returns {Promise<void>}
 */
const handleFileUpload = async (event) => {
  const file = event?.target?.files?.[0];
  if (!file || !props.section?.id) return;

  uploadError.value = '';
  isUploading.value = true;
  try {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('title', file.name || 'Document legal');

    const csrfToken = typeof document !== 'undefined'
      ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      : null;

    const response = await axios.post(
      route('sections.files.store', { section: props.section.id }),
      formData,
      {
        withCredentials: true,
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        },
      }
    );

    const uploadedUrl = String(response?.data?.file?.url || response?.data?.file?.file || '');
    if (!uploadedUrl) {
      uploadError.value = "Upload réussi, mais l'URL du fichier est introuvable.";
      return;
    }

    localData.value.sourceUrl = uploadedUrl;
    if (!localData.value.title) {
      localData.value.title = String(file.name || '').replace(/\.[^.]+$/, '');
    }
  } catch (error) {
    uploadError.value = "Impossible d'uploader le fichier.";
  } finally {
    isUploading.value = false;
    if (event?.target) {
      event.target.value = '';
    }
  }
};
</script>

<template>
  <div class="section-legal-markdown-edit space-y-4">
    <div class="flex justify-end">
      <InlineSaveStatus :state="saveState" />
    </div>
    <InputField
      v-model="localData.sourceUrl"
      label="URL du markdown"
      type="text"
      placeholder="/storage/legal/cgu.md"
      helper="Utilise une URL same-origin vers un fichier .md (ex: /storage/legal/politique-donnees.md)"
    />

    <div class="space-y-2">
      <label class="label">
        <span class="label-text">Uploader un fichier markdown/texte</span>
      </label>
      <input
        type="file"
        accept=".md,.markdown,.txt,text/markdown,text/plain"
        class="file-input file-input-bordered w-full"
        :disabled="isUploading"
        @change="handleFileUpload"
      />
      <p class="text-xs text-base-content/70">
        Le fichier est attaché à la section, puis son URL est renseignée automatiquement.
      </p>
      <p v-if="uploadError" class="text-error text-sm">{{ uploadError }}</p>
    </div>

    <InputField
      v-model="localData.title"
      label="Titre (optionnel)"
      type="text"
      placeholder="Conditions Generales d'Utilisation"
      helper="Titre affiche au-dessus du document."
    />

    <div class="alert alert-info">
      <i class="fa-solid fa-circle-info"></i>
      <span>Le rendu markdown est sanitise cote client avant affichage.</span>
    </div>
  </div>
</template>
