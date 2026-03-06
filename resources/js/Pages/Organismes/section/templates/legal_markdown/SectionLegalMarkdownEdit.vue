<script setup>
/**
 * SectionLegalMarkdownEdit Template
 *
 * @description
 * Editeur simple pour configurer l'URL du markdown legal et un titre optionnel.
 */
import { ref, watch } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['data-updated']);
const { saveSection } = useSectionSave();

const localData = ref({
  sourceUrl: props.data?.sourceUrl || '/storage/legal/cgu.md',
  title: props.data?.title || '',
});

watch(() => props.data, (newData) => {
  if (!newData) return;
  localData.value = {
    sourceUrl: newData.sourceUrl || '/storage/legal/cgu.md',
    title: newData.title || '',
  };
}, { deep: true });

watch(localData, (newVal) => {
  const newData = {
    ...props.data,
    ...newVal,
  };

  saveSection(props.section.id, { data: newData });
  emit('data-updated', newData);
}, { deep: true });
</script>

<template>
  <div class="section-legal-markdown-edit space-y-4">
    <InputField
      v-model="localData.sourceUrl"
      label="URL du markdown"
      type="text"
      placeholder="/storage/legal/cgu.md"
      helper="Utilise une URL same-origin vers un fichier .md (ex: /storage/legal/politique-donnees.md)"
    />

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
