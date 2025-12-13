<script setup>
/**
 * SectionImageEdit Template
 * 
 * @description
 * Template de section pour éditer une image en mode écriture.
 * - Formulaire d'édition pour les images
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
  alt: props.data?.alt || '',
  caption: props.data?.caption || ''
});

// Synchroniser avec les props
watch(() => props.data, (newData) => {
  if (newData) {
    localData.value = {
      src: newData.src || '',
      alt: newData.alt || '',
      caption: newData.caption || ''
    };
  }
}, { deep: true });

// Auto-save avec debounce
watch(localData, (newVal) => {
  const newData = {
    ...props.data,
    ...newVal
  };
  
  // Sauvegarder via le composable
  saveSection(props.section.id, { data: newData });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
}, { deep: true });
</script>

<template>
  <div class="section-image-edit space-y-4">
    <InputField
      v-model="localData.src"
      label="URL de l'image"
      type="url"
      placeholder="https://example.com/image.jpg"
      helper="URL complète de l'image à afficher"
    />
    
    <InputField
      v-model="localData.alt"
      label="Texte alternatif"
      type="text"
      placeholder="Description de l'image"
      helper="Texte affiché si l'image ne peut pas être chargée"
    />
    
    <InputField
      v-model="localData.caption"
      label="Légende"
      type="text"
      placeholder="Légende de l'image"
      helper="Légende affichée sous l'image (optionnel)"
    />
    
    <div class="alert alert-info">
      <i class="fa-solid fa-info-circle"></i>
      <div>
        <p class="text-sm">
          L'édition des images via upload sera disponible prochainement.
          Pour l'instant, utilisez une URL d'image.
        </p>
      </div>
    </div>
  </div>
</template>

