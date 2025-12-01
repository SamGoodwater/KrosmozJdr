<script setup>
/**
 * Audio Atom (DaisyUI, Atomic Design)
 * 
 * @description
 * Atom pour afficher un aperçu audio avec contrôles de lecture.
 * Utilisé par FilePreview pour l'affichage automatique des fichiers audio.
 * 
 * @example
 * <Audio 
 *   :src="audioUrl"
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
    <div class="relative p-4 bg-base-200 rounded-lg border border-base-300">
      <div class="flex items-center gap-4">
        <div class="flex-shrink-0">
          <i class="fa-solid fa-music text-3xl text-primary"></i>
        </div>
        <div class="flex-1">
          <audio
            :src="src"
            controls
            class="w-full"
          >
            Votre navigateur ne supporte pas la lecture audio.
          </audio>
          <div v-if="name || fileSizeFormatted" class="mt-1 text-xs text-content-600">
            <span v-if="name">{{ name }}</span>
            <span v-if="name && fileSizeFormatted"> - </span>
            <span v-if="fileSizeFormatted">{{ fileSizeFormatted }}</span>
          </div>
        </div>
        <div v-if="canDelete" class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            type="button"
            @click="handleDelete"
            class="btn btn-sm btn-error btn-circle"
            aria-label="Supprimer le fichier"
          >
            <i class="fa-solid fa-times"></i>
          </button>
        </div>
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

