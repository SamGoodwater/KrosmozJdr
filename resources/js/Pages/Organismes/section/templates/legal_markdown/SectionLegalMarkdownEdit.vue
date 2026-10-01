<script setup>
/**
 * SectionLegalMarkdownEdit Template
 *
 * @description
 * Pour le journal : édite les fichiers Markdown (frise et version) sur le disque.
 * Pour les pages légales : configure l'URL du document.
 */
import { computed, ref, watch } from 'vue';
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
const saveState = ref('idle');
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
  sourceUrl: props.data?.sourceUrl || '/legal/cgu',
  title: props.data?.title || '',
});

const isChangelog = computed(() => String(localData.value.sourceUrl || '').includes('/changelog/feed/'));

const versionFromUrl = computed(() => {
  const match = String(localData.value.sourceUrl || '').match(/\/changelog\/feed\/(\d+\.\d+\.\d+)/);
  return match ? match[1] : '';
});

const files = ref([]);
const drafts = ref({});
const originals = ref({});
const activeVersion = ref('');
const newVersion = ref('');
const editorError = ref('');
const editorLoading = ref(false);

const versionChoices = computed(() => files.value.filter((file) => /^\d+\.\d+\.\d+$/.test(file.name)));

watch(() => props.data, (newData) => {
  if (!newData) return;
  syncFromProps.value = true;
  localData.value = {
    sourceUrl: newData.sourceUrl || '/legal/cgu',
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
    sourceUrl: String(newData?.sourceUrl || '/legal/cgu'),
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
 * Charge les fichiers markdown du journal (frise, intro, versions).
 *
 * @returns {Promise<void>}
 */
async function loadChangelogFiles() {
  if (!isChangelog.value) return;
  editorLoading.value = true;
  editorError.value = '';
  try {
    const response = await axios.get(route('changelog.sources'), { withCredentials: true });
    const list = Array.isArray(response?.data?.files) ? response.data.files : [];
    files.value = list;
    const nextDrafts = {};
    const nextOriginals = {};
    list.forEach((file) => {
      nextDrafts[file.name] = String(file.markdown || '');
      nextOriginals[file.name] = String(file.markdown || '');
    });
    if (versionFromUrl.value && nextDrafts[versionFromUrl.value] === undefined) {
      nextDrafts[versionFromUrl.value] = '';
      nextOriginals[versionFromUrl.value] = '';
    }
    drafts.value = nextDrafts;
    originals.value = nextOriginals;
    activeVersion.value = versionFromUrl.value || versionChoices.value.at(-1)?.name || '';
  } catch {
    editorError.value = 'Impossible de lire les fichiers du journal.';
  } finally {
    editorLoading.value = false;
  }
}

watch(isChangelog, (enabled) => {
  if (enabled) loadChangelogFiles();
}, { immediate: true });

/**
 * Enregistre la frise et la version ouverte.
 *
 * @returns {Promise<void>}
 */
async function saveChangelog() {
  editorError.value = '';
  const names = ['roadmap', activeVersion.value].filter((name) => name && drafts.value[name] !== originals.value[name]);
  if (names.length === 0) {
    setSaveState('saved');
    return;
  }

  setSaveState('saving');
  try {
    await Promise.all(names.map((name) => axios.put(
      route('changelog.sources.update', { name }),
      { markdown: drafts.value[name] ?? '' },
      { withCredentials: true },
    )));
    names.forEach((name) => {
      originals.value[name] = drafts.value[name] ?? '';
    });
    setSaveState('saved');
  } catch {
    editorError.value = 'Le texte n’a pas pu être enregistré.';
    setSaveState('error');
  }
}

/**
 * Prépare une nouvelle version X.Y.Z, enregistrée au prochain clic.
 */
function addVersion() {
  const version = newVersion.value.trim();
  if (!/^\d+\.\d+\.\d+$/.test(version)) {
    editorError.value = 'Écris une version du type 1.4.0.';
    return;
  }
  editorError.value = '';
  if (drafts.value[version] === undefined) {
    drafts.value[version] = `# Version ${version}\n\n`;
    originals.value[version] = null;
    files.value = [...files.value, { name: version, label: `Version ${version}`, markdown: drafts.value[version] }];
  }
  activeVersion.value = version;
  newVersion.value = '';
}

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
  } catch {
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

    <div v-if="isChangelog" class="space-y-4">
      <p v-if="editorLoading" class="text-sm text-base-content/70">Chargement des textes…</p>
      <template v-else>
        <label class="form-control w-full">
          <span class="label-text mb-1 block font-medium">La suite</span>
          <textarea
            v-model="drafts.roadmap"
            class="textarea textarea-bordered min-h-40 w-full font-mono text-sm"
            spellcheck="true"
          />
          <span class="mt-1 block text-xs text-base-content/70">
            Un titre par étape, par exemple <code>## 1.4 · Prochaine version</code>, puis un court paragraphe.
          </span>
        </label>

        <label class="form-control w-full">
          <span class="label-text mb-1 block font-medium">Version affichée</span>
          <select v-if="versionChoices.length > 1" v-model="activeVersion" class="select select-bordered mb-2 w-full">
            <option v-for="file in versionChoices" :key="file.name" :value="file.name">
              {{ file.label }}
            </option>
          </select>
          <textarea
            v-model="drafts[activeVersion]"
            class="textarea textarea-bordered min-h-64 w-full font-mono text-sm"
            spellcheck="true"
          />
          <span class="mt-1 block text-xs text-base-content/70">
            Ce qui a changé pour les joueurs et les meneurs. Pas de détail technique.
          </span>
        </label>

        <div class="flex flex-col gap-2 sm:flex-row">
          <input
            v-model="newVersion"
            type="text"
            class="input input-bordered w-full sm:max-w-40"
            placeholder="1.4.0"
            aria-label="Nouvelle version"
          />
          <button type="button" class="btn btn-ghost" @click="addVersion">
            Ajouter une version
          </button>
        </div>

        <button type="button" class="btn btn-primary" @click="saveChangelog">
          Enregistrer le journal
        </button>
      </template>
      <p v-if="editorError" class="text-sm text-error">{{ editorError }}</p>
    </div>

    <InputField
      v-model="localData.sourceUrl"
      label="URL du markdown"
      type="text"
      placeholder="/legal/cgu"
      helper="Page légale : /legal/… Le journal : /changelog/feed/X.Y.Z. Le texte du journal s’enregistre dans les fichiers, pas dans la base."
    />

    <div v-if="!isChangelog" class="space-y-2">
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
      helper="Titre affiche au-dessus du document. Laisser vide sur le journal : le texte markdown porte déjà son titre."
    />
  </div>
</template>
