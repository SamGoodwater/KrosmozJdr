<script setup>
/**
 * SectionImageEdit Template
 * 
 * @description
 * Template de section pour éditer une image en mode écriture.
 * Formulaire d'édition pour les images (non compatible auto-save pour l'instant).
 */
import { ref, watch } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import FileField from '@/Pages/Molecules/data-input/FileField.vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['update:data']);

const localData = ref({
  src: props.data.src || '',
  alt: props.data.alt || '',
  caption: props.data.caption || ''
});

watch(() => props.data, (newData) => {
  localData.value = {
    src: newData.src || '',
    alt: newData.alt || '',
    caption: newData.caption || ''
  };
}, { deep: true });

watch(localData, (newVal) => {
  emit('update:data', { ...props.data, ...newVal });
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

