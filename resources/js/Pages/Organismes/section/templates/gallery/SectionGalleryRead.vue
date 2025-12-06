<script setup>
/**
 * SectionGalleryRead Template
 * 
 * @description
 * Template de section pour afficher une galerie en mode lecture.
 */
import { computed } from 'vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const images = computed(() => props.data?.images || []);
</script>

<template>
  <div class="section-gallery-content">
    <div v-if="images.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="(image, index) in images" :key="index" class="aspect-square">
        <img :src="image.src" :alt="image.alt || 'Image'" class="w-full h-full object-cover rounded-lg" />
      </div>
    </div>
    <p v-else class="text-center text-base-content/50 italic py-8">
      Aucune image dans la galerie
    </p>
  </div>
</template>

<style scoped lang="scss">
.section-gallery-content {
  // Styles spécifiques
}
</style>

