<script setup>
/**
 * SectionImageRead Template
 * 
 * @description
 * Template de section pour afficher une image en mode lecture.
 */
import { computed } from 'vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import { useSectionStyles } from '../../composables/useSectionStyles';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const src = computed(() => props.data?.src || '');
const alt = computed(() => props.data?.alt || 'Image');
const caption = computed(() => props.data?.caption || '');

// Utiliser le composable pour les styles
const { alignClasses, imageSizeClasses } = useSectionStyles(() => props.settings);

// Adapter les classes d'alignement pour flexbox
const flexAlignClasses = computed(() => {
  const align = props.settings?.align || 'center';
  return {
    'left': 'justify-start',
    'center': 'justify-center',
    'right': 'justify-end'
  }[align] || 'justify-center';
});
</script>

<template>
  <div class="section-image-content">
    <div class="flex" :class="flexAlignClasses">
      <figure class="w-full" :class="imageSizeClasses">
        <Image
          v-if="src"
          :src="src"
          :alt="alt"
          class="w-full h-auto rounded-lg shadow-lg"
        />
        <div v-else class="text-center text-base-content/50 italic py-8">
          Aucune image
        </div>
        <figcaption 
          v-if="caption"
          class="mt-2 text-sm text-base-content/70 text-center italic"
        >
          {{ caption }}
        </figcaption>
      </figure>
    </div>
  </div>
</template>

<style scoped lang="scss">
.section-image-content {
  figure {
    margin: 0;
  }
}
</style>

