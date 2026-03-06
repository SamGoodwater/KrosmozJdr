<script setup>
/**
 * SectionTextEdit Template
 * 
 * @description
 * Template de section pour éditer du texte riche en mode écriture.
 * - Utilise RichTextEditorField (TipTap)
 * - Auto-save avec debounce (500ms)
 * - Synchronisation avec les props
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 * 
 * @emits data-updated - Émis quand les données sont mises à jour
 */
import { computed, ref, watch, onMounted } from 'vue';
import axios from 'axios';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: {
    type: Object,
    required: true
  },
  data: {
    type: Object,
    default: () => ({})
  },
  settings: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['data-updated']);

const { saveSection, saveSectionImmediate } = useSectionSave();

// Contenu local pour l'éditeur (initialisé depuis props)
const content = ref(props.data?.content || '');
const localSettings = ref({
  align: String(props.settings?.align || 'left'),
  size: String(props.settings?.size || 'md'),
});
const syncFromProps = ref(false);
const syncContentFromProps = ref(false);
const lastSavedDataSignature = ref('');
const lastSavedSettingsSignature = ref('');
const saveState = ref('idle'); // idle | saving | saved | error
const lastSavedAt = ref(null);
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

const markSaved = () => {
  lastSavedAt.value = new Date();
  setSaveState('saved');
};

const lastSavedLabel = computed(() => {
  if (!lastSavedAt.value) return '';
  return `Derniere sauvegarde: ${lastSavedAt.value.toLocaleTimeString()}`;
});

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
];

const handleManualSave = () => {
  const sectionId = props.section?.id;
  if (!sectionId) return;

  const normalizedSettings = {
    align: String(localSettings.value?.align || 'left'),
    size: String(localSettings.value?.size || 'md'),
  };
  const newData = {
    ...props.data,
    content: content.value || '',
  };

  lastSavedDataSignature.value = JSON.stringify({ content: newData.content || '' });
  lastSavedSettingsSignature.value = JSON.stringify(normalizedSettings);
  setSaveState('saving');

  saveSectionImmediate(sectionId, {
    data: newData,
    settings: {
      ...props.settings,
      ...normalizedSettings,
    },
  }, {
    onSuccess: () => markSaved(),
    onError: () => setSaveState('error'),
  });

  emit('data-updated', newData);
};

/**
 * Upload un fichier local sur la section courante pour l'insérer dans TipTap.
 *
 * @param {File} file
 * @returns {Promise<{url:string,name:string,mime_type:string}>}
 */
const uploadEditorFile = async (file) => {
  const sectionId = props.section?.id;
  if (!sectionId || !file) {
    throw new Error('Section ou fichier invalide');
  }

  const formData = new FormData();
  formData.append('file', file);
  formData.append('title', file.name || 'Fichier');

  const csrfToken = typeof document !== 'undefined'
    ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    : null;

  const response = await axios.post(
    route('sections.files.store', { section: sectionId }),
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

  const payload = response?.data?.file || {};
  return {
    url: String(payload.url || payload.file || ''),
    name: String(payload.title || file.name || 'Fichier'),
    mime_type: String(file.type || ''),
  };
};

// Synchroniser avec les props (quand les données changent depuis l'extérieur)
watch(() => props.data?.content, (newContent) => {
  if (!syncContentFromProps.value && newContent !== content.value) {
    syncContentFromProps.value = true;
    content.value = newContent || '';
    lastSavedDataSignature.value = JSON.stringify({ content: content.value });
    syncContentFromProps.value = false;
  }
}, { immediate: true });

watch(
  () => props.settings,
  (newSettings) => {
    syncFromProps.value = true;
    localSettings.value = {
      align: String(newSettings?.align || 'left'),
      size: String(newSettings?.size || 'md'),
    };
    lastSavedSettingsSignature.value = JSON.stringify(localSettings.value);
    syncFromProps.value = false;
  },
  { deep: true, immediate: true }
);

// Auto-save avec debounce quand le contenu change
watch(content, (newContent) => {
  if (syncContentFromProps.value) return;
  
  const sectionId = props.section?.id;
  if (!sectionId) return;
  
  const newData = {
    ...props.data,
    content: newContent
  };
  const signature = JSON.stringify({ content: newData.content || '' });
  if (signature === lastSavedDataSignature.value) return;
  lastSavedDataSignature.value = signature;
  
  // Sauvegarder via le composable (avec debounce)
  saveSection(sectionId, { data: newData }, {
    onQueued: () => setSaveState('saving'),
    onSuccess: () => markSaved(),
    onError: () => setSaveState('error'),
  });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
});

watch(
  localSettings,
  (newSettings) => {
    if (syncFromProps.value) return;
    const sectionId = props.section?.id;
    if (!sectionId) return;
    const normalized = {
      align: String(newSettings?.align || 'left'),
      size: String(newSettings?.size || 'md'),
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
      onSuccess: () => markSaved(),
      onError: () => setSaveState('error'),
    });
  },
  { deep: true }
);

// Initialiser le contenu au montage
onMounted(() => {
  if (props.data?.content && !content.value) {
    content.value = props.data.content;
  }
});
</script>

<template>
  <div class="section-text-edit">
    <div class="flex justify-end items-center gap-3 mb-2">
      <span v-if="lastSavedLabel" class="text-xs text-base-content/60">{{ lastSavedLabel }}</span>
      <InlineSaveStatus :state="saveState" />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
      <SelectField
        v-model="localSettings.align"
        label="Alignement"
        :options="alignOptions"
      />
      <SelectField
        v-model="localSettings.size"
        label="Taille du texte"
        :options="sizeOptions"
      />
    </div>
    <RichTextEditorField
      v-model="content"
      label=""
      :height="'min-h-[300px]'"
      :show-save-button="true"
      save-button-label="Enregistrer"
      :upload-file-handler="uploadEditorFile"
      @save-request="handleManualSave"
    />
  </div>
</template>

<style scoped lang="scss">
.section-text-edit {
  // Styles spécifiques pour le mode édition
  width: 100%;
}
</style>

