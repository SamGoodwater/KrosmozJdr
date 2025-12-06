<script setup>
/**
 * SectionGalleryEdit Template
 * 
 * @description
 * Template de section pour éditer une galerie en mode écriture.
 * Utilise RichTextEditorField pour l'édition (compatible auto-save).
 */
import { ref, watch } from 'vue';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

// Pour l'instant, on utilise le contenu HTML (comme text)
// TODO: Implémenter un vrai éditeur de galerie
const content = ref(props.data?.content || '');

watch(content, (newContent) => {
  const newData = { ...props.data, content: newContent };
  saveSection(props.section.id, { data: newData });
  emit('data-updated', newData);
}, { deep: true });
</script>

<template>
  <div class="section-gallery-edit">
    <RichTextEditorField
      v-model="content"
      label=""
      :height="'min-h-[300px]'"
    />
    <p class="text-xs text-base-content/50 mt-2 italic">
      Éditeur de galerie à améliorer
    </p>
  </div>
</template>

