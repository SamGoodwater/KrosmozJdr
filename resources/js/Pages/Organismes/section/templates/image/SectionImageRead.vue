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
  if (!files.length) return null;
  return [...files].sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))[0] || null;
});

const src = computed(() => uploadedFile.value?.url || props.data?.src || '');
const alt = computed(() => props.data?.alt || uploadedFile.value?.title || 'Média');
const caption = computed(() => props.data?.caption || '');
const mediaType = computed(() => inferTypeFromUrl(src.value));
const documentDisplayMode = computed(() => String(props.settings?.documentDisplayMode || 'preview'));
const shouldForceDocumentDownload = computed(() => {
  return mediaType.value !== 'image' && documentDisplayMode.value === 'download';
});
const fileExtension = computed(() => {
  const current = String(src.value || '');
  if (!current) return '';
  const noQuery = current.split('?')[0] || '';
  const ext = noQuery.includes('.') ? noQuery.split('.').pop() : '';
  return String(ext || '').toUpperCase();
});
const fileTypeLabel = computed(() => {
  if (mediaType.value === 'image') return 'Image';
  if (mediaType.value === 'pdf') return 'PDF';
  return fileExtension.value || 'Fichier';
});
const mediaSizeClasses = computed(() => {
  if (mediaType.value === 'image') {
    return imageSizeClasses.value;
  }
  return 'w-full';
});

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
      <figure class="w-full" :class="mediaSizeClasses">
        <Image
          v-if="mediaType === 'image'"
          :src="src"
          :alt="alt"
          class="w-full h-auto rounded-lg shadow-lg"
        />
        <div v-else-if="shouldForceDocumentDownload" class="text-center text-base-content/70 py-8 border border-base-300 rounded-lg space-y-3">
          <div class="inline-flex items-center gap-2 badge badge-soft badge-neutral">
            <i class="fa-solid fa-file-arrow-down"></i>
            <span>{{ fileTypeLabel }}</span>
          </div>
          <p>Aperçu désactivé pour ce type de document.</p>
          <div class="flex justify-center">
            <a :href="src" download>
              <Btn size="sm" variant="outline">Télécharger le fichier</Btn>
            </a>
          </div>
        </div>
        <div v-else-if="mediaType === 'pdf'" class="space-y-2">
          <iframe
            :src="src"
            class="w-full rounded-lg border border-base-300"
            style="min-height: 480px"
            title="Aperçu PDF"
          />
          <div class="flex justify-end">
            <a :href="src" target="_blank" rel="noopener noreferrer" download>
              <Btn size="sm" variant="outline">Ouvrir le PDF</Btn>
            </a>
          </div>
        </div>
        <div v-else class="text-center text-base-content/70 py-8 border border-base-300 rounded-lg space-y-2">
          <div class="inline-flex items-center gap-2 badge badge-soft badge-neutral">
            <i class="fa-solid fa-file-lines"></i>
            <span>{{ fileTypeLabel }}</span>
          </div>
          <p>
            Fichier chargé.
            <a :href="src" target="_blank" rel="noopener noreferrer" download class="link">Télécharger le document</a>
          </p>
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

