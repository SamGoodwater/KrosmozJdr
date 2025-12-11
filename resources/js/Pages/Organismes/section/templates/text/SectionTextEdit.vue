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
import { ref, watch, onMounted } from 'vue';
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

// Contenu local pour l'éditeur (initialisé depuis props)
const content = ref(props.data?.content || '');

// Flag pour éviter les boucles de synchronisation
let isUpdatingFromProps = false;

// Synchroniser avec les props (quand les données changent depuis l'extérieur)
watch(() => props.data?.content, (newContent) => {
  if (!isUpdatingFromProps && newContent !== content.value) {
    content.value = newContent || '';
  }
}, { immediate: true });

// Auto-save avec debounce quand le contenu change
watch(content, (newContent) => {
  if (isUpdatingFromProps) return;
  
  const sectionId = props.section?.id;
  if (!sectionId) return;
  
  const newData = {
    ...props.data,
    content: newContent
  };
  
  // Sauvegarder via le composable (avec debounce)
  saveSection(sectionId, { data: newData });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
});

// Initialiser le contenu au montage
onMounted(() => {
  if (props.data?.content && !content.value) {
    content.value = props.data.content;
  }
});
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
  // Styles spécifiques pour le mode édition
  width: 100%;
}
</style>

