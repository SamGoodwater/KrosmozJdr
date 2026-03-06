<script setup>
/**
 * SectionVideoEdit Template
 * 
 * @description
 * Template de section pour éditer une vidéo en mode écriture.
 * - Formulaire d'édition pour les vidéos
 * - Auto-save avec debounce
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 * 
 * @emits data-updated - Émis quand les données sont mises à jour
 */
import { ref, watch } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

const localData = ref({
  src: props.data?.src || '',
  type: props.data?.type || 'youtube'
});
const localSettings = ref({
  autoplay: Boolean(props.settings?.autoplay),
  controls: props.settings?.controls !== false,
  directVideoDisplayMode: String(props.settings?.directVideoDisplayMode || 'preview'),
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

const videoTypeOptions = [
  { value: 'youtube', label: 'YouTube' },
  { value: 'vimeo', label: 'Vimeo' },
  { value: 'direct', label: 'URL directe (MP4, WebM, etc.)' }
];

const directVideoDisplayModeOptions = [
  { value: 'preview', label: 'Lecture dans la page' },
  { value: 'download', label: 'Téléchargement uniquement' },
];

// Synchroniser avec les props
watch(() => props.data, (newData) => {
  if (newData) {
    syncDataFromProps.value = true;
    localData.value = {
      src: newData.src || '',
      type: newData.type || 'youtube'
    };
    lastSavedDataSignature.value = JSON.stringify(localData.value);
    syncDataFromProps.value = false;
  }
}, { deep: true });

watch(
  () => props.settings,
  (newSettings) => {
    syncSettingsFromProps.value = true;
    localSettings.value = {
      autoplay: Boolean(newSettings?.autoplay),
      controls: newSettings?.controls !== false,
      directVideoDisplayMode: String(newSettings?.directVideoDisplayMode || 'preview'),
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
    type: String(newData?.type || 'youtube'),
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
    const normalized = {
      autoplay: Boolean(newSettings?.autoplay),
      controls: Boolean(newSettings?.controls),
      directVideoDisplayMode: String(newSettings?.directVideoDisplayMode || 'preview'),
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
</script>

<template>
  <div class="section-video-edit space-y-4">
    <div class="flex justify-end">
      <InlineSaveStatus :state="saveState" />
    </div>
    <div class="space-y-3">
      <h5 class="font-semibold text-sm">Paramètres de lecture</h5>
      <ToggleField
        v-model="localSettings.autoplay"
        label="Lecture automatique"
        helper="Démarrer la vidéo automatiquement au chargement."
      />
      <ToggleField
        v-model="localSettings.controls"
        label="Afficher les contrôles"
        helper="Afficher les contrôles de lecture (play, pause, volume, etc.)."
      />
      <SelectField
        v-if="localData.type === 'direct'"
        v-model="localSettings.directVideoDisplayMode"
        label="Vidéo directe"
        :options="directVideoDisplayModeOptions"
        helper="Choisir entre lecture intégrée et téléchargement uniquement."
      />
    </div>

    <SelectField
      v-model="localData.type"
      label="Type de vidéo"
      :options="videoTypeOptions"
      helper="Plateforme ou type de vidéo"
    />
    
    <InputField
      v-model="localData.src"
      label="URL ou ID de la vidéo"
      type="text"
      :placeholder="localData.type === 'youtube' ? 'ID YouTube (ex: dQw4w9WgXcQ)' : localData.type === 'vimeo' ? 'ID Vimeo' : 'https://example.com/video.mp4'"
      helper="URL complète ou ID de la vidéo selon le type sélectionné"
    />
    
    <div class="alert alert-info">
      <i class="fa-solid fa-info-circle"></i>
      <div>
        <p class="text-sm">
          Pour YouTube/Vimeo, entrez l'ID ou une URL complète.
          Pour les vidéos directes, entrez l'URL complète.
        </p>
      </div>
    </div>
  </div>
</template>

