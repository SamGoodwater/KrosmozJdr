<script setup>
/**
 * SectionImageRead Template
 * 
 * @description
 * Template de section pour afficher une image en mode lecture.
 */
import { computed } from 'vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const src = computed(() => props.data?.src || '');
const alt = computed(() => props.data?.alt || 'Image');
const caption = computed(() => props.data?.caption || '');

const alignClasses = computed(() => {
  const align = props.settings?.align || 'center';
  return {
    'left': 'justify-start',
    'center': 'justify-center',
    'right': 'justify-end'
  }[align] || 'justify-center';
});

const sizeClasses = computed(() => {
  const size = props.settings?.size || 'md';
  return {
    'sm': 'max-w-sm',
    'md': 'max-w-md',
    'lg': 'max-w-lg',
    'xl': 'max-w-xl',
    'full': 'max-w-full'
  }[size] || 'max-w-md';
});
</script>

<template>
  <div class="section-image-content">
    <div class="flex" :class="alignClasses">
      <figure class="w-full" :class="sizeClasses">
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

