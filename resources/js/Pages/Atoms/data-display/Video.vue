<script setup>
/**
 * Video Atom (DaisyUI, Atomic Design)
 * 
 * @description
 * Atom pour afficher un aperçu vidéo avec contrôles de lecture.
 * Utilisé par FilePreview pour l'affichage automatique des vidéos.
 * 
 * @example
 * <Video 
 *   :src="videoUrl"
 *   :name="fileName"
 *   :size="fileSize"
 *   @delete="handleDelete"
 *   :canDelete="true"
 * />
 */
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true,
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

const fileSizeFormatted = computed(() => {
  if (!props.size) return null
  if (props.size < 1024) return `${props.size} B`
  if (props.size < 1024 * 1024) return `${(props.size / 1024).toFixed(2)} KB`
  return `${(props.size / (1024 * 1024)).toFixed(2)} MB`
})

const handleDelete = () => {
  if (props.canDelete) {
    emit('delete')
  }
}
</script>

<template>
  <div class="file-preview-item relative group">
    <div class="relative">
      <video
        :src="src"
        controls
        class="max-w-full h-auto rounded-lg border border-base-300 max-h-64"
      >
        Votre navigateur ne supporte pas la lecture de vidéos.
      </video>
      <div v-if="canDelete" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
        <button
          type="button"
          @click="handleDelete"
          class="btn btn-sm btn-error btn-circle shadow-lg hover:shadow-xl transition-all"
          aria-label="Supprimer le fichier"
          title="Supprimer le fichier"
        >
          <i class="fa-solid fa-trash-can"></i>
        </button>
      </div>
      <div v-if="name || fileSizeFormatted" class="mt-1 text-xs text-content-600">
        <span v-if="name">{{ name }}</span>
        <span v-if="name && fileSizeFormatted"> - </span>
        <span v-if="fileSizeFormatted">{{ fileSizeFormatted }}</span>
      </div>
    </div>
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

