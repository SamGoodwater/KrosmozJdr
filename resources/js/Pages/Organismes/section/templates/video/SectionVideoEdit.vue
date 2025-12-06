<script setup>
/**
 * SectionVideoEdit Template
 * 
 * @description
 * Template de section pour éditer une vidéo en mode écriture.
 * Formulaire d'édition pour les vidéos (non compatible auto-save pour l'instant).
 */
import { ref, watch } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['update:data']);

const localData = ref({
  src: props.data.src || '',
  type: props.data.type || 'youtube'
});

const videoTypeOptions = [
  { value: 'youtube', label: 'YouTube' },
  { value: 'vimeo', label: 'Vimeo' },
  { value: 'direct', label: 'URL directe (MP4, WebM, etc.)' }
];

watch(() => props.data, (newData) => {
  localData.value = {
    src: newData.src || '',
    type: newData.type || 'youtube'
  };
}, { deep: true });

watch(localData, (newVal) => {
  emit('update:data', { ...props.data, ...newVal });
}, { deep: true });
</script>

<template>
  <div class="section-video-edit space-y-4">
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
          Pour YouTube/Vimeo, entrez uniquement l'ID de la vidéo.
          Pour les vidéos directes, entrez l'URL complète.
        </p>
      </div>
    </div>
  </div>
</template>

