<script setup>
/**
 * FilePreview Atom (DaisyUI, Atomic Design)
 * 
 * @description
 * Atom pour afficher un aperçu de fichier selon son type (image, vidéo, audio, document).
 * Utilise les composants dédiés Image, Video, Audio, Document pour l'affichage.
 * Utilisé par FileField pour l'affichage automatique.
 * 
 * @example
 * <FilePreview 
 *   :file="fileObject"
 *   :url="previewUrl"
 *   type="image"
 *   :name="fileName"
 *   :size="fileSize"
 *   @delete="handleDelete"
 *   :canDelete="true"
 * />
 */
import Image from '@/Pages/Atoms/data-display/Image.vue'
import Video from '@/Pages/Atoms/data-display/Video.vue'
import Audio from '@/Pages/Atoms/data-display/Audio.vue'
import Document from '@/Pages/Atoms/data-display/Document.vue'

const props = defineProps({
  file: {
    type: [Object, String, null],
    default: null,
  },
  url: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    required: true,
    validator: (value) => ['image', 'video', 'audio', 'file', 'unknown'].includes(value),
  },
  name: {
    type: String,
    default: null,
  },
  size: {
    type: Number,
    default: null,
  },
  canDelete: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['delete'])

/**
 * Formate la taille d'un fichier en format lisible
 * @param {Number} size - Taille en octets
 * @returns {String} Taille formatée
 */
const formatFileSize = (size) => {
  if (!size) return null
  if (size < 1024) return `${size} B`
  if (size < 1024 * 1024) return `${(size / 1024).toFixed(2)} KB`
  return `${(size / (1024 * 1024)).toFixed(2)} MB`
}

const handleDelete = () => {
  if (props.canDelete) {
    emit('delete')
  }
}
</script>

<template>
  <div class="file-preview-item relative group">
    <!-- Preview Image -->
    <div v-if="type === 'image'" class="relative">
      <Image
        :src="url"
        :alt="name || 'Image preview'"
        fit="contain"
        rounded="lg"
        class="max-w-full h-auto max-h-64 border border-base-300"
      />
      <div v-if="canDelete" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          type="button"
          @click="handleDelete"
          class="btn btn-sm btn-error btn-circle"
          aria-label="Supprimer le fichier"
        >
          <i class="fa-solid fa-times"></i>
        </button>
      </div>
      <div v-if="name || size" class="mt-1 text-xs text-content-600">
        <span v-if="name">{{ name }}</span>
        <span v-if="name && size"> - </span>
        <span v-if="size">{{ formatFileSize(size) }}</span>
      </div>
    </div>
    
    <!-- Preview Vidéo -->
    <Video
      v-else-if="type === 'video'"
      :src="url"
      :name="name"
      :size="size"
      :canDelete="canDelete"
      @delete="handleDelete"
    />
    
    <!-- Preview Audio -->
    <Audio
      v-else-if="type === 'audio'"
      :src="url"
      :name="name"
      :size="size"
      :canDelete="canDelete"
      @delete="handleDelete"
    />
    
    <!-- Preview Fichier générique -->
    <Document
      v-else
      :name="name"
      :size="size"
      :canDelete="canDelete"
      @delete="handleDelete"
    />
  </div>
</template>

<style scoped lang="scss">
.file-preview-item {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

