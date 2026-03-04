<script setup>
/**
 * SectionImageRead Template
 * 
 * @description
 * Template de section pour afficher un média (image/PDF) en mode lecture.
 */
import { computed } from 'vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { SectionStyleService } from '@/Utils/Services';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const inferTypeFromUrl = (url = '') => {
  const normalized = String(url).toLowerCase();
  if (/\.(png|jpe?g|gif|webp|avif|bmp|svg)(\?|$)/.test(normalized)) return 'image';
  if (/\.pdf(\?|$)/.test(normalized)) return 'pdf';
  return 'unknown';
};

const uploadedFile = computed(() => {
  const files = Array.isArray(props.section?.files) ? props.section.files : [];
  return files[0] || null;
});

const src = computed(() => uploadedFile.value?.url || props.data?.src || '');
const alt = computed(() => props.data?.alt || uploadedFile.value?.title || 'Média');
const caption = computed(() => props.data?.caption || '');
const mediaType = computed(() => inferTypeFromUrl(src.value));

// Utiliser le service pour les styles
const imageSizeClasses = computed(() => {
  return SectionStyleService.getImageSizeClasses(props.settings || {});
});

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
    <div class="flex" :class="flexAlignClasses" v-if="src">
      <figure class="w-full" :class="imageSizeClasses">
        <Image
          v-if="mediaType === 'image'"
          :src="src"
          :alt="alt"
          class="w-full h-auto rounded-lg shadow-lg"
        />
        <div v-else-if="mediaType === 'pdf'" class="space-y-2">
          <iframe
            :src="src"
            class="w-full rounded-lg border border-base-300"
            style="min-height: 480px"
            title="Aperçu PDF"
          />
          <div class="flex justify-end">
            <a :href="src" target="_blank" rel="noopener noreferrer">
              <Btn size="sm" variant="outline">Ouvrir le PDF</Btn>
            </a>
          </div>
        </div>
        <div v-else class="text-center text-base-content/70 py-8 border border-base-300 rounded-lg">
          Fichier chargé. <a :href="src" target="_blank" rel="noopener noreferrer" class="link">Ouvrir le document</a>
        </div>
        <figcaption 
          v-if="caption"
          class="mt-2 text-sm text-base-content/70 text-center italic"
        >
          {{ caption }}
        </figcaption>
      </figure>
    </div>
    <div v-else class="text-center text-base-content/50 italic py-8">
      Aucun média
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

