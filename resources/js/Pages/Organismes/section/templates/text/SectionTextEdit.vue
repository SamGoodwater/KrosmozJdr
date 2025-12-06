<script setup>
/**
 * SectionTextEdit Template
 * 
 * @description
 * Template de section pour éditer du texte riche en mode écriture.
 * - Utilise RichTextEditorField (TipTap)
 * - Auto-save avec debounce
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 * 
 * @emits data-updated - Émis quand les données sont mises à jour
 */
import { ref, watch, computed } from 'vue';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
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

const { saveSection } = useSectionSave();

// Contenu local pour l'éditeur
const content = ref(props.data?.content || '');

// Synchroniser avec les props
watch(() => props.data?.content, (newContent) => {
  if (newContent !== content.value) {
    content.value = newContent || '';
  }
});

// Auto-save avec debounce
watch(content, (newContent) => {
  const newData = {
    ...props.data,
    content: newContent
  };
  
  // Sauvegarder via le composable
  saveSection(props.section.id, { data: newData });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
}, { deep: true });
</script>

<template>
  <div class="section-text-edit">
    <RichTextEditorField
      v-model="content"
      label=""
      :height="'min-h-[300px]'"
    />
  </div>
</template>

<style scoped lang="scss">
.section-text-edit {
  // Styles spécifiques pour le mode édition si nécessaire
}
</style>

