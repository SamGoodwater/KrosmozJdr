<script setup>
/**
 * SectionGalleryRead Template
 * 
 * @description
 * Template de section pour afficher une galerie en mode lecture.
 */
import { computed } from 'vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import { SectionStyleService } from '@/Utils/Services';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const images = computed(() => props.data?.images || []);

// Utiliser le service pour les styles de galerie
const galleryClasses = computed(() => {
  return SectionStyleService.getGalleryClasses(props.settings || {});
});
</script>

<template>
  <div class="section-gallery-content">
    <div v-if="images.length > 0" :class="galleryClasses">
      <div v-for="(image, index) in images" :key="index" class="aspect-square">
        <Image
          :source="image.src"
          :alt="image.alt || 'Image'"
          fit="cover"
          rounded="lg"
          class="h-full w-full"
        />
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

