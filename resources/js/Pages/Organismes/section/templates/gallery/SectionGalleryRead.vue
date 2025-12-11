<script setup>
/**
 * SectionGalleryRead Template
 * 
 * @description
 * Template de section pour afficher une galerie en mode lecture.
 */
import { computed } from 'vue';
import { useSectionStyles } from '../../composables/useSectionStyles';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const images = computed(() => props.data?.images || []);

// Utiliser le composable pour les styles de galerie
const { galleryClasses } = useSectionStyles(() => props.settings);
</script>

<template>
  <div class="section-gallery-content">
    <div v-if="images.length > 0" :class="galleryClasses">
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

