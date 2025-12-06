<script setup>
/**
 * SectionVideoRead Template
 * 
 * @description
 * Template de section pour afficher une vidéo en mode lecture.
 */
import { computed } from 'vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const videoUrl = computed(() => props.data?.url || '');
const caption = computed(() => props.data?.caption || '');
</script>

<template>
  <div class="section-video-content">
    <div v-if="videoUrl" class="aspect-video">
      <iframe
        :src="videoUrl"
        class="w-full h-full rounded-lg"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      ></iframe>
    </div>
    <p v-else class="text-center text-base-content/50 italic py-8">
      Aucune vidéo
    </p>
    <p v-if="caption" class="mt-2 text-sm text-base-content/70 text-center italic">
      {{ caption }}
    </p>
  </div>
</template>

