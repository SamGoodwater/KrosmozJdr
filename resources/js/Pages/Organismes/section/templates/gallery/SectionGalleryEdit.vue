<script setup>
/**
 * SectionGalleryEdit Template
 * 
 * @description
 * Éditeur de galerie en mode écriture.
 * Permet d'ajouter/supprimer des images et d'éditer URL/alt/légende.
 */
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();
const isUploading = ref(false);
const uploadError = ref('');
const syncFromProps = ref(false);
const lastEmittedSignature = ref('');
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

const localImages = ref(
  Array.isArray(props.data?.images)
    ? props.data.images.map((img) => ({
        src: String(img?.src || ''),
        alt: String(img?.alt || ''),
        caption: String(img?.caption || ''),
      }))
    : []
);

const normalizeImages = () =>
  (localImages.value || [])
    .map((img) => ({
      src: String(img?.src || '').trim(),
      alt: String(img?.alt || '').trim(),
      caption: String(img?.caption || '').trim(),
    }))
    .filter((img) => img.src !== '');

const stringifyImages = (images) => {
  try {
    return JSON.stringify(images || []);
  } catch {
    return '[]';
  }
};

watch(localImages, () => {
  if (syncFromProps.value) return;
  const normalized = normalizeImages();
  const signature = stringifyImages(normalized);
  if (signature === lastEmittedSignature.value) return;
  lastEmittedSignature.value = signature;
  const newData = { ...props.data, images: normalized };
  saveSection(props.section.id, { data: newData }, {
    onQueued: () => setSaveState('saving'),
    onSuccess: () => setSaveState('saved'),
    onError: () => setSaveState('error'),
  });
  emit('data-updated', newData);
}, { deep: true });

const addImage = () => {
  localImages.value = [...localImages.value, { src: '', alt: '', caption: '' }];
};

const removeImage = (index) => {
  localImages.value = localImages.value.filter((_, i) => i !== index);
};

watch(
  () => props.data,
  (newData) => {
    const incoming = Array.isArray(newData?.images)
      ? newData.images.map((img) => ({
          src: String(img?.src || ''),
          alt: String(img?.alt || ''),
          caption: String(img?.caption || ''),
        }))
      : [];
    const incomingSignature = stringifyImages(incoming);
    const currentSignature = stringifyImages(normalizeImages());
    if (incomingSignature === currentSignature) return;
    syncFromProps.value = true;
    localImages.value = incoming;
    lastEmittedSignature.value = incomingSignature;
    syncFromProps.value = false;
  },
  { deep: true, immediate: true }
);

const uploadFiles = async (event) => {
  const files = Array.from(event?.target?.files || []);
  if (!files.length || !props.section?.id) return;

  isUploading.value = true;
  uploadError.value = '';

  try {
    const uploadedRows = [];
    for (const file of files) {
      const formData = new FormData();
      formData.append('file', file);
      formData.append('title', file.name);
      const response = await axios.post(
        route('sections.files.store', { section: props.section.id }),
        formData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      );
      const uploaded = response?.data?.file || null;
      if (uploaded?.url) {
        uploadedRows.push({
          src: String(uploaded.url),
          alt: String(uploaded.title || file.name || ''),
          caption: '',
        });
      }
    }

    if (uploadedRows.length) {
      localImages.value = [...localImages.value, ...uploadedRows];
    }
  } catch (error) {
    uploadError.value = error?.response?.data?.message || "Erreur lors de l'upload des images.";
  } finally {
    isUploading.value = false;
    if (event?.target) event.target.value = '';
  }
};

const sectionFiles = computed(() => {
  const files = Array.isArray(props.section?.files) ? props.section.files : [];
  return files.filter((file) => {
    const url = String(file?.url || file?.file || '').toLowerCase();
    return /\.(png|jpe?g|gif|webp|avif|bmp|svg)(\?|$)/.test(url);
  });
});

const addSectionFileToGallery = (file) => {
  const src = String(file?.url || file?.file || '').trim();
  if (!src) return;
  const exists = (localImages.value || []).some((img) => String(img?.src || '').trim() === src);
  if (exists) return;
  localImages.value = [
    ...localImages.value,
    {
      src,
      alt: String(file?.title || ''),
      caption: String(file?.description || ''),
    },
  ];
};
</script>

<template>
  <div class="section-gallery-edit space-y-4">
    <div class="flex justify-end">
      <InlineSaveStatus :state="saveState" />
    </div>
    <div class="space-y-2">
      <label class="label">
        <span class="label-text">Uploader des images</span>
      </label>
      <input
        type="file"
        accept="image/*"
        multiple
        class="file-input file-input-bordered w-full"
        :disabled="isUploading"
        @change="uploadFiles"
      />
      <p class="text-xs text-base-content/60">
        Les fichiers sont attachés à la section et ajoutés automatiquement à la galerie.
      </p>
      <p v-if="uploadError" class="text-error text-sm">{{ uploadError }}</p>
    </div>

    <div v-if="sectionFiles.length" class="space-y-2">
      <h5 class="font-semibold text-sm">Fichiers déjà uploadés</h5>
      <div class="space-y-2">
        <div
          v-for="file in sectionFiles"
          :key="file.id"
          class="rounded-lg border border-base-300 p-2 flex items-center justify-between gap-2"
        >
          <div class="min-w-0">
            <p class="text-xs font-medium truncate">{{ file.title || file.url || file.file }}</p>
            <p class="text-xs text-base-content/60 truncate">{{ file.url || file.file }}</p>
          </div>
          <Btn size="xs" variant="ghost" @click="addSectionFileToGallery(file)">
            Ajouter à la galerie
          </Btn>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <h5 class="font-semibold text-sm">Images</h5>
      <Btn size="xs" variant="outline" @click="addImage">
        <Icon source="fa-plus" pack="solid" class="mr-1" />
        Ajouter une image
      </Btn>
    </div>

    <div v-if="!localImages.length" class="text-xs text-base-content/60 italic">
      Aucune image. Ajoute au moins une URL d'image.
    </div>

    <div
      v-for="(image, index) in localImages"
      :key="index"
      class="rounded-lg border border-base-300 p-3 space-y-2"
    >
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-base-content/70">Image {{ index + 1 }}</span>
        <Btn size="xs" variant="ghost" color="error" @click="removeImage(index)">
          Supprimer
        </Btn>
      </div>

      <InputField
        v-model="image.src"
        label="URL"
        placeholder="https://example.com/image.jpg"
      />
      <InputField
        v-model="image.alt"
        label="Texte alternatif"
        placeholder="Description de l'image"
      />
      <InputField
        v-model="image.caption"
        label="Légende (optionnel)"
        placeholder="Légende affichée sous l'image"
      />
    </div>
  </div>
</template>

